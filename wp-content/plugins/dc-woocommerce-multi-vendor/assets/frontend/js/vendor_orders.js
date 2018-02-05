jQuery(document).ready(function($) {		
	$('.wcmp_download_order_submit').on( "click", function(e) {
		e.preventDefault();
		var data_total = $(this).attr('data-total');
		var data_shown = $(this).attr('data-shown');
		var data_type = $(this).attr('data-type');
		
		var this_button = $(this);
		
		if(data_type == 'all') {
			
			var data_to_show = wcmp_vendor_all_orders_array.slice(data_shown, (parseInt(data_shown)+6));
			
		} else if(data_type == 'processing') {
			
			var data_to_show = wcmp_vendor_processing_orders_array.slice(data_shown, (parseInt(data_shown)+6));
			
		} else if(data_type == 'completed') {
			
			var data_to_show = wcmp_vendor_completed_orders_array.slice(data_shown, (parseInt(data_shown)+6));
			
		} else {
			return;
		}
		
		var data = {
			action : 'get_more_orders',
			data_to_show : data_to_show,
			order_status : data_type
		}	
		
		$.post(woocommerce_params.ajax_url, data, function(response) {
			var data_shown = parseInt(data_shown) + 6;
			this_button.attr("data-shown", parseInt(this_button.attr("data-shown"))+6);
			if(data_type == 'all') {
				$( ".wcmp_order_all_table tr:last" ).after(response);
				checkbox_custome_design();
				var table_row =  $('.wcmp_order_all_table tr').length;
				$( ".wcmp_all_now_showing").html(table_row-1);
				if((table_row - 1) == data_total) {
					this_button.hide();
				}
			} else if(data_type == 'processing') {
			
				$( ".wcmp_order_processing_table tr:last" ).after(response);
				checkbox_custome_design();
				var table_row =  $('.wcmp_order_processing_table tr').length;
				$( ".wcmp_processing_now_showing").html(table_row-1);
				if((table_row - 1) == data_total) {
					this_button.hide();
				}
			
			} else if(data_type == 'completed') {
			
				$( ".wcmp_order_completed_table tr:last" ).after(response);
				checkbox_custome_design();
				var table_row =  $('.wcmp_order_completed_table tr').length;
				$( ".wcmp_completed_now_showing").html(table_row-1);
				if((table_row - 1) == data_total) {
					this_button.hide();
				};
			
			} else {
				return;
			}
		}); 
	});
	
	$(document).on( "click", ".submit_tracking", function(e) {
		e.preventDefault();
                var $this = $(this);
                var order_id = $(this).attr('data-id');
                var tracking_url = $('#shipping_tracking_url_'+order_id).val();
                if(tracking_url == '') return false;
                var tracking_id = $('#shipping_tracking_id_'+order_id).val();
                if(tracking_id == '') return false;
                var selector = $('.mark_ship_'+order_id);
		selector.find('img').attr('title', 'Shipped');
		thisimg = selector.find('img');
		thisimg.attr('src', wcmp_mark_shipped_text.image);
		selector.css( "pointer-events",'none' );
		selector.css( "cursor",'default' );		
		var data = {
			action : 'order_mark_as_shipped',
			order_id : order_id,
			user_id : selector.attr('data-user'),
                        tracking_url : tracking_url,
                        tracking_id : tracking_id
		}
		$(this).toggleClass('submit_tracking_loader').blur();	
		$.post(woocommerce_params.ajax_url, data, function(response) {
				$this.removeClass('submit_tracking_loader').blur();
				//console.log(response);
                                $('.shipping_msg_'+order_id).html(wcmp_mark_shipped_text.text);
                                setTimeout(function(){
                                    $('.popup-exit').click();
                                }, 3000);
		});
	});
	
	if($('#total_orders_count').val() == 0) {
		$('.more_orders').hide();
	}
	
	$('button[name=download_all_all_csv]').on("click", function (e) {
		e.preventDefault();
		var parent_ele = $(this).parent().parent();
		var target_ele = parent_ele.find(".wcmp_table_holder");
		var length = 0;
		$(".wcmp_table_holder .select_all").each(function (e) {
			if ($(this).is(':checked')) {
				length = parseInt(length)+1;
			}				
		});
		if(length == 0) {
			jQuery('.select_all').each(function () {
				jQuery(this).prop('checked', true);
				jQuery(this).parent().find('.fa').removeClass('fa-square-o');
				jQuery(this).parent().find('.fa').addClass('fa-check-square-o');
			});
		}
		$('form[name=wcmp_vendor_dashboard_all_stat_export]').submit();
	});
	
	$('button[name=download_processing_all_csv]').on("click", function (e) {
		e.preventDefault();
		var parent_ele = $(this).parent().parent();
		var target_ele = parent_ele.find(".wcmp_table_holder");
		var length = 0;
		$(".wcmp_table_holder .select_processing").each(function (e) {
			if ($(this).is(':checked')) {
				length = parseInt(length)+1;
			}				
		});
		if(length == 0) {
			jQuery('.select_processing').each(function () {
				jQuery(this).prop('checked', true);
				jQuery(this).parent().find('.fa').removeClass('fa-square-o');
				jQuery(this).parent().find('.fa').addClass('fa-check-square-o');
			});
		}
		$('form[name=wcmp_vendor_dashboard_processing_stat_export]').submit();
	});
	
	$('button[name=download_completed_all_csv]').on("click", function (e) {
		e.preventDefault();
		var parent_ele = $(this).parent().parent();
		var target_ele = parent_ele.find(".wcmp_table_holder");
		var length = 0;
		$(".wcmp_table_holder .select_completed").each(function (e) {
			if ($(this).is(':checked')) {
				length = parseInt(length)+1;
			}				
		});
		if(length == 0) {
			jQuery('.select_completed').each(function () {
				jQuery(this).prop('checked', true);
				jQuery(this).parent().find('.fa').removeClass('fa-square-o');
				jQuery(this).parent().find('.fa').addClass('fa-check-square-o');
			});
		}
		$('form[name=wcmp_vendor_dashboard_completed_stat_export]').submit();
	});
	
	
});

function geturlvalue(who, order_id) {
    jQuery('#shipping_tracking_url_'+order_id).val(jQuery(who).val());
}

function getidvalue(who, order_id) {
    jQuery('#shipping_tracking_id_'+order_id).val(jQuery(who).val());
}
