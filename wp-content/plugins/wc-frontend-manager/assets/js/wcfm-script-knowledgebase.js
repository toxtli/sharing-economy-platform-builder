jQuery(document).ready(function($) {
		
	if( $('#wcfm-knowledgebase').length > 0 ) {
		$wcfm_knowledgebase_table = $('#wcfm-knowledgebase').DataTable( {
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
					d.controller   = 'wcfm-knowledgebase'
				},
				"complete" : function () {
					initiateTip();
					
					// Fire wcfm-groups table refresh complete
					$( document.body ).trigger( 'updated_wcfm-knowledgebase' );
				}
			}
		} );
		
		// Delete knowledgebase
		$( document.body ).on( 'updated_wcfm-knowledgebase', function() {
			$('.wcfm_knowledgebase_delete').each(function() {
				$(this).click(function(event) {
					event.preventDefault();
					var rconfirm = confirm("Are you sure and want to delete this 'Knowledgebase'?\nYou can't undo this action ...");
					if(rconfirm) deleteWCFMKnowledgebase($(this));
					return false;
				});
			});
		});
		
		function deleteWCFMKnowledgebase(item) {
			jQuery('#wcfm_knowledgebase_listing_expander').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action          : 'delete_wcfm_knowledgebase',
				knowledgebaseid : item.data('knowledgebaseid')
			}	
			jQuery.ajax({
				type:		'POST',
				url: wcfm_params.ajax_url,
				data: data,
				success:	function(response) {
					if($wcfm_knowledgebase_table) $wcfm_knowledgebase_table.ajax.reload();
					jQuery('#wcfm_knowledgebase_listing_expander').unblock();
				}
			});
		}
	} else {
		// Collapsible
		$('.page_collapsible').collapsible({
			//defaultOpen: 'wcfm_profile_personal_head',
			speed: 'slow',
			loadOpen: function (elem) { //replace the standard open state with custom function
				elem.next().show();
			},
			loadClose: function (elem, opts) { //replace the close state with custom function
				elem.next().hide();
			},
			animateOpen: function(elem, opts) {
				elem.find('span').addClass('fa-arrow-circle-o-up').removeClass('fa-arrow-circle-o-down').css( { 'float': 'right', 'padding': '5px' } ).show();
				elem.next().stop(true, true).slideDown(opts.speed);
			},
			animateClose: function(elem, opts) {
				elem.find('span').addClass('fa-arrow-circle-o-down').removeClass('fa-arrow-circle-o-up').css( { 'float': 'right', 'padding': '5px' } ).show();
				elem.next().stop(true, true).slideUp(opts.speed);
			}
		});	
		$('.page_collapsible').find('span').addClass('fa');
		$('.collapse-close').find('span').addClass('fa-arrow-circle-o-down').css( { 'float': 'right', 'padding': '5px' } ).show();
		$('.collapse-open').find('span').addClass('fa-arrow-circle-o-up').css( { 'float': 'right', 'padding': '5px' } ).show();
	}
} );