jQuery(document).ready(function($) {	
	$('.more_orders').click(function (e) {
		e.preventDefault();
		var data = {
			action : 'withdrawal_more_orders',
			offset : $(this).attr('data-id')
		}	
		$.post(woocommerce_params.ajax_url, data, function(response) {
			var offsett = parseInt($('.more_orders').attr('data-id')) + 6 ;
			$('.more_orders').attr("data-id", offsett);
			$( ".get_paid_orders tr:last" ).after(response);
			checkbox_custome_design();
			var table_row =  $('.get_paid_orders tr').length;
			$( ".wcmp_withdrawal_now_showing").html(table_row-1);
			if((table_row-1) == $('#total_orders_count').val()) {
				$('.more_orders').hide();
			}
		});
	});
});