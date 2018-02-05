$product_type = '';	
$product_cat = '';
$product_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_products_table = $('#wcfm-products').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
			              { responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 7 },
										{ responsivePriority: 6 },
										{ responsivePriority: 4 },
										{ responsivePriority: 5 },
										{ responsivePriority: 2 },
										{ responsivePriority: 7 },
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
										{ "targets": 7, "orderable" : false },
										{ "targets": 8, "orderable" : false },
										{ "targets": 9, "orderable" : false },
										{ "targets": 10, "orderable" : false },
										{ "targets": 11, "orderable" : false }
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-products',
				d.product_type     = $product_type,
				d.product_cat      = $product_cat,
				d.product_vendor   = $product_vendor,
				d.product_status   = GetURLParameter( 'product_status' )
			},
			"complete" : function () {
				initiateTip();
				if (typeof intiateWCFMuQuickEdit !== 'undefined' && $.isFunction(intiateWCFMuQuickEdit)) intiateWCFMuQuickEdit();
				
				// Fire wcfm-products table refresh complete
				$( document.body ).trigger( 'updated_wcfm-products' );
			}
		}
	} );
	
	if( $('#dropdown_product_type').length > 0 ) {
		$('#dropdown_product_type').on('change', function() {
		  $product_type = $('#dropdown_product_type').val();
		  $wcfm_products_table.ajax.reload();
		});
	}
	
	if( $('.dropdown_product_cat').length > 0 ) {
		$('.dropdown_product_cat').on('change', function() {
			$product_cat = $('.dropdown_product_cat').val();
			$wcfm_products_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$product_vendor = $('#dropdown_vendor').val();
			$wcfm_products_table.ajax.reload();
		}).select2();
	}
	
	// Delete Product
	$( document.body ).on( 'updated_wcfm-products', function() {
		$('.wcfm_product_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.product_delete_confirm);
				if(rconfirm) deleteWCFMProduct($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMProduct(item) {
		jQuery('#wcfm-products_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'delete_wcfm_product',
			proid : item.data('proid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_products_table) $wcfm_products_table.ajax.reload();
				jQuery('#wcfm-products_wrapper').unblock();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-products', function() {
		$.each(wcfm_products_screen_manage, function( column, column_val ) {
		  $wcfm_products_table.column(column).visible( false );
		} );
	});
	
} );