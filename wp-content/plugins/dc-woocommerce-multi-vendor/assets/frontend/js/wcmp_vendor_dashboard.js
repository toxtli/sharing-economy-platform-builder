jQuery(document).ready(function($){
	$("body").on("click", ".wcmp_delate_announcements_dashboard", function(e) {
			var post_id = $(this).attr("data-element");
			var element_to_be_refrash = $(this).parent();	
			var lodder_parent = $(this).parent().parent();
			var lodder = lodder_parent.find('.ajax_loader_class_msg');
			lodder.show();			
			var data = {
				action : 'wcmp_dismiss_dashboard_announcements',
				post_id : post_id
			}
			$.post(woocommerce_params.ajax_url, data, function(responsee) { 
				element_to_be_refrash.html(responsee);			
			});
			lodder.hide();			
			e.preventDefault();
	});
		
	$(".wcmp_frontend_sale_show_more_button").click(function(e) {
		var lodder_parent = $(this).parent();	
		var ajax_loader_class = $(lodder_parent).find('.ajax_loader_class');		
		$(ajax_loader_class).show();		
		var current_page = '';
		var next_page = '';
		var total_page = '';
		var today_or_weekly = '';
		var myaction = '';
		var perpagedata = 6;
		var tobeappend = '';
		var first_number = '';
		
		var button_type = $(this).attr('element-data');
		var mybutton = $(this);
		if( button_type == 'sale_weekly_more') {
			current_page = $("#week_sale_current_page").val();
			$("#week_sale_current_page").val(parseInt(current_page)+1);
			next_page = $("#week_sale_next_page").val();
			$("#week_sale_next_page").val(parseInt(next_page)+1);
			total_page = $("#week_sale_total_page").val();
			today_or_weekly = 'weekly';
			myaction = 'wcmp_frontend_sale_get_row';
			tobeappend = 'wcmp_sale_report_table_week';
			first_number = 'wcmp_front_count_first_num_week';
			
		}
		else if(button_type == 'sale_today_more') {
			current_page = $("#today_sale_current_page").val();
			$("#today_sale_current_page").val(parseInt(current_page)+1);
			next_page = $("#today_sale_next_page").val();
			$("#today_sale_next_page").val(parseInt(next_page)+1);
			total_page = $("#today_sale_total_page").val();
			today_or_weekly = 'today';
			myaction = 'wcmp_frontend_sale_get_row';
			tobeappend = 'wcmp_sale_report_table_today';
			first_number = 'wcmp_front_count_first_num_today';
			
		}
		var data = {
			action : myaction,
			current_page : current_page,
			next_page : next_page,
			today_or_weekly : today_or_weekly,
			total_page : total_page,
			perpagedata : perpagedata			
		}
		$.post(woocommerce_params.ajax_url, data, function(responsee) {		 		 
			$('#'+tobeappend+' tr:last').after(responsee);		 	 
			if((parseInt(next_page) + 1) > parseInt(total_page)) {
			  $(mybutton).remove();
			}
			var count = $('#'+tobeappend+' tr').length;
			count = parseInt(count) - 1;
			$("."+first_number).html(count);
		});
		$(ajax_loader_class).hide();
	});
	$(".wcmp_frontend_pending_shipping_show_more_button").click(function(e) {
		var lodder_parent = $(this).parent();	
		var ajax_loader_class = $(lodder_parent).find('.ajax_loader_class');		
		$(ajax_loader_class).show();		
		var current_page = '';
		var next_page = '';
		var total_page = '';
		var today_or_weekly = '';
		var myaction = '';
		var perpagedata = 6;
		var tobeappend = '';
		var first_number = '';
		
		var button_type = $(this).attr('element-data');
		var mybutton = $(this);
		if( button_type == 'pending_shipping_weekly_more') {
			current_page = $("#week_pending_shipping_current_page").val();
			$("#week_pending_shipping_current_page").val(parseInt(current_page)+1);
			next_page = $("#week_pending_shipping_next_page").val();
			$("#week_pending_shipping_next_page").val(parseInt(next_page)+1);
			total_page = $("#week_pending_shipping_total_page").val();
			today_or_weekly = 'weekly';
			myaction = 'wcmp_frontend_pending_shipping_get_row';
			tobeappend = 'wcmp_pending_shipping_report_table_week';
			first_number = 'wcmp_front_count_first_num_week_ps';
			
		}
		else if(button_type == 'pending_shipping_today_more') {
			current_page = $("#today_pending_shipping_current_page").val();
			$("#today_pending_shipping_current_page").val(parseInt(current_page)+1);
			next_page = $("#today_pending_shipping_next_page").val();
			$("#today_pending_shipping_next_page").val(parseInt(next_page)+1);
			total_page = $("#today_pending_shipping_total_page").val();
			today_or_weekly = 'today';
			myaction = 'wcmp_frontend_pending_shipping_get_row';
			tobeappend = 'wcmp_pending_shipping_report_table_today';
			first_number = 'wcmp_front_count_first_num_today_ps';
			
		}
		var data = {
			action : myaction,
			current_page : current_page,
			next_page : next_page,
			today_or_weekly : today_or_weekly,
			total_page : total_page,
			perpagedata : perpagedata			
		}
		$.post(woocommerce_params.ajax_url, data, function(responsee) {		 		 
			$('#'+tobeappend+' tr:last').after(responsee);		 	 
			if((parseInt(next_page) + 1) > parseInt(total_page)) {
			  $(mybutton).remove();
			}
			var count = $('#'+tobeappend+' tr').length;
			count = parseInt(count) - 1;
			$("."+first_number).html(count);
		});
		$(ajax_loader_class).hide();
	});

});
