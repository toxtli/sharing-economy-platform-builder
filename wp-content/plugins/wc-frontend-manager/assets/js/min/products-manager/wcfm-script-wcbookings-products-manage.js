jQuery(document).ready(function($) {
  $('#_wc_booking_duration_type').change(function() {
    if( $(this).val() == 'customer' ) {
    	$('.duration_type_customer_ele').show();
    } else {
    	$('.duration_type_customer_ele').hide();
    }
  }).change();
  
  $('#_wc_booking_user_can_cancel').change(function() {
    if( $(this).is(':checked')) {
    	$('.can_cancel_ele').show();
    	resetCollapsHeight($('#_wc_booking_user_can_cancel'));
    } else {
    	$('.can_cancel_ele').hide();
    }
  } ).change();
  
  $('#_wc_booking_duration_unit').change(function() {
  	$('._wc_booking_buffer_period_unit').html($(this).val() + 's ');
  }).change();
});