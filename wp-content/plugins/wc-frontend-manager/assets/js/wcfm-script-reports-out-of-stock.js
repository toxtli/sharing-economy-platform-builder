jQuery(document).ready(function($) {
	$wcfm_products_table = $('#wcfm-reports-out-of-stock').DataTable( {
		"processing": true,
		"serverSide": true,
		"bFilter"   : false,
		"dom"       : 'Bfrtip',
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"buttons"   : [
            				'print', 'pdf', 'excel', 'csv' 
        					],
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 4 },
										{ responsivePriority: 2 },
										{ responsivePriority: 3 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-reports-out-of-stock'
			},
			"complete" : function () {
				initiateTip();
				if (typeof intiateWCFMuQuickEdit !== 'undefined' && $.isFunction(intiateWCFMuQuickEdit)) intiateWCFMuQuickEdit();
				
				// Fire wcfm_products_table table refresh complete
				$( document.body ).trigger( 'updated_wcfm_products_table' );
			}
		}
	} );
	
	// Delete Product
	$( document.body ).on( 'updated_wcfm_products_table', function() {
		$('.wcfm_product_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm("Are you sure and want to delete this 'Product'?\nYou can't undo this action ...");
				if(rconfirm) deleteWCFMProduct($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMProduct(item) {
		jQuery('#wcfm-reports-out-of-stock_wrapper').block({
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
				jQuery('#wcfm-reports-out-of-stock_wrapper').unblock();
			}
		});
	}
	
} );