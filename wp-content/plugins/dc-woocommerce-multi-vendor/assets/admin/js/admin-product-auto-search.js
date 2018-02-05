jQuery(document).ready(function($) {	
	$('#titlediv #titlewrap').append('<div id="wcmp_auto_suggest_product_title"></div>');		
	$('#titlediv #titlewrap #title').keyup(function(e){
		var strtitle = $(this).val();
		if(strtitle.length >= 3) {
			var data = {
				action : 'wcmp_auto_search_product',
				protitle : strtitle				
		  }
		  $.post(ajaxurl, data, function(responsee) {
		  	$('#wcmp_auto_suggest_product_title').html(responsee);		  		
		  });
		}
		else if(strtitle.length == 0){
			$('#wcmp_auto_suggest_product_title').html('');
		}
	});
		
	$('#titlediv #titlewrap #title').focusout(function(e){		
		jQuery(window).unbind('beforeunload');
	});
	$('body').on('click','wcmp_auto_suggest_product_title ul li a', function(e){
			
	});
	var title = $('#titlediv #titlewrap #title').val();
	if(title != undefined && title != '') {
		title = title.replace('(Copy)', '');
		$('#titlediv #titlewrap #title').val(title);
	}	
});
