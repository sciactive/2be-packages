<?php
/**
 * The module for viewing approved testimonials.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
$customer = (isset($_SESSION['user']) && $_SESSION['user']->hasTag('customer'));

// get a testimonial to have loaded
$aggregate = $_->com_testimonials->get_testimonials('aggregate', true, 20, 0, array('rated'));
$testimonials = $_->com_testimonials->get_testimonials('individual', true, 20, 0, array('approved', 'share'), null, null, null, false, true);
if (is_array($testimonials)) {
	$testimonial = $testimonials[0];
}
$_->com_timeago->load();
$_->com_testimonials->load();
?>

<div id="p_muid_testimonials" class="col-sm-12 testimonials-module">
	<div class="row">
		<div class="col-sm-12">
			<div class="frame">
				<div class="no-average-rating clearfix <?php echo (is_array($aggregate)) ? 'hide': ''; ?>">Reviews</div>
				<div class="average-rating clearfix <?php echo (is_array($aggregate)) ? '': 'hide'; ?>" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
					<span class="star-rating" data-original-title="<?php echo (is_array($aggregate)) ? (round($aggregate['average'], 2).' / 5') : ''; ?>">
					<meta itemprop="ratingValue" content="<?php echo (is_array($aggregate)) ? round($aggregate['average'], 2) : ''; ?>"/>
					<?php 
					// Figure out how many stars to show
					$rating = (is_array($aggregate)) ? (float) $aggregate['average'] : 0;
					$star_rating = round($rating);
					for ($c = 1; $c <= 5; $c++) { 
						if ((int) $star_rating >= $c) { ?>
						<i class="fa fa-star"></i>
					<?php } else { ?>
						<i class="fa fa-star-o"></i>
					<?php } 
					} ?>
					</span>
					<span class="pull-right votes"><?php echo (is_array($aggregate)) ? '<span itemprop="reviewCount">'.$aggregate['votes'] : ''; echo ((is_array($aggregate)) ? ((((int) $aggregate['votes']) > 1) ? '</span> Reviews' : '</span> Review') : ''); ?></span>
				</div>
				<div class="testimonials">
					<div class="list-read-more">Load More</div>
					<div class="list-up"><i class="fa fa-caret-up"></i></div>
					<div class="list-top"><i class="fa fa-step-backward"></i> Top</div>
					<div class="testimonial-list-container">
						<div class="list-placeholder" style="padding: 20px; text-align: center; text-shadow: 1px 1px #fff; color: #aaa;">Scroll Down to Load More</div>
					</div>
					<div class="testimonial-loader"><i class="fa fa-spinner fa-spin"></i><p>Loading</p></div>
					<div class="testimonial loaded-testimonial" itemprop="review" itemscope itemtype="http://schema.org/Review">
						<meta itemprop="name" content="<?php e($_->config->com_testimonials->business_review_name); ?>" />
						<meta itemprop="about" content="<?php e($_->config->com_testimonials->business_review_name); ?>" />
						<?php
						if (is_array($testimonial)) { ?>
						<meta itemprop="datePublished" content="<?php e($testimonial['date']); ?>">
						<blockquote>
							<p itemprop="reviewBody">"<?php e($testimonial['testimonial']); ?>"</p>
						<?php if ($testimonial['author']) { ?>
						<small>
							<?php 
							$just_author = preg_replace('/ in.*$/', '', $testimonial['author']);
							$place = ' in'.preg_replace('/.*?in/', '', $testimonial['author']);
							?>
							<span itemprop="author"><?php e($just_author); ?></span><?php e($place); ?>
						</small>
						<?php } ?>
						<div class="pull-right rating-container" itemtype="http://schema.org/Rating" itemscope="" itemprop="reviewRating"> 
							<span>
								<meta content="1" itemprop="worstRating">
								<meta content="5" itemprop="bestRating">
								<meta content="<?php e((int) $testimonial['rating']); ?>" itemprop="ratingValue">
							<?php 
							for ($c = 1; $c <= 5; $c++) { 
								if ((int) $testimonial['rating'] >= $c) { ?>
								<i class="fa fa-star"></i>
							<?php } else { ?>
								<i class="fa fa-star-o"></i>
							<?php } 
							}
							?>
							</span>
						</div>
						<noscript><div class="star-container enable-js">Please enable JavaScript.</div></noscript>
						</blockquote>
						<?php } else { ?>	
						<noscript>You must enable JavaScript.</noscript>
						<p style="text-align:center;">
							<i class="fa fa-spinner fa-spin"></i>
							<br/>
							<span>Loading...</span>
						</p>
						<?php } ?>
					</div>
				</div>
				<div class="give-feedback">
					<div class="trigger-feedback transition">Tell us your story. Give feedback! <noscript>You must enable JavaScript!</noscript></div>
					<div id="feedback_form" class="hide clearfix" >
						<hr class="feedback-hr" />
						<?php if ($customer) { ?>
						<div class="form-content">
							<div class="row">
								<div class="col-sm-12" style="padding-right: 10px;">
									<label>
										<span style="font-size:.8em;">Your <span class="story">story</span>:</span>
										<textarea type="text" name="feedback" style="width: 100%; background-color: #eee;"></textarea>
									</label>
								</div>
							</div>
							<div class="row share-checkbox">
								<div class="col-sm-6">
									<label>
										<span style="font-size:.8em;">Allow us to share your <span class="story">story</span>:</span>
										<input type="checkbox" checked="checked"  value="ON" name="share" autocomplete="off" style="background-color: #eee;" />
									</label>
								</div>
								<div class="col-sm-6 share-checkbox">
									<label class="right-align">
										<span style="font-size:.8em;">Share Anonymously:</span>
										<input type="checkbox" name="anon" value="ON" autocomplete="off" style="background-color: #eee;" />
									</label>
								</div>
							</div>
							<hr class="feedback-hr" style="margin-bottom: 10px;" />
							<div class="row">
								<div class="col-sm-12">
									<div class="clearfix" style="margin-bottom: 8px;">
										<div class="pull-left">
											<div class="submit-button">Submit</div>
										</div>
										<div class="pull-right rating-container"> 
											<span class="rate-us">Rate Us</span>
											<span class="star-container">
												<span class="star"><i class="fa fa-star"></i></span>
												<span class="star"><i class="fa fa-star"></i></span>
												<span class="star"><i class="fa fa-star"></i></span>
												<span class="star"><i class="fa fa-star"></i></span>
												<span class="star"><i class="fa fa-star"></i></span>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-submit">
							<div class="feedback-status">
								<div class="please-rate-us">
									<span class="star"><i class="fa fa-star"></i></span>
									<span class="star"><i class="fa fa-star"></i></span>
									<span class="star"><i class="fa fa-star"></i></span>
									<span class="star"><i class="fa fa-star"></i></span>
									<span class="star"><i class="fa fa-star"></i></span>
								</div>
								<i class="fa fa-spinner fa-spin feedback-status-icon"></i>
								<br/>
								<span class="feedback-status-words">Submitting</span>
							</div>
							<div class="share-again">Share Another <span class="story">Story</span>?</div>
						</div>
						<?php } elseif (!gatekeeper()) { ?>
						<div class="row">
							<div class="col-sm-12" style="padding-right: 10px; text-align: center;">
								Please sign in to your account to share your story!
								<hr class="feedback-hr"/>
							</div>
							<div class="login-container">
								<?php
								if (empty($_SERVER['QUERY_STRING'])) {
									$url = pines_url();
								} else {
									$option = $_GET['option'];
									$action = $_GET['action'];
									$extra = preg_replace('/option=.+?&action=.+?(&|$)/', '', $_SERVER['QUERY_STRING']);
									if (empty($extra)) {
										$url = pines_url($option, $action);
									} else {
										$get_names = preg_replace('/,$/', '', preg_replace('/=.*?(&|$)/', ',', $extra));
										$get_values = preg_replace('/^,/', '', preg_replace('/(&|^).*?=/', ',', $extra));
										$var_names = explode(',', $get_names);
										$var_values = explode(',', $get_values);
										$query = array_combine($var_names, $var_values);
										$url = pines_url($option, $action, $query);
									}
								}
								?>
								<div style="text-align:center;"><a class="btn btn-success btn-block btn-lg signup-button" href="<?php e($_->config->com_testimonials->signup_link); ?>">Sign Up!</a></div>
								<?php
								$login = $_->user_manager->print_login(null, $url);
								$login->detach();
								echo $login->render();
								?>
							</div>
						</div>
						<?php } else { ?>
							<p>You must be a customer in order to leave a testimonial!</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	
