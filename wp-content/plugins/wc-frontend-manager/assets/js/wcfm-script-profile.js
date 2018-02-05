jQuery(document).ready( function($) {
	// Collapsible
	$('.page_collapsible').collapsible({
		defaultOpen: 'wcfm_profile_personal_head',
		speed: 'slow',
		loadOpen: function (elem) { //replace the standard open state with custom function
				elem.next().show();
		},
		loadClose: function (elem, opts) { //replace the close state with custom function
				elem.next().hide();
		},
		animateOpen: function(elem, opts) {
			$('.collapse-open').addClass('collapse-close').removeClass('collapse-open');
			elem.addClass('collapse-open');
			$('.collapse-close').find('span').removeClass('fa-arrow-circle-o-right block-indicator');
			elem.find('span').addClass('fa-arrow-circle-o-right block-indicator');
			$('.wcfm-tabWrap').find('.wcfm-container').stop(true, true).slideUp(opts.speed);
			elem.next().stop(true, true).slideDown(opts.speed);
		},
		animateClose: function(elem, opts) {
			elem.find('span').removeClass('fa-arrow-circle-o-up block-indicator');
			elem.next().stop(true, true).slideUp(opts.speed);
		}
	});
	$('.page_collapsible').each(function() {
		$(this).html('<div class="page_collapsible_content_holder">' + $(this).html() + '</div>');
		$(this).find('.page_collapsible_content_holder').after( $(this).find('span') );
	});
	$('.page_collapsible').find('span').addClass('fa');
	$('.collapse-open').addClass('collapse-close').removeClass('collapse-open');
	$('.wcfm-tabWrap').find('.wcfm-container').hide();
	setTimeout(function() {
		$('.wcfm-tabWrap').find('.page_collapsible:first').click();
	}, 500 );
	
	// Tabheight  
	$('.page_collapsible').each(function() {
		if( !$(this).hasClass('wcfm_head_hide') ) {
			collapsHeight += $(this).height() + 30;
		}
	});  
	
	// TinyMCE intialize - About
	if( $('#about').length > 0 ) {
		if( $('#about').hasClass('rich_editor') ) {
			var descTinyMCE = tinymce.init({
																		selector: '#about',
																		height: 75,
																		menubar: false,
																		plugins: [
																			'advlist autolink lists link charmap print preview anchor',
																			'searchreplace visualblocks code fullscreen',
																			'insertdatetime table contextmenu paste code directionality',
																			'autoresize'
																		],
																		toolbar: tinyMce_toolbar,
																		content_css: '//www.tinymce.com/css/codepen.min.css',
																		statusbar: false,
																		browser_spellcheck: true,
																		entity_encoding: "raw"
																	});
		}
	}
		
	if( $(".country_select").length > 0 ) {
		$(".country_select").select2({
			placeholder: wcfm_dashboard_messages.choose_select2 + ' ...'
		});
	}
	
	// Save Profile
	$('#wcfmprofile_save_button').click(function(event) {
	  event.preventDefault();
	  
	  var about = '';
	  if( $('#about').hasClass('rich_editor') ) {
			if( typeof tinymce != 'undefined' ) about = tinymce.get('about').getContent({format: 'raw'});
		} else {
			about = $('#about').val();
		}
  
	  // Validations
	  $('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
	  $wcfm_is_valid_form = true;
	  $( document.body ).trigger( 'wcfm_form_validate' );
	  $is_valid = $wcfm_is_valid_form;
	  
	  if($is_valid) {
			$('#wcfm_profile_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-profile',
				wcfm_profile_form  : $('#wcfm_profile_form').serialize(),
				about              : about
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_profile_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					} else {
						audio.play();
						$('#wcfm_profile_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_profile_form').unblock();
				}
			});	
		}
	});
});