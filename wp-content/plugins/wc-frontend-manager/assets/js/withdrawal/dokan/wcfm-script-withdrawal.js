jQuery(document).ready(function($) {
		
	// Request Withdrawals
	$('#wcfm_withdrawal_request_button').click(function(event) {
	  event.preventDefault();
	  
		$('#wcfm-content').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action                      : 'wcfm_ajax_controller',
			controller                  : 'wcfm-withdrawal-request',
			wcfm_withdrawal_manage_form : $('#wcfm_withdrawal_manage_form').serialize(),
			status                      : 'submit'
		}	
		$.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$response_json = $.parseJSON(response);
				if($response_json.status) {
					audio.play();
					$('.wcfm-message').html('').removeClass('wcfm-success').slideUp();
					$('#wcfm_withdrawal_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					if( $response_json.redirect ) {
						setTimeout(function() {
							window.location = 	$response_json.redirect;
						}, 2000);
					} else {
						$('#wcfm-content').unblock();
					}
				} else {
					audio.play();
					$('.wcfm-message').html('').removeClass('wcfm-success').slideUp();
					$('#wcfm_withdrawal_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					$('#wcfm-content').unblock();
				}
			}
		});
	});
} );