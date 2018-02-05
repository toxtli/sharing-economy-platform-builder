jQuery(document).ready(function($) {
	$('.img_tip').each(function() {
		$(this).qtip({
			content: $(this).attr('data-desc'),
			position: {
				my: 'center left',
				at: 'center right',
				viewport: $(window)
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
				classes: 'qtip-dark qtip-shadow qtip-rounded qtip-dc-css'
			}
		});
	});
	
	$('.dc_datepicker').each(function() {
	  $(this).datepicker({
      dateFormat : $(this).data('date_format'),
      changeMonth: true,
      changeYear: true
    });
  });
	
	$('.multi_input_holder').each(function() {
	  var multi_input_holder = $(this);
	  if(multi_input_holder.find('.multi_input_block').length == 1) multi_input_holder.find('.remove_multi_input_block').css('display', 'none'); 
    multi_input_holder.find('.multi_input_block').each(function() {
      if($(this)[0] != multi_input_holder.find('.multi_input_block:last')[0]) {
        $(this).find('.add_multi_input_block').remove();
      }
    });
    
    multi_input_holder.find('.add_multi_input_block').click(function() {
      var holder_id = multi_input_holder.attr('id');
      var holder_name = multi_input_holder.data('name');
      var multi_input_blockCount = multi_input_holder.data('length');
      multi_input_blockCount++;
      var multi_input_blockEle = multi_input_holder.find('.multi_input_block:first').clone(true);
      
      multi_input_blockEle.find('textarea,input:not(input[type=button],input[type=submit],input[type=checkbox],input[type=radio])').val('');
      multi_input_blockEle.find('.multi_input_block_element').each(function() {
        var ele = $(this);
        var ele_name = ele.data('name');
        ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
        ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
        if(ele.parent().hasClass('dc-wp-fields-uploader')) {
          var uploadEle = ele.parent();
          uploadEle.find('img').attr('src', '').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_display').addClass('placeHolder');
          uploadEle.find('.upload_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_button').show();
          uploadEle.find('.remove_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_remove_button').hide();
        }
        
        if(ele.hasClass('dc_datepicker')) {
          ele.removeClass('hasDatepicker').datepicker({
            dateFormat : ele.data('date_format'),
            changeMonth: true,
            changeYear: true
          });
        }
        
      });
      
      multi_input_blockEle.find('.add_multi_input_block').remove();
      multi_input_holder.append(multi_input_blockEle);
      multi_input_holder.find('.multi_input_block:last').append($(this));
      if(multi_input_holder.find('.multi_input_block').length > 1) multi_input_holder.find('.remove_multi_input_block').css('display', 'block');
      multi_input_holder.data('length', multi_input_blockCount);
      
      $('body').trigger('add_multi_input_block_clicked');
    });
    
    multi_input_holder.find('.remove_multi_input_block').click(function() {
      var addEle = multi_input_holder.find('.add_multi_input_block').clone(true);
      $(this).parent().remove();
      multi_input_holder.find('.add_multi_input_block').remove();
      multi_input_holder.find('.multi_input_block:last').append(addEle);
      if(multi_input_holder.find('.multi_input_block').length == 1) multi_input_holder.find('.remove_multi_input_block').css('display', 'none');
      
      $('body').trigger('remove_multi_input_block_clicked');
    });
  });
  if($('#is_other_admin_mail').is(':checked')) {
		var parrent_ele = $('#other_admin_mail').parent().parent();
		parrent_ele.show();
  }
  else {
  	var parrent_ele = $('#other_admin_mail').parent().parent();
		parrent_ele.hide();
  }
  if($('#is_override_form_heading').is(':checked')) {
		var parrent_ele = $('#custom_static_heading').parent().parent();
		parrent_ele.show();
  }
  else {
  	var parrent_ele = $('#custom_static_heading').parent().parent();
		parrent_ele.hide();
  }
  if($('#is_page_redirect').is(':checked')) {
		var parrent_ele = $('#redirect_page_id').parent().parent();
		parrent_ele.show();
  }
  else {
  	var parrent_ele = $('#redirect_page_id').parent().parent();
		parrent_ele.hide();
  }
  if($('#is_fileupload').is(':checked')) {
		var parrent_ele = $('#filesize_limit').parent().parent();
		parrent_ele.show();
  }
  else {
  	var parrent_ele = $('#filesize_limit').parent().parent();
		parrent_ele.hide();
  }
  
	$('#is_other_admin_mail').change(function() {
			if($(this).is(":checked")) {
				var parrent_ele = $('#other_admin_mail').parent().parent();
				parrent_ele.show('slow');
			}
			else {
				var parrent_ele = $('#other_admin_mail').parent().parent();
				parrent_ele.hide('slow');
			}
	});
	$('#is_override_form_heading').change(function() {
			if($(this).is(":checked")) {
				var parrent_ele = $('#custom_static_heading').parent().parent();
				parrent_ele.show('slow');
			}
			else {
				var parrent_ele = $('#custom_static_heading').parent().parent();
				parrent_ele.hide('slow');
			}
	});
	$('#is_page_redirect').change(function() {
			if($(this).is(":checked")) {
				var parrent_ele = $('#redirect_page_id').parent().parent();
				parrent_ele.show('slow');
			}
			else {
				var parrent_ele = $('#redirect_page_id').parent().parent();
				parrent_ele.hide('slow');
			}
	});
	$('#is_fileupload').change(function() {
			if($(this).is(":checked")) {
				var parrent_ele = $('#filesize_limit').parent().parent();
				parrent_ele.show('slow');
			}
			else {
				var parrent_ele = $('#filesize_limit').parent().parent();
				parrent_ele.hide('slow');
			}
	});
});
