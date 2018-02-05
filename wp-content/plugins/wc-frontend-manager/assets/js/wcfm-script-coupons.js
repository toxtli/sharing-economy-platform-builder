$wcfm_coupons_table = '';
$coupon_type = '';	
	
jQuery(document).ready(function($) {
	
	$wcfm_coupons_table = $('#wcfm-coupons').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 2 },
										{ responsivePriority: 5 },
										{ responsivePriority: 4 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }, 
										{ "targets": 5, "orderable" : false } 
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-coupons',
				d.coupon_type     = $coupon_type
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-coupons table refresh complete
				$( document.body ).trigger( 'updated_wcfm-coupons' );
			}
		}
	} );
	
} );