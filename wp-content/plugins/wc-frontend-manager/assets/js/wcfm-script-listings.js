$wcfm_listings_table = '';
	
jQuery(document).ready(function($) {
	
	$wcfm_listings_table = $('#wcfm-listings').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 2 },
										{ responsivePriority: 2 },
										{ responsivePriority: 4 },
										{ responsivePriority: 2 },
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
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-listings',
				d.listing_status   = GetURLParameter( 'listing_status' )
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-listings table refresh complete
				$( document.body ).trigger( 'updated_wcfm-listings' );
			}
		}
	} );
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-listings', function() {
		$.each(wcfm_listings_screen_manage, function( column, column_val ) {
		  $wcfm_listings_table.column(column).visible( false );
		} );
	});
	
} );