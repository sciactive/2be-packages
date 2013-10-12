<?php
/**
 * A dynamic JS file to use for testimonial modules.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */

header("Content-type: text/javascript");
$save_testimonial = json_encode($pines->template->url('com_testimonials', 'testimonial/save'));
$get_testimonials = json_encode($pines->template->url('com_testimonials', 'testimonial/get_testimonials'));

?>
$(document).ready(function(){
		
		var create_testimonial_module = function(parent) {
			var testimonials_container = parent.find('.testimonials-module'),
				feedback_container = testimonials_container.find('.give-feedback'),
				trigger_feedback = feedback_container.find('.trigger-feedback'),
				feedback_form = testimonials_container.find('#feedback_form'),
				anon_check = feedback_form.find('[name="anon"]'),
				share_check = feedback_form.find('[name="share"]'),
				submit = testimonials_container.find('.submit-button'),
				feedback_textarea = testimonials_container.find('[name=feedback]'),
				share_again = testimonials_container.find('.share-again'),
				form_submit = testimonials_container.find('.form-submit'),
				form_content = testimonials_container.find('.form-content'),
				please_rate = form_submit.find('.please-rate-us'),
				status_icon = form_submit.find('.feedback-status-icon'),
				status_words = form_submit.find('.feedback-status-words'),
				stars = form_content.find('.star'),
				please_stars = please_rate.find('.star'),
				stars_container = form_content.find('.star-container'),
				average_rating = testimonials_container.find('.average-rating'),
				star_rating = average_rating.find('.star-rating'),
				votes = average_rating.find('.votes'),
				testimonials_testimonials = testimonials_container.find('.testimonials');

			// Get all testimonial display javascript variables
			var test_loader = testimonials_container.find('.testimonial-loader'),
				loaded_testimonial = testimonials_container.find('.loaded-testimonial'),
				testimonial_box = testimonials_container.closest('.testimonial-box'),
				average_rating_box = testimonials_container.find('.average-rating'),
				no_average_rating_box = testimonials_container.find('.no-average-rating'),
				story_spans = testimonials_container.find('.story'),
				list_container = testimonials_container.find('.testimonial-list-container'),
				list_more = testimonials_container.find('.list-read-more'),
				list_up = testimonials_container.find('.list-up'),
				list_top = testimonials_container.find('.list-top');


			// Get all testimonial display variables
			if (!testimonial_box.length) {
				console.log('Please wrap your testimonial module in an element with a class testimonial-box. Put all your options in inputs within that same box.')
				return;
			}

			// Get all testimonial display options
			var review_reverse = testimonial_box.find('[name=review_option_reverse]'),
				review_limit = testimonial_box.find('[name=review_option_limit]'),
				review_offset = testimonial_box.find('[name=review_option_offset]'),
				review_type = testimonial_box.find('[name=review_option_type]'),
				review_data_type = testimonial_box.find('[name=review_data_type]'),
				review_display = testimonial_box.find('[name=review_option_display]'),
				review_tags = testimonial_box.find('[name=review_option_additional_tags]'),
				review_clear = testimonial_box.find('[name=review_option_clear]'),
				review_feedback_text = testimonial_box.find('[name=review_option_feedback_text]'),
				review_story_text = testimonial_box.find('[name=review_option_story_text]'),
				review_item_name = testimonial_box.find('[name=review_item_name]'),
				review_list_height = testimonial_box.find('[name=review_list_height]');

			if (review_type.val() == 'review') {
				// define more variables
				var review_entity = testimonial_box.find('[name=review_option_entity]');
				var review_entity_id = testimonial_box.find('[name=review_option_entity_id]');
				var review_name = testimonial_box.find('[name=review_option_name]');
			}

			$(window).resize(function(){
				if (testimonials_container.width() < 600)
					testimonials_container.addClass('small')
				else
					testimonials_container.removeClass('small');
			}).resize();

			trigger_feedback.click(function(){
				if (feedback_container.hasClass('opened')) {
					feedback_container.removeClass('opened')
					feedback_form.fadeOut(50);
				} else {
					feedback_container.addClass('opened')
					feedback_form.fadeIn(1000);
				}
			});

			share_check.change(function(){
				if ($(this).is(':checked')) {
					anon_check.removeAttr('disabled').closest('label').show();
				} else 
					anon_check.attr('disabled', 'disabled').closest('label').hide();
			});

			share_again.click(function(){
				status_icon.addClass('icon-spin icon-spinner').removeClass('icon-ok');
				status_words.text('Submitting');
				share_again.css('visiblity', 'hidden');
				if (status_words.text() != 'Error. Not Submitted.')
					feedback_textarea.val('');
				form_submit.hide();
				form_content.fadeIn();
				// Reset the height
				form_content.css('height', 'auto');
			});

			// Stars
			please_stars.click(function(){
				var cur_star = $(this);
				var num = cur_star.prevAll('.star').length;
				stars.eq(num).click();
				// submit now
				status_icon.show();
				submit.click();
			});
			stars.click(function(){
				var clicked_star = $(this);
				var rated = clicked_star.nextAll('.star').andSelf(); // deprecated changed to addBack();
				var rating = rated.length;

				stars.removeClass('rated');
				rated.addClass('rated')
				stars.hide();

				if (rating > 3)
					stars_container.append('<span class="remove"><i class="icon-ok"></i> <span>Thanks!</span></span></span>').fadeIn(200);
				else
					stars_container.append('<span class="remove" style="font-size: 14px;"><i class="icon-ok"></i></span>').fadeIn(200);

				setTimeout(function(){
					stars_container.find('.remove').remove();
					stars.fadeIn();
				}, 700);
			});

			submit.click(function(){
				please_rate.hide();
				var height = feedback_form.height() - 11;
				// Set the height so that it doesn't move weird when we send other messages to this form area.
				form_submit.height(height);

				if (check_value(feedback_textarea) != "" && form_content.find('.rated').length) {
					// This is where we make the ajax submit thing

					var feedback = feedback_textarea.val(),
						share = share_check.is(':checked') ? 'ON' : '',
						anon = anon_check.is(':checked') ? 'ON' : '',
						rating = form_content.find('.rated').length,
						customer_guid = <?php echo json_encode($_SESSION['user']->guid);?>;

					form_content.hide();
					form_submit.fadeIn();

					var values = {};
					values.type = 'module';
					values.customer = customer_guid;
					values.feedback = feedback;
					values.share = share;
					values.anon = anon;
					values.rating = rating;
					// Add some possible inputs from the module
					values.review_option_entity = check_value(review_entity);
					values.review_option_entity_id = check_value(review_entity_id);
					values.review_option_additional_tags = check_value(review_tags);
					values.review_option_name = check_value(review_name);
					values.review_option_type = check_value(review_type);
					values.review_data_type = check_value(review_data_type);

					$.ajax({
						url: <?php echo $save_testimonial; ?>,
						type: "POST",
						dataType: "json",
						data: {"type": "module", "customer": customer_guid, "feedback": feedback, "share": share, "anon": anon, "rating": rating},
						beforeSend: function() {
							status_icon.addClass('icon-spin icon-spinner').removeClass('icon-ok');
							status_words.text('Submitting');
							share_again.css('visibility', 'hidden');
						}, 
						error: function(XMLHttpRequest, textStatus){
							pines.error("An error occured:\n"+pines.safe(XMLHttpRequest.status)+": "+pines.safe(textStatus));
							status_icon.removeClass('icon-spin icon-spinner').removeClass('icon-remove');
							status_words.text('Error. Not Submitted.');
							share_again.hide().text('Try Again?').css('visibility', 'visible').fadeIn();
						},
						success: function(data){
							if (!data) {
								status_icon.removeClass('icon-spin icon-spinner icon-warning-sign icon-ok').addClass('icon-remove');
								status_words.text('Error. Not Submitted.');
								share_again.hide().text('Try Again?').css('visibility', 'visible').fadeIn();
								return;
							}

							if (data) {
								status_icon.removeClass('icon-spin icon-spinner icon-warning icon-remove').addClass('icon-ok');
								status_words.text('Success!');
								share_again.hide().text('Share another Story?').css('visibility', 'visible').fadeIn();
								return;
							}

							if (!data.result) {
								status_icon.removeClass('icon-spin icon-spinner icon-ok icon-warning-sign').addClass('icon-remove');
								status_words.text(data.message);
								share_again.hide().text('Try Again?').css('visibility', 'visible').fadeIn();
								return;
							}

						}
					});
				} else if (feedback_textarea.val() != "") {
					// They have completed the story but they didn't rate us.
					// Make them rate us here.
					status_icon.hide();
					please_rate.show();
					status_words.text('Please Rate Us!');
					form_content.hide();
					share_again.css('visibility', 'hidden');
					form_submit.fadeIn();
				} else {
					// Hide the content, show the submit
					status_icon.removeClass('icon-spin icon-spinner icon-remove').addClass('icon-warning-sign');
					status_words.text('Incomplete!');
					form_content.hide();
					share_again.css('visibility', 'hidden');
					form_submit.fadeIn();

					setTimeout(function(){
						form_submit.hide();
						status_icon.addClass('icon-spin icon-spinner').removeClass('icon-ok icon-warning-sign icon-remove');
						status_words.text('Submitting');
						share_again.css('visibility', 'hidden');
						form_content.fadeIn();
						// Reset the height
						feedback_form.css('height', 'auto');
					}, 500);
				}
			});


			// BEGIN RETRIEVAL 
			var check_value = function(element) {
				return (element == undefined) ?  '' : element.val();
			}

			// this will be used to determine all the options we need and what functions to call for the review/testimonials
			var get_testimonials = function(){
				// Change text
				if (check_value(review_feedback_text) != '') {
					trigger_feedback.text(review_feedback_text.val());
				}
				if (check_value(review_story_text) != '') {
					story_spans.text(review_story_text.val());
				}

				// Determine if we need to hide the php loaded one with a loading throbber.
				if (check_value(review_clear) == 'true') {
					loaded_testimonial.hide();
					average_rating_box.hide();
					if (check_value(review_item_name) != '')
						no_average_rating_box.text(pines.safe(review_item_name.val()) + ' Reviews');
					no_average_rating_box.show();
					testimonial_box.hide();
					testimonial_box.css('visibility', 'visible');
					testimonial_box.fadeIn();
					test_loader.fadeIn();
				}

				// Determine how to display the testimonials
				// If list, then call list function
				var options = {};
				options.review_reverse = check_value(review_reverse);
				options.review_limit = check_value(review_limit);
				options.review_offset = check_value(review_offset);
				options.review_tags = check_value(review_tags);
				options.review_entity = check_value(review_entity);
				options.review_entity_id = check_value(review_entity_id);
				options.review_name = check_value(review_name);
				options.review_option_type = check_value(review_type);
				options.review_data_type = check_value(review_data_type);

				if (check_value(review_display) == 'list') {
					list_testimonials(options);
					return;
				}

				// Do this for carousel
				//load_testimonials(options, 'carousel');
			};

			// Use to create list
			var list_testimonials = function(options){
				testimonials_testimonials.addClass('make-list');
				if (check_value(review_list_height) != '') {
					testimonials_testimonials.css('height', review_list_height.val()+'px');
				}
				// Capture the first time here, and then never again
				// To create the scroll and click rule
				if (!testimonials_container.hasClass('list-started')) {
					testimonials_container.addClass('list-started');
					var scroll_increment = (check_value(review_list_height) != '') ? parseInt(review_list_height.val()) - 40 : 120;
					// Define scroll rule
					// Define click rule on list more
					list_more.click(function(){
						if (list_container[0].scrollHeight - list_container.scrollTop() == list_container.height()) {
							list_testimonials(options);
						}
						var scroll = list_container.scrollTop() + scroll_increment;
						list_container.animate({scrollTop: scroll});
					});
					// Capture scrolling
					list_container.scroll(function(){
						if (list_container[0].scrollHeight - list_container.scrollTop() == list_container.height()) {
							list_testimonials(options);
						}
					});
					// Define click rule for list up
					list_up.click(function(){
						var scroll = list_container.scrollTop() - scroll_increment;
						list_container.animate({scrollTop: scroll});
					});
					// Define click rule for list up
					list_top.click(function(){
						list_container.animate({scrollTop: 0});
					});
				}

				// You only need to increment the offset
				// So you WILL need to get this value every time
				options.review_offset = check_value(review_offset);

				// Load the testimonials
				load_testimonials(options, 'list');

				// and change the offset value everytime.
				var cur_value = (check_value(review_offset) == '') ? 0 : review_offset.val(); 
				review_offset.val(parseInt(cur_value) + parseInt(review_limit.val())); // so that next time it will get the next 5

				// this is in charge of calling load testimonials in increments as the user clicks read more or scrolls down.
			};

			// the function to be used by all types of calls to retrieve reviews/testimonials
			var load_testimonials = function(options, display){
				// Using arguments, make ajax call to retrieve testimonials
				$.ajax({
					url: <?php echo $get_testimonials; ?>,
					type: "POST",
					dataType: "json",
					data: options,
					beforeSend: function() {
						// Here you will want to make the list-more have a loader icon
						// But you really won't want to have loading things happening with carousel, it should happen once and
						// hopefully not obstructively
						// and it should happen once in the beginning to load the first X amount of items for the list.
						list_more.css('visibility', 'hidden');
						list_more.find('i').remove();
						list_more.prepend('<i class="icon-spin icon-spinner"></i> ');
						list_more.hide().css('visibility', 'visible').fadeIn();
					}, 
					error: function(XMLHttpRequest, textStatus){
						pines.error("An error occured:\n"+pines.safe(XMLHttpRequest.status)+": "+pines.safe(textStatus));
						list_more.find('i').remove();
						list_more.text('Error Loading...');
						list_more.prepend('<i class="icon-remove"></i> ');
						// I don't know what kind of error you want for the carousel.
						// If we cleared then maybe show error, but if not a clear - leave it
						if (check_value(review_clear) == 'true') {
							test_loader.text('An Error Occurred.');
							test_loader.find('i').removeAttr('class').addClass('icon-remove')
						}
					},
					success: function(data){
						if (data == 'No testimonials found.') {
							if (test_loader.is(':visible')) {
								test_loader.text('Be the First to Share!');
								test_loader.find('i').removeAttr('class').addClass('icon-thumbs-up');
								return;
							} else {
								list_more.find('i').remove();
								list_more.text('No More Reviews. Share yours!');
								list_more.prepend('<i class="icon-edit"></i> ');
								list_more.css('visibility', 'hidden');
								list_more.hide().css('visibility', 'visible').fadeIn();
								return;
							}
						}
						test_loader.hide();
						list_more.find('i').remove();
						list_more.css('visibility', 'hidden');
						list_more.hide().css('visibility', 'visible').fadeIn();
						// Depending on the display, add items to list or to carousel
						list_container.fadeIn();
						add_testimonials(data, display);
					}
				});


				// On success, call function to put the testimonials in either a list or a carousel
				// call function to add item(s)
			};

			// Construct a testimonial
			var construct_testimonial = function(object) {
				var blockquote = $('<blockquote></blockquote>');
				blockquote.append('<meta content="'+check_value(review_item_name)+'" itemprop="about"/>');
				blockquote.append('<meta content="'+check_value(review_item_name)+'" itemprop="name"/>');
				blockquote.append('<meta content="'+object.date+'" itemprop="datePublished"/>');

				blockquote.append('<p itemprop="description">'+object.testimonial+'</p>');
				if (object.author != false) {
					var just_author = object.author.replace(/ in.*$/, '');
					var just_place = ' in'+object.author.replace(/.*?in/, '');
					blockquote.append('<small><span itemprop="author">'+just_author+'</span>'+just_place+'</small>');
				}

				var stars = $('<div class="pull-right rating-container"><span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><meta itemprop="worstRating" content="1"><meta itemprop="bestRating" content="5"><meta itemprop="ratingValue" content="'+object.rating+'"></span></div>');
				for (var c = 1; c <= 5; c++) {
					if (object.rating >= c) {
						stars.find('span').append('<i class="icon-star"></i> ');
					} else {
						stars.find('span').append('<i class="icon-star-empty"></i> ');
					}
				}
				blockquote.append(stars);
				var item = $('<div class="testimonial item hide" itemtype="http://schema.org/Review" itemscope="" itemprop="review"><div class="content clearfix"></div><div class="item-bottom-border"></div></div>');
				item.find('.content').append(blockquote);
				return item;
			}

			// Use to add items
			var add_testimonials = function(data, display){

				// Add Items to List
				if (display == 'list') {
					$.each(data, function(index, value){
						var item = construct_testimonial(value);
						list_container.find('.list-placeholder').before(item);
						item.fadeIn();
					});
				} else {
					// Add Items to Carousel
					// Add Carousel Class to testimonials_testimonials
					testimonials_testimonials.addClass('carousel');
					// Add Carousel-inner class to testimionial-list-container
					list_container.addClass('carousel-inner');
				}


				// Initialize the Carousel.
				if (!testimonials_container.hasClass('initialized')) {
					// You will need to initialize the Carousel!
					if (display == 'carousel') {

					}
					testimonials_container.addClass('initialized');
				} else {
					//list_more.click();
				}
			};


			// Launch Get Testimonials
			get_testimonials();

			if (star_rating.length) {
				star_rating.tooltip();
			}
		}
		
		$('.testimonial-box').each(function(){
			create_testimonial_module($(this));
		});
	});