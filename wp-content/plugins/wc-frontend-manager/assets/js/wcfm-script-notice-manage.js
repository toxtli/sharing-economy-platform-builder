jQuery(document).ready( function($) {
		
	// TinyMCE intialize - Description
	if( $('#wcfm_notice').length > 0 ) {
		var descTinyMCE = tinymce.init({
																	selector: '#wcfm_notice',
																	height: 75,
																	menubar: false,
																	plugins: [
																		'advlist autolink lists link charmap print preview anchor',
																		'searchreplace visualblocks code fullscreen',
																		'insertdatetime image media table contextmenu paste code directionality',
																		'autoresize'
																	],
																	toolbar: tinyMce_toolbar,
																	content_css: '//www.tinymce.com/css/codepen.min.css',
																	statusbar: false,
																	browser_spellcheck: true,
																	entity_encoding: "raw"
																});
	}
	
	function wcfm_notice_manage_form_validate() {
		$is_valid = true;
		$('.wcfm-message').html('').removeClass('wcfm-error').slideUp();
		var title = $.trim($('#wcfm_notice_manage_form').find('#title').val());
		if(title.length == 0) {
			$is_valid = false;
			$('#wcfm_notice_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_notice_manage_messages.no_title).addClass('wcfm-error').slideDown();
			audio.play();
		}
		return $is_valid;
	}
	
	// Submit Notice
	$('#wcfm_notice_manager_submit_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_notice_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			var content = '';
			if( tinymce.get('wcfm_notice') != null ) content = tinymce.get('wcfm_notice').getContent({format: 'raw'});
			
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-notice-manage',
				wcfm_notice_manage_form : $('#wcfm_notice_manage_form').serialize(),
				content                  : content,
				status                   : 'submit'
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					if($response_json.status) {
						audio.play();
						$('#wcfm_notice_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
						  if( $response_json.redirect ) window.location = $response_json.redirect;	
						} );
					} else {
						audio.play();
						$('.wcfm-message').html('').removeClass('wcfm-success').slideUp();
						$('#wcfm_notice_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#notice_id').val($response_json.id);
					$('#wcfm-content').unblock();
				}
			});
		}
	});
} );