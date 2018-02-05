jQuery(document).ready( function($) {
	// Collapsible
  $('.page_collapsible').collapsible({
		defaultOpen: 'wcfm_settings_dashboard_head',
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
			collapsHeight += $(this).height() + 20;
		}
	});  
	
	if( $("#timezone").length > 0 ) {
		$("#timezone").select2({
			placeholder: wcfm_dashboard_messages.choose_select2 + ' ...'
		});
	}
	
	if( $(".wcfm_product_type_categories").length > 0 ) {
		$(".wcfm_product_type_categories").select2({
			placeholder: wcfm_dashboard_messages.choose_select2 + ' ...'
		});
	}
	
	if( $(".wcfm_product_type_toolset_fields").length > 0 ) {
		$(".wcfm_product_type_toolset_fields").select2({
			placeholder: wcfm_dashboard_messages.choose_select2 + ' ...'
		});
	}
	
	// WCMp paymode settings options
	if( $('#_vendor_payment_mode').length > 0 ) {
		$('#_vendor_payment_mode').change(function() {
			$payment_mode = $(this).val();
			$('.paymode_field').hide();
			$('.paymode_' + $payment_mode).show();
			resetCollapsHeight($('#_vendor_payment_mode'));
		}).change();
	}
	
	// WC Vendors MangoPay paymode settings options
	if( $('#vendor_account_type').length > 0 ) {
		$('#vendor_account_type').change(function() {
			$payment_mode = $(this).val();
			$('.mangopay_acc_type').addClass('wcfm_ele_hide');
			$('.mangopay_acc_type_' + $payment_mode).removeClass('wcfm_ele_hide');
			resetCollapsHeight($('#vendor_account_type'));
		}).change();
	}
	
	// TinyMCE intialize - Description
	if( $('#shop_description').length > 0 ) {
		if( $('#shop_description').hasClass('rich_editor') ) {
			var descTinyMCE = tinymce.init({
																		selector: '#shop_description',
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
	}
	
	// Style Settings Reset to Default
	if( $('#wcfm_color_setting_reset_button').length > 0 ) {
		$('#wcfm_color_setting_reset_button').click(function(event) {
			event.preventDefault();
			$.each(wcfm_color_setting_options, function( wcfm_color_setting_option, wcfm_color_setting_option_values ) {
				//$('#' + wcfm_color_setting_option_values.name).val( wcfm_color_setting_option_values.default );	
				$('#' + wcfm_color_setting_option_values.name).iris( 'color', wcfm_color_setting_option_values.default );
			} );
			$('#wcfm_settings_save_button').click();
		});
	}
	
	// Save Settings
	$('#wcfm_settings_save_button').click(function(event) {
	  event.preventDefault();
	  
	  var profile = '';
	  if( $('#shop_description').hasClass('rich_editor') ) {
			if( typeof tinymce != 'undefined' ) profile = tinymce.get('shop_description').getContent({format: 'raw'});
		} else {
			profile = $('#shop_description').val();
		}
  
	  // Validations
	  $('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
	  $wcfm_is_valid_form = true;
	  $( document.body ).trigger( 'wcfm_form_validate' );
	  $is_valid = $wcfm_is_valid_form;
	  
	  if($is_valid) {
			$('#wcfm_settings_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-settings',
				wcfm_settings_form : $('#wcfm_settings_form').serialize(),
				profile            : profile
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_settings_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
						if( $response_json.file ) $('#wcfm_custom_css-css').attr( 'href', $response_json.file );
					} else {
						audio.play();
						$('#wcfm_settings_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_settings_form').unblock();
				}
			});	
		}
	});
	
	$('.multi_input_holder').each(function() {
	  var multi_input_holder = $(this);
	  addMultiInputProperty(multi_input_holder);
	});
	
	function addMultiInputProperty(multi_input_holder) {
		var multi_input_limit = multi_input_holder.data('limit');
		if( typeof multi_input_limit == 'undefined' ) multi_input_limit = -1;
	  if(multi_input_holder.children('.multi_input_block').length == 1) multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
	  if( multi_input_holder.children('.multi_input_block').length == multi_input_limit )  multi_input_holder.find('.add_multi_input_block').hide();
	  else multi_input_holder.find('.add_multi_input_block').show();
    multi_input_holder.children('.multi_input_block').each(function() {
      if($(this)[0] != multi_input_holder.children('.multi_input_block:last')[0]) {
        $(this).children('.add_multi_input_block').remove();
      }
    });
    
    multi_input_holder.children('.multi_input_block').children('.add_multi_input_block').off('click').on('click', function() {
      var holder_id = multi_input_holder.attr('id');
      var holder_name = multi_input_holder.data('name');
      var multi_input_blockCount = multi_input_holder.data('length');
      multi_input_blockCount++;
      var multi_input_blockEle = multi_input_holder.children('.multi_input_block:first').clone(false);
      
      multi_input_blockEle.find('textarea,input:not(input[type=button],input[type=submit],input[type=checkbox],input[type=radio])').val('');
      multi_input_blockEle.find('input[type=checkbox]').attr('checked', false);
      multi_input_blockEle.find('.select2-container').remove();
      multi_input_blockEle.find('select').select2();
      multi_input_blockEle.find('select').select2('destroy');
      multi_input_blockEle.children('.wcfm-wp-fields-uploader,.multi_input_block_element:not(.multi_input_holder)').each(function() {
        var ele = $(this);
        var ele_name = ele.data('name');
				ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
				ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
      });
      
      // Nested multi-input block property
      multi_input_blockEle.children('.multi_input_holder').each(function() {
        setNestedMultiInputIndex($(this), holder_id, holder_name, multi_input_blockCount);
      });
       
      
      multi_input_blockEle.children('.remove_multi_input_block').off('click').on('click', function() {
      	var remove_ele_parent = $(this).parent().parent();
				var addEle = remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').clone(true);
				$(this).parent().remove();
				remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').remove();
				remove_ele_parent.children('.multi_input_block:last').append(addEle);
				if( remove_ele_parent.children('.multi_input_block').length == multi_input_limit ) remove_ele_parent.find('.add_multi_input_block').hide();
				else remove_ele_parent.find('.add_multi_input_block').show();
				if(remove_ele_parent.children('.multi_input_block').length == 1) remove_ele_parent.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
			});
      
      multi_input_blockEle.children('.add_multi_input_block').remove();
      multi_input_holder.append(multi_input_blockEle);
      multi_input_holder.children('.multi_input_block:last').append($(this));
      if(multi_input_holder.children('.multi_input_block').length > 1) multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'block');
      if( multi_input_holder.children('.multi_input_block').length == multi_input_limit ) multi_input_holder.find('.add_multi_input_block').hide();
      else multi_input_holder.find('.add_multi_input_block').show();
      multi_input_holder.data('length', multi_input_blockCount);
      
      // Fields Type Property
			multi_input_holder.find('.field_type_options').each(function() {
				$(this).off('change').on('change', function() {
					$(this).parent().find('.field_type_select_options').hide();
					if( $(this).val() == 'select' ) $(this).parent().find('.field_type_select_options').show();
				} ).change();
			} );
			
			// Group Name
			multi_input_holder.find('.custom_field_is_group').each( function() {
				$(this).change( function() {
					if( $(this).is(':checked') ) {
						$(this).parent().find('.custom_field_is_group_name').css('visibility', 'visible');
					} else {
						$(this).parent().find('.custom_field_is_group_name').css('visibility', 'hidden');
					}
				} ).change();
			} );
			
			// Fields Collaper
			multi_input_holder.find('.fields_collapser').each(function() {
				$(this).off('click').on('click', function() {
				  $(this).parent().parent().parent().find('.multi_input_holder').toggleClass('wcfm_ele_hide');
				  $(this).toggleClass('fa-arrow-circle-o-up');
				  resetCollapsHeight(multi_input_holder);
				} );
			} );
			
			if( multi_input_holder.parent().hasClass('multi_input_block ') ) {
				resetCollapsHeight(multi_input_holder.parent().parent());
			} else {
				resetCollapsHeight(multi_input_holder);
			}
    });
    
    if(!multi_input_holder.hasClass('multi_input_block_element')) {
			//multi_input_holder.children('.multi_input_block').css('padding-bottom', '40px');
		}
		if(multi_input_holder.children('.multi_input_block').children('.multi_input_holder').length > 0) {
			//multi_input_holder.children('.multi_input_block').css('padding-bottom', '40px');
		}
    
    multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').off('click').on('click', function() {
    	var remove_ele_parent = $(this).parent().parent();
      var addEle = remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').clone(true);
      $(this).parent().remove();
      remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').remove();
      remove_ele_parent.children('.multi_input_block:last').append(addEle);
      if(remove_ele_parent.children('.multi_input_block').length == 1) remove_ele_parent.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
      if( remove_ele_parent.children('.multi_input_block').length == multi_input_limit ) remove_ele_parent.find('.add_multi_input_block').hide();
      else remove_ele_parent.find('.add_multi_input_block').show();
    });
    
    // Fields Type Property
		multi_input_holder.find('.field_type_options').each(function() {
			$(this).off('change').on('change', function() {
				$(this).parent().find('.field_type_select_options').hide();
				if( $(this).val() == 'select' ) $(this).parent().find('.field_type_select_options').show();
			} ).change();
		} );
		
		// Group Name
		multi_input_holder.find('.custom_field_is_group').each( function() {
			$(this).change( function() {
				if( $(this).is(':checked') ) {
					$(this).parent().find('.custom_field_is_group_name').css('visibility', 'visible');
				} else {
					$(this).parent().find('.custom_field_is_group_name').css('visibility', 'hidden');
				}
			} ).change();
		} );
  }
  
  // Fields Collapser
	$('.wcfm_title').find('.fields_collapser').each(function() {
		$(this).addClass('fa-arrow-circle-o-up');
		$(this).off('click').on('click', function() {
			$(this).parent().parent().parent().find('.multi_input_holder').toggleClass('wcfm_ele_hide');
			$(this).toggleClass('fa-arrow-circle-o-up');
			resetCollapsHeight($(this).parent().parent().parent().parent().parent().find('.multi_input_holder'));
		} ).click();
	} );
  
  function resetMultiInputIndex(multi_input_holder) {
  	var holder_id = multi_input_holder.attr('id');
		var holder_name = multi_input_holder.data('name');
		var multi_input_blockCount = 0;
		
		multi_input_holder.find('.multi_input_block').each(function() {
			$(this).children('.wcfm-wp-fields-uploader,.multi_input_block_element:not(.multi_input_holder)').each(function() {
				var ele = $(this);
				var ele_name = ele.data('name');
				var multiple = ele.attr('multiple');
				if (typeof multiple !== typeof undefined && multiple !== false) {
					ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+'][]');
				} else {
					ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
				}
				ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
			});
			multi_input_blockCount++;
		});
  }
  
  function setNestedMultiInputIndex(nested_multi_input, holder_id, holder_name, multi_input_blockCount) {
		nested_multi_input.children('.multi_input_block:not(:last)').remove();
		var multi_input_id = nested_multi_input.attr('id');
		multi_input_id = multi_input_id.replace(holder_id + '_', '');
		var multi_input_id_splited = multi_input_id.split('_');
		var multi_input_name = '';
		for(var i = 0; i < (multi_input_id_splited.length -1); i++) {
		 if(multi_input_name != '') multi_input_name += '_';
		 multi_input_name += multi_input_id_splited[i];
		}
		nested_multi_input.attr('data-name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']');
		nested_multi_input.attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount);
		nested_multi_input.children('.multi_input_block').children('.wcfm-wp-fields-uploader,.multi_input_block_element:not(.multi_input_holder)').each(function() {
		  var ele = $(this);
		  var ele_name = ele.data('name');
			var multiple = ele.attr('multiple');
			if (typeof multiple !== typeof undefined && multiple !== false) {
				ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+'][0]['+ele_name+'][]');
			} else {
				ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+'][0]['+ele_name+']');
			}
			ele.attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount + '_' + ele_name + '_0');
		});
		
		addMultiInputProperty(nested_multi_input);
		
		if(nested_multi_input.children('.multi_input_block').children('.multi_input_holder').length > 0) nested_multi_input.children('.multi_input_block').css('padding-bottom', '40px');
		
		nested_multi_input.children('.multi_input_block').children('.multi_input_holder').each(function() {
			setNestedMultiInputIndex($(this), holder_id+'_'+multi_input_name+'_0', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']', 0);
		});
	}
	
});