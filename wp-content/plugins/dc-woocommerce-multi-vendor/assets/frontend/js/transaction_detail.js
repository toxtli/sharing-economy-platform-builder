jQuery(document).ready(function($) {
	$( "#wcmp_from_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
	$( "#wcmp_to_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
	$('.more_transactions').click(function (e) {
		e.preventDefault();
		var data_total = $(this).attr('data-total');
		var data_shown = $(this).attr('data-shown');
		
		var data_to_show = wcmp_vendor_transactions_array.slice(data_shown, (parseInt(data_shown)+6));
		var data = {
			action : 'show_more_transaction',
			data_to_show : data_to_show 
		}	
		$.post(woocommerce_params.ajax_url, data, function(response) {
			var offsett = parseInt($('.more_transactions').attr('data-shown')) + 6 ;
			$('.more_transactions').attr("data-shown", offsett);
			$( ".get_wcmp_transactions tr:last" ).after(response);
			checkbox_custome_design();
			var table_row =  $('.get_wcmp_transactions tr').length;
			$( ".wcmp_withdrawal_now_showing").html(table_row-1);
			if((table_row-1) >= data_total) {
				$('.more_transactions').hide();
			}
		});
	});
});