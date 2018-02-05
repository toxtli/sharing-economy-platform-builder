jQuery(document).ready(function($) {
	$enquiry_form_show = false;
	$('.add_enquiry').click(function() {
		if( $enquiry_form_show ) {
			$('.enquiry_form_wrapper_hide').slideUp( "slow" );
			$enquiry_form_show = false;
		} else {
			$('.enquiry_form_wrapper_hide').slideDown( "slow" );
			$enquiry_form_show = true;
		}
	});
	
	
	function wcfm_enquiry_form_validate() {
		$is_valid = true;
		$('.wcfm-message').html('').removeClass('wcfm-success').removeClass('wcfm-error').slideUp();
		var enquiry_comment = $.trim($('#wcfm_enquiry_form').find('#enquiry_comment').val());
		if(enquiry_comment.length == 0) {
			$is_valid = false;
			$('#wcfm_enquiry_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_enquiry_manage_messages.no_enquiry).addClass('wcfm-error').slideDown();
		}
		
		if( $('#enquiry_author').length > 0 ) {
			var enquiry_author = $.trim($('#wcfm_enquiry_form').find('#enquiry_author').val());
			if(enquiry_author.length == 0) {
				if( $is_valid )
					$('#wcfm_enquiry_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_enquiry_manage_messages.no_name).addClass('wcfm-error').slideDown();
				else
					$('#wcfm_enquiry_form .wcfm-message').append('<br /><span class="wcicon-status-cancelled"></span>' + wcfm_enquiry_manage_messages.no_name).addClass('wcfm-error').slideDown();
				
				$is_valid = false;
			}
		}
		
		if( $('#enquiry_email').length > 0 ) {
			var enquiry_email = $.trim($('#wcfm_enquiry_form').find('#enquiry_email').val());
			if(enquiry_email.length == 0) {
				if( $is_valid )
					$('#wcfm_enquiry_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_enquiry_manage_messages.no_email).addClass('wcfm-error').slideDown();
				else
					$('#wcfm_enquiry_form .wcfm-message').append('<br /><span class="wcicon-status-cancelled"></span>' + wcfm_enquiry_manage_messages.no_email).addClass('wcfm-error').slideDown();
				
				$is_valid = false;
			}
		}
		return $is_valid;
	}
	
	// Submit Enquiry
	$('#wcfm_enquiry_submit_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_enquiry_form_validate();
	  
	  if($is_valid) {
			$('#enquiry_form_wrapper').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-enquiry-tab',
				wcfm_enquiry_tab_form    : $('#wcfm_enquiry_form').serialize(),
				status                   : 'submit'
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					if($response_json.status) {
						$('#wcfm_enquiry_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow" );
					} else {
						$('#wcfm_enquiry_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#enquiry_form_wrapper').unblock();
					setTimeout(function() {
						$('.enquiry_form_wrapper_hide').slideUp( "slow" );
						$enquiry_form_show = false;
						$('#wcfm_enquiry_form').find('#enquiry_comment').val('');
					}, 2000 );
				}
			});
		}
	});
	
	
});