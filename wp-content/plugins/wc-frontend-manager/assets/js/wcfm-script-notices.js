jQuery(document).ready(function($) {
		
	$wcfm_notice_table = $('#wcfm-notice').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 1 },
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
										{ "targets": 1, "orderable" : false }, 
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-notices'
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-groups table refresh complete
				$( document.body ).trigger( 'updated_wcfm-notice' );
			}
		}
	} );
	
	// Delete notice
	$( document.body ).on( 'updated_wcfm-notice', function() {
		$('.wcfm_notice_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm("Are you sure and want to delete this 'Topic'?\nYou can't undo this action ...");
				if(rconfirm) deleteWCFMNotice($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMNotice(item) {
		jQuery('#wcfm_notice_listing_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action   : 'delete_wcfm_notice',
			noticeid : item.data('noticeid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_notice_table) $wcfm_notice_table.ajax.reload();
				jQuery('#wcfm_notice_listing_expander').unblock();
			}
		});
	}
} );