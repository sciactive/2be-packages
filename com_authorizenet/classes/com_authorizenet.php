<?php
/**
 * com_authorizenet class.
 *
 * @package Components\authorizenet
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_authorizenet main class.
 *
 * Process sales through Authorize.Net.
 *
 * @package Components\authorizenet
 */
class com_authorizenet extends component {
	/**
	 * Determine the type of credit card that is being charged.
	 *
	 * @param string $card_num The credit card number.
	 * @return string The credit card type.
	 */
	function card_type($card_num) {
		$prefix = substr($card_num, 0, 1);
		if ($prefix == '4') {
			return 'VISA';
		} else {
			$prefix = substr($card_num, 0, 2);
			if ($prefix == '30' || $prefix == '36' || $prefix == '38') {
				return 'DC';
			} elseif ($prefix == '34' || $prefix == '37') {
				return 'AMEX';
			} elseif ($prefix == '35') {
				return 'JCB';
			} elseif ($prefix == '51' || $prefix == '52' || $prefix == '53' || $prefix == '54' || $prefix == '55') {
				return 'MC';
			} elseif ($prefix == '64' || $prefix == '65') {
				return 'DISC';
			} else {
				$prefix = substr($card_num, 0, 4);
				if ($prefix == '6011') {
					return 'DISC';
				} else if ($prefix == '5610' || $prefix == '5602') {
					return 'BC';
				}
			}
		}
		return 'OTHER';
	}
	
