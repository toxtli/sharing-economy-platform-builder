jQuery(document).ready( function($) {
	// Collapsible
  $('.page_collapsible').collapsible({
		defaultOpen: 'coupons_manage_restriction',
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
			$('.collapse-close').find('span').addClass('fa-arrow-circle-o-left').removeClass('fa-arrow-circle-o-right').css( { 'float': 'right', 'padding': '5px' } ).show();
			elem.find('span').addClass('fa-arrow-circle-o-right').removeClass('fa-arrow-circle-o-left').css( { 'float': 'right', 'padding': '5px' } ).show();
			$('.wcfm-tabWrap').find('.wcfm-container').stop(true, true).slideUp(opts.speed);
			elem.next().stop(true, true).slideDown(opts.speed);
		},
		animateClose: function(elem, opts) {
			elem.find('span').removeClass('fa-arrow-circle-o-up').hide();
			elem.next().stop(true, true).slideUp(opts.speed);
		}
	});
	$('.page_collapsible').find('span').addClass('fa');
	$('.collapse-open').addClass('collapse-close').removeClass('collapse-open');
	$('.wcfm-tabWrap').find('.wcfm-container').hide();
	$('.wcfm-tabWrap').find('.page_collapsible:first').click();
	
	
	// Tabheight  
	$('.page_collapsible').each(function() {
		if( !$(this).hasClass('wcfm_head_hide') ) {
			collapsHeight += $(this).height() + 30;
		}
	});  
	if ($(window).width() > 768) {
		$('.wcfm-tabWrap').css('height', 475 );
	}
	
	// Datepicker
	$('.wcfm_datepicker').each(function() {
	  $(this).datepicker({
      dateFormat : 'yy-mm-d',
      changeMonth: true,
      changeYear: true
    });
  });
  
	function wcfm_coupons_manage_form_validate() {
		$is_valid = true;
		$('.wcfm-message').html('').removeClass('wcfm-error').slideUp();
		var title = $.trim($('#wcfm_coupons_manage_form').find('#title').val());
		if(title.length == 0) {
			$is_valid = false;
			$('#wcfm_coupons_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_coupons_manage_messages.no_title).addClass('wcfm-error').slideDown();
			audio.play();
		}
		return $is_valid;
	}
	
	// Draft Coupon
	$('#wcfm_coupon_manager_draft_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_coupons_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-coupons-manage',
				wcfm_coupons_manage_form : $('#wcfm_coupons_manage_form').serialize(),
				status                   : 'draft'
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_coupons_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					} else {
						audio.play();
						$('#wcfm_coupons_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#coupon_id').val($response_json.id);
					$('#wcfm-content').unblock();
				}
			});	
		}
	});
	
	// Submit Coupon
	$('#wcfm_coupon_manager_submit_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_coupons_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-coupons-manage',
				wcfm_coupons_manage_form : $('#wcfm_coupons_manage_form').serialize(),
				status                   : 'submit'
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					if($response_json.status) {
						audio.play();
						$('#wcfm_coupons_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
						  if( $response_json.redirect ) window.location = $response_json.redirect;	
						} );
					} else {
						audio.play();
						$('.wcfm-message').html('').removeClass('wcfm-success').slideUp();
						$('#wcfm_coupons_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#coupon_id').val($response_json.id);
					$('#wcfm-content').unblock();
				}
			});
		}
	});
} );