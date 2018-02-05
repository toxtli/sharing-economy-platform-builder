$wcfm_orders_table = '';
$order_status = '';	
$filter_by_date = '';
$commission_status = '';

jQuery(document).ready(function($) {
		
	// Dummy Mark Complete Dummy
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_order_mark_complete_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.wcfmu_upgrade_notice );
				return false;
			});
		});
	});
	
	// Invoice Dummy
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_invoice_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.pdf_invoice_upgrade_notice );
				return false;
			});
		});
	});
	
	// Invoice dummy - vendor
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_invoice_vendor_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.wcfmu_missing_feature );
				return false;
			});
		});
	});
	
	// Mark Shipped dummy - vendor
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_wcvendors_order_mark_shipped_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.wcfmu_missing_feature );
				return false;
			});
		});
	});
	
	$wcfm_orders_table = $('#wcfm-orders').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"dom"       : 'Bfrtip',
		"language"  : $.parseJSON(dataTables_language),
    "buttons"   : [
            				'print', 'pdf', 'excel', 'csv' 
        					],
		"columns"   : [
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 4 },
										{ responsivePriority: 5 },
										{ responsivePriority: 3 },
										{ responsivePriority: 3 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }, 
										{ "targets": 5, "orderable" : false },
										{ "targets": 6, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action            = 'wcfm_ajax_controller',
				d.controller        = 'wcfm-orders',
				d.order_status      = GetURLParameter( 'order_status' ),
				d.m                 = $filter_by_date,
				d.commission_status = $commission_status
			},
			"complete" : function () {
				initiateTip();
				
				$('.show_order_items').click(function(e) {
					e.preventDefault();
					$(this).next('div.order_items').toggleClass( "order_items_visible" );
					return false;
				});
				
				// Fire wcfm-orders table refresh complete
				$( document.body ).trigger( 'updated_wcfm-orders' );
			}
		}
	} );
	
	// Order Table auto Refresher
	var orderTableRefrsherTime = '';
	function orderTableRefrsher() {
		clearTimeout(orderTableRefrsherTime);
		orderTableRefrsherTime = setTimeout(function() {
			$wcfm_orders_table.ajax.reload();
			orderTableRefrsher();
		}, 30000 );
	}
	orderTableRefrsher();
	
	// Mark Order as Completed
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_order_mark_complete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm( wcfm_dashboard_messages.order_mark_complete_confirm );
				if(rconfirm) markCompleteWCFMOrder($(this));
				return false;
			});
		});
	});
	
	function markCompleteWCFMOrder(item) {
		clearTimeout(orderTableRefrsherTime);
		$('#wcfm-orders_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'wcfm_order_mark_complete',
			orderid : item.data('orderid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_orders_table.ajax.reload();
				$('#wcfm-orders_wrapper').unblock();
				orderTableRefrsher();
			}
		});
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$.each(wcfm_orders_screen_manage, function( column, column_val ) {
		  $wcfm_orders_table.column(column).visible( false );
		} );
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
} );