	/**
	 * Process a payment.
	 *
	 * @param array &$array The argument array.
	 * @return module|null If action is "request", a form module is returned.
	 */
	function payment_credit(&$array) {
		global $_;
		switch ($array['action']) {
			case 'request':
				$module = new module('com_authorizenet', 'form_payment');
				if ($array['ticket']->customer->guid) {
					$module->name_first = $array['ticket']->customer->name_first;
					$module->name_last = $array['ticket']->customer->name_last;
					$module->address = $array['ticket']->customer->address_1;
					$module->state = $array['ticket']->customer->state;
					$module->zip = $array['ticket']->customer->zip;
				}
				return $module;
				break;
			case 'request_cust':
				$module = new module('com_authorizenet', 'form_payment_cust');
				if ($array['ticket']->customer->guid) {
					$module->name_last = $array['ticket']->customer->name;
					$module->address = $array['ticket']->customer->address_1;
					$module->state = $array['ticket']->customer->state;
					$module->zip = $array['ticket']->customer->zip;
				}
				return $module;
				break;
			case 'approve':
				$array['payment']['status'] = 'approved';
				if ($array['type'] == 'return') {
					if (
							(empty($array['payment']['data']['name_last']) || empty($array['payment']['data']['card_number']))
							&&
							(empty($array['payment']['com_authorizenet_credit_info']['name_last']) || empty($array['payment']['com_authorizenet_credit_info']['card_number']))
						) {
						pines_notice('Please swipe or enter the card used in the original transaction.');
						$array['payment']['status'] = 'info_requested';
					}
				} else {
					if ($array['payment']['data']['card_swiped'] == 'ON') {
						if (empty($array['payment']['data']['name_last']) ||
							empty($array['payment']['data']['card_number']) ||
							empty($array['payment']['data']['card_exp_month']) ||
							empty($array['payment']['data']['card_exp_year']))
							$array['payment']['status'] = 'info_requested';
					} else {
						if (empty($array['payment']['data']['name_last']) ||
							empty($array['payment']['data']['card_number']) ||
							empty($array['payment']['data']['card_exp_month']) ||
							empty($array['payment']['data']['card_exp_year']) ||
							empty($array['payment']['data']['cid']))
							$array['payment']['status'] = 'info_requested';
					}
				}
				break;
			case 'tender':
				$firstname = $array['payment']['data']['name_first'];
				$lastname = $array['payment']['data']['name_last'];
				$amt = $_->com_sales->round((float) $array['payment']['amount']);
				$card_num = $array['payment']['data']['card_number'];
				$exp_date = $array['payment']['data']['card_exp_month'].$array['payment']['data']['card_exp_year'];
				//$address = $args['payment']['data']['address'];
				//$state = $args['payment']['data']['state'];
				//$zip = $args['payment']['data']['zip'];
				$card_code = $array['payment']['data']['cid'];
				$invoice_num = ($array['ticket']->has_tag('sale') ? 'SA' : 'RE').$array['ticket']->id;
				$transaction_name = $_->config->com_authorizenet->trans_name;

				$post_values = array(
					// the API Login ID and Transaction Key must be replaced with valid values
					'x_login'			=> $_->config->com_authorizenet->apilogin,
					'x_tran_key'		=> $_->config->com_authorizenet->tran_key,
					'x_test_request'	=> ($_->config->com_authorizenet->test_mode) ? 'TRUE' : 'FALSE',

					'x_version'			=> '3.1',
					'x_delim_data'		=> 'TRUE',
					'x_delim_char'		=> '|',
					'x_relay_response'	=> 'FALSE',

					'x_type'			=> 'AUTH_CAPTURE',
					'x_method'			=> 'CC',
					'x_card_num'		=> $card_num,
					'x_exp_date'		=> $exp_date,

					'x_amount'			=> $amt,
					'x_invoice_num'		=> $invoice_num,
					'x_description'		=> $transaction_name,

					'x_first_name'		=> $firstname,
					'x_last_name'		=> $lastname,
					'x_address'			=> '', //$address,
					'x_state'			=> '', //$state,
					'x_zip'				=> '' //$zip
				);

				// If the card is a merchant card and not an online account.
				if ($_->config->com_authorizenet->merchant_type == 'retail') {
					$post_values['x_cpversion'] = '1.0';
					$post_values['x_market_type'] = '2';
					$post_values['x_device_type'] = '5';
					$post_values['x_response_format'] = '1';
				}
				if ($array['payment']['data']['card_swiped'] != 'ON')
					$post_values['x_card_code'] = $card_code;
				$post_string = '';
				foreach ($post_values as $key => $value) {
					$post_string .= "$key=" . urlencode($value) . '&';
				}
				$post_string = rtrim($post_string, '& ');

				$request = curl_init($_->config->com_authorizenet->post_url);
				curl_setopt($request, CURLOPT_HEADER, 0);
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
				$post_response = curl_exec($request);
				curl_close($request);

				if ($post_response === false) {
					pines_error('Credit processing gateway cannot be reached. Please try again, and if the problem persists, please contact an administrator.');
					break;
				}

				$response_array = explode($post_values['x_delim_char'],$post_response);
				$array['payment']['com_authorizenet_credit_info'] = array(
					'name_first'		=> $array['payment']['data']['name_first'],
					'name_last'			=> $array['payment']['data']['name_last'],
					//'address'			=> $args['payment']['data']['address'],
					//'state'			=> $args['payment']['data']['state'],
					//'zip'				=> $args['payment']['data']['zip'],
					'card_type'			=> $this->card_type($array['payment']['data']['card_number']),
					'card_number'		=> substr($array['payment']['data']['card_number'], -4),
					'card_exp_month'	=> $array['payment']['data']['card_exp_month'],
					'card_exp_year'		=> $array['payment']['data']['card_exp_year'],
					'card_swiped'		=> $array['payment']['data']['card_swiped']
				);
				if ($_->config->com_authorizenet->merchant_type == 'retail') {
					$response_code = $response_array[1];
				} else {
					$response_code = $response_array[0];
				}
				switch ($response_code) {
					case 1:
						$array['payment']['status'] = 'tendered';
						$array['payment']['label'] = $this->card_type($array['payment']['data']['card_number']) . ' ' . substr($array['payment']['data']['card_number'], -4);
						unset($array['payment']['data']['name_first']);
						unset($array['payment']['data']['name_last']);
						//unset($args['payment']['data']['address']);
						//unset($args['payment']['data']['state']);
						//unset($args['payment']['data']['zip']);
						unset($array['payment']['data']['card_number']);
						unset($array['payment']['data']['card_exp_month']);
						unset($array['payment']['data']['card_exp_year']);
						unset($array['payment']['data']['cid']);
						break;
					case 2:
						$array['payment']['status'] = 'declined';
						$array['payment']['label'] = $this->card_type($array['payment']['data']['card_number']) . ' ' . substr($array['payment']['data']['card_number'], -4);
						unset($array['payment']['data']['name_first']);
						unset($array['payment']['data']['name_last']);
						//unset($args['payment']['data']['address']);
						//unset($args['payment']['data']['state']);
						//unset($args['payment']['data']['zip']);
						unset($array['payment']['data']['card_number']);
						unset($array['payment']['data']['card_exp_month']);
						unset($array['payment']['data']['card_exp_year']);
						unset($array['payment']['data']['cid']);
						break;
					case 3:
						$array['payment']['status'] = 'info_requested';
						break;
					case 4:
						pines_notice('Payment is held for review.');
						break;
					default:
						$array['payment']['status'] = 'pending';
						pines_error('Credit processing failed. Please try again, and if the problem persists, please contact an administrator.');
						break;
				}
				$array['payment']['com_authorizenet_credit_info']['transaction_id'] = $response_array[6];
				pines_notice($response_array[3]);
				break;
			case 'void':
				$post_values = array(
					// the API Login ID and Transaction Key must be replaced with valid values
					'x_login'			=> $_->config->com_authorizenet->apilogin,
					'x_tran_key'		=> $_->config->com_authorizenet->tran_key,
					'x_test_request'	=> ($_->config->com_authorizenet->test_mode) ? 'TRUE' : 'FALSE',

					'x_version'			=> '3.1',
					'x_delim_data'		=> 'TRUE',
					'x_delim_char'		=> '|',
					'x_relay_response'	=> 'FALSE',

					'x_type'			=> 'VOID',
					'x_trans_id'		=> $array['payment']['com_authorizenet_credit_info']['transaction_id']
				);
				
				$post_string = '';
				foreach ($post_values as $key => $value) {
					$post_string .= "$key=" . urlencode($value) . '&';
				}
				$post_string = rtrim($post_string, '& ');

				$request = curl_init($_->config->com_authorizenet->post_url);
				curl_setopt($request, CURLOPT_HEADER, 0);
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
				$post_response = curl_exec($request);
				curl_close($request);

				if ($post_response === false) {
					pines_error('Credit processing gateway cannot be reached. Please try again, and if the problem persists, please contact an administrator.');
					break;
				}

				$response_array = explode($post_values['x_delim_char'], $post_response);
				switch ($response_array[0]) {
					case 1:
						$array['payment']['status'] = 'voided';
						break;
					case 2:
						pines_notice('Payment void was declined.');
						break;
					case 3:
						pines_notice('Payment void required more information.');
						break;
					case 4:
						pines_notice('Payment void is being held for review.');
						break;
					default:
						pines_notice('Void processing failed. Please try again, and if the problem persists, please contact an administrator.');
						break;
				}
				pines_notice($response_array[3]);
				break;
			case 'return':
				$amt = $_->com_sales->round((float) $array['payment']['amount'], true);
				$card_num = !empty($array['payment']['data']['card_number']) ? $array['payment']['data']['card_number'] : $array['payment']['com_authorizenet_credit_info']['card_number'];
				$exp_date = !empty($array['payment']['data']['card_exp_month']) ? $array['payment']['data']['card_exp_month'].$array['payment']['data']['card_exp_year'] : $array['payment']['com_authorizenet_credit_info']['card_exp_month'].$array['payment']['com_authorizenet_credit_info']['card_exp_year'];
				$transaction_id = $array['payment']['com_authorizenet_credit_info']['transaction_id'];
				$card_type = $array['payment']['com_authorizenet_credit_info']['card_type'];
				$transaction_name = 'RETURN: '.$_->config->com_authorizenet->trans_name;

				$post_values = array(
					// The API Login ID and Transaction Key must be replaced with valid values
					'x_login'			=> $_->config->com_authorizenet->apilogin,
					'x_tran_key'		=> $_->config->com_authorizenet->tran_key,
					'x_test_request'	=> ($_->config->com_authorizenet->test_mode) ? 'TRUE' : 'FALSE',

					'x_version'			=> '3.1',
					'x_delim_data'		=> 'TRUE',
					'x_delim_char'		=> '|',
					'x_relay_response'	=> 'FALSE',

					'x_type'			=> 'CREDIT',
					'x_card_num'		=> $card_num,
					'x_exp_date'		=> $exp_date,
					'x_trans_id'		=> $transaction_id,
					'x_description'		=> $transaction_name,
					'x_amount'			=> $amt
				);

				$post_string = '';
				foreach ($post_values as $key => $value) {
					$post_string .= "$key=" . urlencode($value) . '&';
				}
				$post_string = rtrim($post_string, '& ');

				$request = curl_init($_->config->com_authorizenet->post_url);
				curl_setopt($request, CURLOPT_HEADER, 0);
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
				$post_response = curl_exec($request);
				curl_close($request);

				if ($post_response === false) {
					pines_error('Credit processing gateway cannot be reached. Please try again, and if the problem persists, please contact an administrator.');
					break;
				}

				$response_array = explode($post_values['x_delim_char'],$post_response);
				switch ($response_array[0]) {
					case 1:
						$array['payment']['status'] = 'tendered';
						if (!empty($card_type))
							$array['payment']['label'] = $card_type . ' ' . substr($card_num, -4);
						else
							$array['payment']['label'] = $this->card_type($card_num) . ' ' . substr($card_num, -4);
						break;
					case 2:
						pines_notice('Payment refund was declined.');
						$array['payment']['status'] = 'declined';
						if (!empty($card_type))
							$array['payment']['label'] = $card_type . ' ' . substr($card_num, -4);
						else
							$array['payment']['label'] = $this->card_type($card_num) . ' ' . substr($card_num, -4);
						break;
					case 3:
						if ($response_array[2] == 54) {
							pines_notice('Payment appears to be unsettled. Refund cannot be processed until the payment has been settled. Please try again tomorrow.');
						} else {
							pines_notice('Payment refund required more information.');
						}
						break;
					case 4:
						pines_notice('Payment refund is being held for review.');
						break;
					default:
						pines_notice('Return processing failed. Please try again, and if the problem persists, please contact an administrator.');
						break;
				}
				pines_notice($response_array[3]);
				break;
		}
	}
}