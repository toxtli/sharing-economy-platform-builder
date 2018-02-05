jQuery(document).ready(function($) {
		
	$start_date = '';
	$end_date = '';
	
	$wcfm_payments_table = $('#wcfm-payments').DataTable( {
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
										{ responsivePriority: 5 },
										{ responsivePriority: 6 },
										{ responsivePriority: 4 },
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 2 }
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
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-payments',
				d.start_date   = $start_date,
				d.end_date     = $end_date
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-payments table refresh complete
				$( document.body ).trigger( 'updated_wcfm-payments' );
			}
		}
	} );
	
	$( "#payment_start_date_filter" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'dd-mm-yy',
		onClose: function( selectedDate ) {
			$( "#payment_end_date_filter" ).datepicker( "option", "minDate", selectedDate );
		}
	}).change(function() {
		$start_date = $(this).val();
		$('.trans_start_date').text($start_date);
		$wcfm_payments_table.ajax.reload();
	});
	$( "#payment_end_date_filter" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'dd-mm-yy',
		onClose: function( selectedDate ) {
			$( "#payment_start_date_filter" ).datepicker( "option", "maxDate", selectedDate );
		}
	}).change(function() {
		$end_date = $(this).val();
		$('.trans_end_date').text($end_date);
		$wcfm_payments_table.ajax.reload();
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
} );