jQuery(document).ready(function($) {
	// Booking Status Update
	$('#wcfm_modify_booking_status').click(function(event) {
		event.preventDefault();
		modifyWCFMBookingStatus();
		return false;
	});
		
	function modifyWCFMBookingStatus() {
		$('#bookings_details_general_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action       : 'wcfm_modify_booking_status',
			booking_status : $('#wcfm_booking_status').val(),
			booking_id     : $('#wcfm_modify_booking_status').data('bookingid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$('#bookings_details_general_expander').unblock();
			}
		});
	}
});