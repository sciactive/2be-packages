<?php
/**
 * com_authorizenet class.
 *
 * @package Pines
 * @subpackage com_authorizenet
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Zak Huber <zakhuber@gmail.com>
 * @copyright Zak Huber
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_authorizenet main class.
 *
 * Process sales through Authorize.Net.
 *
 * @package Pines
 * @subpackage com_authorizenet
 */
class com_authorizenet extends component {
	/**
	 * Process a payment.
	 *
	 * @param array $args The argument array.
	 */
	function payment_credit($args) {
		global $config, $page;
		switch ($args['action']) {
			case 'request':
				$module = new module('com_authorizenet', 'form_payment');
				if ($args['sale']->customer->guid) {
					$module->name_first = $args['sale']->customer->name_first;
					$module->name_last = $args['sale']->customer->name_last;
					$module->address = $args['sale']->customer->address_1;
					$module->state = $args['sale']->customer->state;
					$module->zip = $args['sale']->customer->zip;
				}
				$page->override_doc($module->render());
				break;
			case 'approve':
				$args['payment']['status'] = 'approved';
				if (empty($args['payment']['data']['name_first']) ||
					empty($args['payment']['data']['name_last']) ||
					empty($args['payment']['data']['address']) ||
					empty($args['payment']['data']['state']) ||
					empty($args['payment']['data']['zip']) ||
					empty($args['payment']['data']['card_number']) ||
					empty($args['payment']['data']['card_exp_month']) ||
					empty($args['payment']['data']['card_exp_year']) ||
					empty($args['payment']['data']['cid']))
					$args['payment']['status'] = 'info_requested';
				break;
			case 'tender':
				$firstname = $args['payment']['data']['name_first'];
				$lastname = $args['payment']['data']['name_last'];
				$amt = (float) $args['payment']['amount'];
				$card_num = $args['payment']['data']['card_number'];
				$exp_date = $args['payment']['data']['card_exp_month'].$args['payment']['data']['card_exp_year'];
				$address = $args['payment']['data']['address'];
				$state = $args['payment']['data']['state'];
				$zip = $args['payment']['data']['zip'];

				$post_values = array(
					// the API Login ID and Transaction Key must be replaced with valid values
					'x_login'			=> $config->com_authorizenet->apilogin,
					'x_tran_key'		=> $config->com_authorizenet->tran_key,
					'x_test_request'	=> ($config->com_authorizenet->test_mode) ? 'TRUE' : 'FALSE',

					'x_version'			=> '3.1',
					'x_delim_data'		=> 'TRUE',
					'x_delim_char'		=> '|',
					'x_relay_response'	=> 'FALSE',

					'x_type'			=> 'AUTH_CAPTURE',
					'x_method'			=> 'CC',
					'x_card_num'		=> $card_num,
					'x_exp_date'		=> $exp_date,

					'x_amount'			=> $amt,
					'x_description'		=> $transaction_name,

					'x_first_name'		=> $firstname,
					'x_last_name'		=> $lastname,
					'x_address'			=> $address,
					'x_state'			=> $state,
					'x_zip'				=> $zip
				);
				$post_string = "";
				foreach ($post_values as $key => $value) {
					$post_string .= "$key=" . urlencode($value) . "&";
				}
				$post_string = rtrim($post_string, "& ");

				$request = curl_init($config->com_authorizenet->post_url);
				curl_setopt($request, CURLOPT_HEADER, 0);
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
				$post_response = curl_exec($request);
				curl_close($request);

				if ($post_response === false) {
					display_error('Credit processing gateway cannot be reached. Please try again, and if the problem persists, please contact an administrator.');
					break;
				}

				$response_array = explode($post_values["x_delim_char"],$post_response);
				$args['payment']['com_authorize_credit_info'] = array_merge((array) $args['payment']['data']);
				switch ($response_array[0]) {
					case 1:
						$args['payment']['status'] = 'tendered';
						unset($args['payment']['data']['name_first']);
						unset($args['payment']['data']['name_last']);
						unset($args['payment']['data']['address']);
						unset($args['payment']['data']['state']);
						unset($args['payment']['data']['zip']);
						unset($args['payment']['data']['card_number']);
						unset($args['payment']['data']['card_exp_month']);
						unset($args['payment']['data']['card_exp_year']);
						unset($args['payment']['data']['cid']);
						break;
					case 2:
						$args['payment']['status'] = 'declined';
						unset($args['payment']['data']['name_first']);
						unset($args['payment']['data']['name_last']);
						unset($args['payment']['data']['address']);
						unset($args['payment']['data']['state']);
						unset($args['payment']['data']['zip']);
						unset($args['payment']['data']['card_number']);
						unset($args['payment']['data']['card_exp_month']);
						unset($args['payment']['data']['card_exp_year']);
						unset($args['payment']['data']['cid']);
						break;
					case 3:
						$args['payment']['status'] = 'info_requested';
						break;
					case 4:
						display_notice('Payment is held for review.');
						break;
					default:
						$args['payment']['status'] = 'pending';
						display_error('Credit processing failed. Please try again, and if the problem persists, please contact an administrator.');
						break;
				}
				display_notice($response_array[3]);
				break;
		}
	}
}

?>