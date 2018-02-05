jQuery(document).ready( function($) {
	// Collapsible
  $('.page_collapsible').collapsible({
		defaultOpen: 'wcfm_capability_form_vendor_head',
		speed: 'slow',
		loadOpen: function (elem) { //replace the standard open state with custom function
		  elem.next().show();
		},
		loadClose: function (elem, opts) { //replace the close state with custom function
			elem.next().hide();
		}
	});
	
	if( $('#vendor_allowed_categories').length > 0 ) {
		$('#vendor_allowed_categories').select2();
	}
	
	if( $('.vendor_allowed_custom_taxonomies').length > 0 ) {
		$('.vendor_allowed_custom_taxonomies').select2();
	}
	
	// Save Settings
	$('#wcfm_capability_save_button').click(function(event) {
	  event.preventDefault();
	  
		$('#wcfm_capability_form').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action             : 'wcfm_ajax_controller',
			controller         : 'wcfm-capability',
			wcfm_capability_form : $('#wcfm_capability_form').serialize()
		}	
		$.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$response_json = $.parseJSON(response);
				$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
				if($response_json.status) {
					audio.play();
					$('#wcfm_capability_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
				} else {
					audio.play();
					$('#wcfm_capability_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
				}
				$('#wcfm_capability_form').unblock();
			}
		});	
	});
});