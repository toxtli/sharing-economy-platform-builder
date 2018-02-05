$wcfm_bookings_table = '';
$booking_status = '';	
$booking_filter = '';	

jQuery(document).ready(function($) {
	
	$wcfm_bookings_table = $('#wcfm-bookings').DataTable( {
		"processing": true,
		"serverSide": true,
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 4 },
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
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-bookings',
				d.booking_status = GetURLParameter( 'booking_status' ),
				d.booking_filter = $booking_filter
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-bookings table refresh complete
				$( document.body ).trigger( 'updated_wcfm-bookings' );
			}
		}
	} );
	
	if( $('#dropdown_booking_filter').length > 0 ) {
		$('#dropdown_booking_filter').on('change', function() {
		  $booking_filter = $('#dropdown_booking_filter').val();
		  $wcfm_bookings_table.ajax.reload();
		});
	}
	
	// Mark Booking as Confirmed
	$( document.body ).on( 'updated_wcfm-bookings', function() {
		$('.wcfm_booking_mark_confirm').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm("Are you sure and want to 'Mark as Confirmed' this Booking?");
				if(rconfirm) markCompleteWCFMBooking($(this));
				return false;
			});
		});
	});
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-bookings', function() {
		$.each(wcfm_bookings_screen_manage, function( column, column_val ) {
		  $wcfm_bookings_table.column(column).visible( false );
		} );
	});
	
	function markCompleteWCFMBooking(item) {
		$('#wcfm-bookings_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'wcfm_booking_mark_confirm',
			bookingid : item.data('bookingid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_bookings_table.ajax.reload();
				$('#wcfm-bookings_wrapper').unblock();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
} );