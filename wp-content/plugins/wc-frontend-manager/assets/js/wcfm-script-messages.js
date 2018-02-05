$wcfm_messages_table = '';
jQuery(document).ready( function($) {
	// TinyMCE intialize - Description
	if( $('#wcfm_messages').length > 0 ) {
		var descTinyMCE = tinymce.init({
																	selector: '#wcfm_messages',
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
																});
	}
	
	if( $("#direct_to").length > 0 ) {
		$("#direct_to").select2({
			placeholder: wcfm_dashboard_messages.choose_vendor_select2
		});
	}
	
	// Save Settings
	$('#wcfm_messages_save_button').click(function(event) {
	  event.preventDefault();
	  
	  var wcfm_messages = '';
	  if( typeof tinymce != 'undefined' ) wcfm_messages = tinymce.get('wcfm_messages').getContent();
	  var direct_to = 0;
	  if( $('#direct_to').length > 0 ) direct_to = $('#direct_to').val();
	  
	  if( !wcfm_messages ) return false;
  
	  // Validations
	  $is_valid = true; //wcfm_coupons_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm_messages_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-message-sent',
				wcfm_messages      : wcfm_messages,
				direct_to          : direct_to
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						tinymce.get('wcfm_messages').setContent('');
						$('#direct_to').val('');
						audio.play();
						$wcfm_messages_table.ajax.reload();
						$('#wcfm_messages_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					} else {
						audio.play();
						$('#wcfm_messages_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_messages_form').unblock();
				}
			});	
		}
	});
	
	$message_status = 'unread';
	if( $('#filter-by-status').length > 0 ) {
		$('#filter-by-status').on('change', function() {
			$message_status = $('#filter-by-status').val();
			$wcfm_messages_table.ajax.reload();
		});
	}
	
	$message_type = 'all';
	if( $('#filter-by-type').length > 0 ) {
		$('#filter-by-type').on('change', function() {
			$message_type = $('#filter-by-type').val();
			$wcfm_messages_table.ajax.reload();
		});
	}
	
	$wcfm_messages_table = $('#wcfm-messages').DataTable( {
		"processing": true,
		"serverSide": true,
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 2 },
										{ responsivePriority: 3 },
										{ responsivePriority: 4 },
										{ responsivePriority: 1 },
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false },
										{ "targets": 4, "orderable" : false },
										{ "targets": 5, "orderable" : false },
										{ "targets": 6, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action            = 'wcfm_ajax_controller',
				d.controller        = 'wcfm-messages',
				d.message_status    = $message_status,
				d.message_type      = $message_type
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-orders table refresh complete
				$( document.body ).trigger( 'updated_wcfm-messages' );
			}
		}
	} );
	
	// Mark Messages as Read
	$( document.body ).on( 'updated_wcfm-messages', function() {
		$('.wcfm_messages_mark_read').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				//var rconfirm = confirm("Are you sure and want to 'Mark as Complete' this Order?");
				markReadWCFMMessage($(this));
				return false;
			});
		});
	});
	
	function markReadWCFMMessage(item) {
		$('#wcfm-messages_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'wcfm_messages_mark_read',
			messageid : item.data('messageid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_messages_table.ajax.reload();
				$('#wcfm-messages_wrapper').unblock();
			}
		});
	}
	
	$('.bulk_action_checkbox_all').click(function() {
		if( $(this).is(':checked') ) {
			$('.bulk_action_checkbox_all').attr( 'checked', true );
			$('.bulk_action_checkbox_single').attr( 'checked', true );
		}	else {
			$('.bulk_action_checkbox_all').attr( 'checked', false );
			$('.bulk_action_checkbox_single').attr( 'checked', false );
		}
	});
		
	// Message mark read in bulk
	$('#wcfm_bulk_mark_read').click( function( event ) {
		event.preventDefault();
		
		$('#wcfm-messages_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		
		$selected_messages = [];
		$('.bulk_action_checkbox_single').each(function() {
		  if( $(this).is(':checked') ) {
		  	$selected_messages.push( $(this).val() );
		  }
		});
		
		if ( $selected_messages.length === 0 ) {
			alert( wcfm_dashboard_messages.wcfm_bulk_action_no_option );
			$('#wcfm-messages_wrapper').unblock();
			return false;
		}
		
		var rconfirm = confirm( wcfm_dashboard_messages.wcfm_bulk_action_confirm );
		if(rconfirm) { 
			var data = {
				action            : 'wcfm_messages_bulk_mark_read',
				selected_messages : $selected_messages
			}	
			
			$.ajax({
				type    :		'POST',
				url     : wcfm_params.ajax_url,
				data    : data,
				success :	function(response) {  
					if( response ) {
						if($wcfm_messages_table) $wcfm_messages_table.ajax.reload();
					}
					$('#wcfm-messages_wrapper').unblock();
				}
			});
		} else {
			$('#wcfm-messages_wrapper').unblock();
		}
	});
	
	// Verification Response
	$( document.body ).on( 'updated_wcfm-messages', function() {
		$('.wcfm_messages_seller_verification').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				verificationWCFMSeller($(this));
				return false;
			});
		});
	});
	
	function verificationWCFMSeller(item) {
		/*$('#wcfm-messages_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});*/
		var data = {
			action    : 'wcfmu_seller_verification_html',
			messageid : item.data('messageid'),
			vendorid  : item.data('vendorid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				// Intialize Fancybox
				jQuery.fancybox.open(response);
				
				// Intialize Quick Update Action
				jQuery('#wcfm_verification_response_button').click(function() {
					jQuery('#wcfm_verification_response_form').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
					var data = {
						action     : 'wcfmu_verification_response_update',
						wcfm_verification_response_form : jQuery('#wcfm_verification_response_form').serialize()
					}	
					jQuery.post(wcfm_params.ajax_url, data, function(response) {
						if(response) {
							jQueryresponse_json = jQuery.parseJSON(response);
							jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
							if(jQueryresponse_json.status) {
								jQuery('#wcfm_verification_response_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
								jQuery('#wcfm_verification_response_button').hide();
							} else {
								jQuery('#wcfm_verification_response_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
							}
							jQuery('#wcfm_verification_response_form').unblock();
							setTimeout(function() {
								if($wcfm_messages_table) $wcfm_messages_table.ajax.reload();
								jQuery.fancybox.close();
							}, 2000);
						}
					} );
				});
			}
		});
	}
	
	// Delete Message
	$( document.body ).on( 'updated_wcfm-messages', function() {
		$('.wcfm_messages_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.message_delete_confirm);
				if(rconfirm) deleteWCFMMessage($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMMessage(item) {
		$('#wcfm-messages_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'wcfm_messages_delete',
			messageid : item.data('messageid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_messages_table.ajax.reload();
				$('#wcfm-messages_wrapper').unblock();
			}
		});
	}
	
	// Message Board auto Refresher
	var messageBoardRefrsherTime = '';
	function messageBoardRefrsher() {
		clearTimeout(messageBoardRefrsherTime);
		messageBoardRefrsherTime = setTimeout(function() {
			$wcfm_messages_table.ajax.reload();
			messageBoardRefrsher();
		}, 30000 );
	}
	messageBoardRefrsher();
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
		
});