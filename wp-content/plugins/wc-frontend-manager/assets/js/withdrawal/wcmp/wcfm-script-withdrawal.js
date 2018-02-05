jQuery(document).ready(function($) {
		
	$wcfm_withdrawal_table = $('#wcfm-withdrawal').DataTable( {
		"processing": true,
		"serverSide": true,
		"bFilter"   : false,
		"dom"       : 'Bfrtip',
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"buttons"   : [
            				'print', 'pdf', 'excel', 'csv' 
        					],
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 4 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }, 
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-withdrawal'
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-withdrawal table refresh complete
				$( document.body ).trigger( 'updated_wcfm-withdrawal' );
			}
		}
	} );
	
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
					$('#wcfm_withdrawal_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					$wcfm_withdrawal_table.ajax.reload();	
				} else {
					audio.play();
					$('.wcfm-message').html('').removeClass('wcfm-success').slideUp();
					$('#wcfm_withdrawal_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
				}
				$('#wcfm-content').unblock();
			}
		});
	});
} );