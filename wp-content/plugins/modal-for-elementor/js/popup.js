jQuery(document).ready(function($){
	$('.modal-popup').click(function(){
		var popup_id = $(this).attr('data-target');
		$(popup_id).modal('show');
		$('.modal-backdrop').hide();
	});
});