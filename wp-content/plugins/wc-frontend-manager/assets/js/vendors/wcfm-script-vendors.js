$wcfm_vendors_table = '';
$report_vendor = '';
$report_for = '';	
	
jQuery(document).ready(function($) {
	
	$wcfm_vendors_table = $('#wcfm-vendors').DataTable( {
		"processing": true,
		"serverSide": true,
		"bFilter"   : false,
		"responsive": true,
		"dom"       : 'Bfrtip',
		"language"  : $.parseJSON(dataTables_language),
		"buttons"   : [
            				'print', 'pdf', 'excel', 'csv' 
        					],
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 4 },
										{ responsivePriority: 3 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 3 },
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
				d.action         = 'wcfm_ajax_controller',
				d.controller     = 'wcfm-vendors',
				d.report_vendor  = $report_vendor,
				d.report_for     = $report_for
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-vendors table refresh complete
				$( document.body ).trigger( 'updated_wcfm-vendors' );
			}
		}
	} );
	
	if( $('#dropdown_report_filter').length > 0 ) {
		$('#dropdown_report_filter').on('change', function() {
		  $report_for = $('#dropdown_report_filter').val();
		  $wcfm_vendors_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$report_vendor = $('#dropdown_vendor').val();
			$wcfm_vendors_table.ajax.reload();
		}).select2();
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-vendors', function() {
		$.each(wcfm_vendors_screen_manage, function( column, column_val ) {
		  $wcfm_vendors_table.column(column).visible( false );
		} );
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
} );