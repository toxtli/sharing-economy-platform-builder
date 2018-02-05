jQuery(document).ready(function($) {
  $('#_subscription_period').change(function() {
  	$('.subscription_length_ele').addClass('wcfm_ele_hide wcfm_title_hide');
  	if( $('#product_type').val() == 'subscription' ) {
  		$('.subscription_length_' + $(this).val()).removeClass('wcfm_ele_hide wcfm_title_hide');
  	}
  }).change();
  
  $('#product_type').change(function() {
  	$('#_subscription_period').change();	
  });
});