jQuery(document).ready( function($) {
	$wcfm_messages_table = '';
	
	// TinyMCE intialize - Description
	if( $('#topic_reply').length > 0 ) {
		var descTinyMCE = tinymce.init({
																	selector: '#topic_reply',
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
	
	// Save Settings
	$('#wcfm_reply_send_button').click(function(event) {
	  event.preventDefault();
	  
	  var topic_reply = '';
	  if( typeof tinymce != 'undefined' ) topic_reply = tinymce.get('topic_reply').getContent();
	  var topic_id = $('#topic_id').val();
  
	  // Validations
	  $is_valid = true;
	  
	  $('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
		if(topic_reply.length == 0) {
			$is_valid = false;
			$('#wcfm_topic_reply_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_notice_view_messages.no_title).addClass('wcfm-error').slideDown();
			audio.play();
		}
	  
	  if($is_valid) {
			$('#wcfm_topic_reply_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-notice-reply',
				topic_reply        : topic_reply,
				topic_id           : topic_id
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						tinymce.get('topic_reply').setContent('');
						audio.play();
						$('#wcfm_topic_reply_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow" , function() {
						  if( $response_json.redirect ) window.location = $response_json.redirect;	
						} );
					} else {
						audio.play();
						$('#wcfm_topic_reply_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_topic_reply_form').unblock();
				}
			});	
		}
	});
});