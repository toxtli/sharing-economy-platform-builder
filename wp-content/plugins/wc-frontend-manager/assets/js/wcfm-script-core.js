$wcfm_products_table = '';
var $wcfm_is_valid_form = true;

var tinyMce_toolbar = 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify |  bullist numlist outdent indent | link image | ltr rtl';
if ( wcfm_params.wcfm_allow_tinymce_options ) {
	tinyMce_toolbar = wcfm_params.wcfm_allow_tinymce_options;
}

function initiateTip() {
  jQuery('.img_tip, .text_tip').each(function() {
		jQuery(this).qtip({
			content: jQuery(this).attr('data-tip'),
			position: {
				my: 'top center',
				at: 'bottom center',
				viewport: jQuery(window)
			},
			show: {
				event: 'mouseover',
				solo: true,
			},
			hide: {
				inactive: 6000,
				fixed: true
			},
			style: {
				classes: 'qtip-dark qtip-shadow qtip-rounded qtip-wcfm-css qtip-wcfm-core-css'
			}
		});
	});
}

function GetURLParameter(sParam) {
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');
	for (var i = 0; i < sURLVariables.length; i++) {
		var sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] == sParam) {
			return sParameterName[1];
		}
	}
}

jQuery(document).ready(function($) {
	initiateTip();
	
	// Delete Product
	$('.wcfm_delete_product').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			var rconfirm = confirm("Are you sure and want to delete this 'Product'?\nYou can't undo this action ...");
			if(rconfirm) deleteWCFMProduct($(this));
			return false;
		});
	});
	
	function deleteWCFMProduct(item) {
		jQuery('.products').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'delete_wcfm_product',
			proid : item.data('proid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				window.location = wcfm_params.shop_url;
			}
		});
	}
	
	// WCFM Form Global Validation
	$( document.body ).on( 'wcfm_form_validate', function() {
		$proccessed_required_fileds = [];
		$('[data-required="1"]').each(function() {
			$data_name = $(this).attr('name');
			if( $.inArray( $data_name, $proccessed_required_fileds ) === -1 ) {
				$proccessed_required_fileds.push($data_name);
				if( !$(this).parents('.wcfm-container').hasClass('wcfm_block_hide') && !$(this).parents('.wcfm-container').hasClass('wcfm_custom_hide') && !$(this).parent().parent().hasClass('wcfm_block_hide') && !$(this).parent().parent().hasClass('wcfm_custom_hide') && !$(this).parent().hasClass('wcfm_block_hide') && !$(this).parent().hasClass('wcfm_custom_hide') && !$(this).hasClass('wcfm_ele_hide') && !$(this).hasClass('wcfm_custom_hide') ) {
					if ( $(this).is( 'input[type="checkbox"]' ) || $(this).is( 'input[type="radio"]' ) ) {
						$('[name="'+$data_name+'"]').removeClass('wcfm_validation_failed').addClass('wcfm_validation_success');
						if( !$('[name="'+$data_name+'"]').is( ":checked" ) ) {
							if( $wcfm_is_valid_form ) 
								$('#wcfm-main-contentainer .wcfm-message').html( '<span class="wcicon-status-cancelled"></span>' + $(this).data('required_message') ).addClass('wcfm-error').slideDown();
							else
								$('#wcfm-main-contentainer .wcfm-message').append( '<br /><span class="wcicon-status-cancelled"></span>' + $(this).data('required_message') );
							
							$wcfm_is_valid_form = false;
							$('[name="'+$data_name+'"]').removeClass('wcfm_validation_success').addClass('wcfm_validation_failed');
						}
					} else {
						$(this).removeClass('wcfm_validation_failed').addClass('wcfm_validation_success');
						$data_val = $(this).val();
						if( !$data_val ) {
							if( $wcfm_is_valid_form ) 
								$('#wcfm-main-contentainer .wcfm-message').html( '<span class="wcicon-status-cancelled"></span>' + $(this).data('required_message') ).addClass('wcfm-error').slideDown();
							else
								$('#wcfm-main-contentainer .wcfm-message').append( '<br /><span class="wcicon-status-cancelled"></span>' + $(this).data('required_message') );
							
							$wcfm_is_valid_form = false;
							$(this).removeClass('wcfm_validation_success').addClass('wcfm_validation_failed');
						}
					}
				}
			}
		});
	});
	
	// Message Counter auto Refresher
	var messageCountRefrsherTime = '';
	function messageCountRefrsher() {
		clearTimeout(messageCountRefrsherTime);
		messageCountRefrsherTime = setTimeout(function() {
			var data = {
				action : 'wcfm_message_count'
			}	
			jQuery.ajax({
				type:		'POST',
				url: wcfm_params.ajax_url,
				data: data,
				success:	function(response) {
					$response_json = $.parseJSON(response);
					if($response_json.notice) {
						$('.notice_count').text($response_json.notice);
					}
					if($response_json.message) {
						$('.message_count').text($response_json.message);
						getNewMessageNotification($response_json.message);
					}
					if($response_json.enquiry) {
						$('.enquiry_count').text($response_json.enquiry);
					}
				}
			});
			messageCountRefrsher();
		}, 15000 );
	}
	if( wcfm_params.wcfm_is_allow_wcfm ) {
		messageCountRefrsher();
	}
	
	// Fetching new Message Notifications
	var notificationRefrsherTime = '';
	function getNewMessageNotification(message_count) {
		message_count = parseInt(message_count);
		var unread_message = parseInt(wcfm_params.unread_message);
		
		if( message_count > unread_message ) {
			clearTimeout(notificationRefrsherTime);
			$('.wcfm_notification_wrapper').slideDown(function() { $('.wcfm_notification_wrapper').remove(); } );
			
			var data = {
				action : 'wcfm_message_notification',
				limit  : (message_count - unread_message)
			}	
			jQuery.ajax({
				type:		'POST',
				url: wcfm_params.ajax_url,
				data: data,
				success:	function(response) {
					if( response ) {
						wcfm_params.unread_message = message_count;
						$('body').append(response);
						//initiateTip();
						notificationRefrsherTime = setTimeout(function() {
						  $('.wcfm_notification_wrapper').slideDown(function() { $('.wcfm_notification_wrapper').remove(); } );
						}, 30000 );
						$('.wcfm_notification_close').click(function() {
							clearTimeout(notificationRefrsherTime);
							$('.wcfm_notification_wrapper').slideDown(function() { $('.wcfm_notification_wrapper').remove(); } );
						});
					}
				}
			});
		}
	}
});