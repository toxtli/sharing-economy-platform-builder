(function($){


	"use strict";


	function getVals(formControl, controlType) {

		switch (controlType) {
			case 'text':
				var value = $(formControl).val();
			break;
			case 'number':
				var value = $(formControl).val();
			break;
			case 'textarea':
				var value = $(formControl).val();
			break;
			case 'radio':
				var value = $(formControl).val();
			break;
			case 'checkbox':
				if ($(formControl).is(":checked")) {
					value = 'yes';
				}
				else {
					value = 'no';
				}
			break;
			case 'select':
				var value = $(formControl).val();
			break;
			case 'multiselect':
				var value = $(formControl).val() || [];
			break;
			default:
				var value = $(formControl).val();
			break;
		}
		return value;
	}

	$(document).on( 'click', '.wfsm-button.wfsm-activate', function() {

		var el = $(this).parent();

		if ( el.hasClass('wfsm-active') ) {
			return false;
		}
		else {

			if ( $('body').hasClass('wfsm-active') ) {
				$('.wfsm-buttons.wfsm-active').removeClass('wfsm-active');
				$('.wfsm-quick-editor').remove();
			}
			else {
				$('body').addClass('wfsm-active').append('<div id="wfsm-overlay"></div>');
			}

			el.addClass('wfsm-active');

			var curr_data = {
				action: 'wfsm_respond',
				wfsm_id: el.attr('data-id')
			}

			$.post(wfsm.ajax, curr_data, function(response) {
				if (response) {

					$('body').append(response);

				}
				else {
					alert('Error!');
				}
			});

		}

		return false;

	});

	$(document).on( 'click', '.wfsm-button.wfsm-save', function() {

		var el = $(this).parent();
		var opts = $('.wfsm-quick-editor');
		var curr_saving = {};

		if ( el.hasClass('wfsm-active') ) {

			var inputs = $('.wfsm-quick-editor input.wfsm-collect-data, .wfsm-quick-editor select.wfsm-collect-data'), tmp;
			$.each(inputs, function(i, obj) {
				var tag = ( $(obj).prop('tagName') == 'INPUT' ? $(obj).attr('type') : $(obj).prop('tagName').toLowerCase() );
				curr_saving[$(obj).attr('name').replace('[]', '')] = getVals($(obj), tag);
			});

			curr_saving['wfsm-manage-stock-quantity'] = ( $('.wfsm-editor-button.wfsm-manage-stock-quantity').hasClass('wfsm-active') ? 'yes' : 'no' );

			if ( $('.wfsm-variation').length > 0 ) {
				var curr_ids = [];
				$('.wfsm-variation').each( function() {
					var curr_id = $(this).attr('data-id');
					curr_saving['wfsm-manage-stock-quantity-'+curr_id] = ( $('.wfsm-editor-button.wfsm-manage-stock-quantity-'+curr_id).hasClass('wfsm-active') ? 'yes' : 'no' );
					curr_ids.push(curr_id);
				});
				curr_saving['wfsm-variations-ids'] = curr_ids;
			}

			var curr_data = {
				action: 'wfsm_save',
				wfsm_id: el.attr('data-id'),
				wfsm_save: JSON.stringify(curr_saving),
				wfsm_loop: el.attr('data-loop')
			}

			$.post(wfsm.ajax, curr_data, function(response) {
				if (response) {

					response = $(response);

					if ( curr_data.wfsm_loop !== 'single' ) {
						el.closest('.type-product').replaceWith(response);
						response.addClass('product');
					}


					if ( el.hasClass('wfsm-active') ) {
						$('.wfsm-quick-editor').remove();
						el.removeClass('wfsm-active');
						$('body').removeClass('wfsm-active').find('#wfsm-overlay').remove();
					}

				}
				else {
					alert('Error!');
				}
			});

		}

		if ( curr_data.wfsm_loop == 'single' ) {
			location.reload();
		}
		else {
			return false;
		}

	});

	$(document).on( 'click', '.wfsm-button.wfsm-discard', function() {

		var el = $(this).parent();

		if ( el.hasClass('wfsm-active') ) {
			$('.wfsm-quick-editor').remove();
			el.removeClass('wfsm-active');
			$('body').removeClass('wfsm-active').find('#wfsm-overlay').remove();
		}

		return false;

	});

	$(document).on( 'click', '.wfsm-screen > div > .wfsm-editor-button, .wfsm-variations > div > .wfsm-editor-button', function() {

		var el = $(this).prev();

		if ( el.hasClass('wfsm-hidden') ) {
			$(this).addClass('wfsm-active');
			el.removeClass('wfsm-hidden').addClass('wfsm-visible');
		}
		else {
			el.removeClass('wfsm-visible').addClass('wfsm-hidden').find('input').val('');
			$(this).removeClass('wfsm-active');
		}

		return false;
	});

	$(document).on( 'click', '.wfsm-label-checkbox', function() {

		var curr_selected = $(this).filter(':visible').attr('class');
		curr_selected = curr_selected.substr(10, curr_selected.length);

		var curr_tobe = $(this).find('span').not(':visible').attr('class');
		curr_tobe = curr_tobe.substr(10, curr_tobe.length);

		$(this).removeClass('wfsm-'+curr_selected);
		$(this).addClass('wfsm-'+curr_tobe);
		$(this).find('input').val(curr_tobe);

	});

	$(document).on( 'click', '.wfsm-headline', function() {

		var curr = $(this);

		var curr_selected = $(this).next();

		curr_selected.slideToggle( 60, function() {
			curr.toggleClass('wfsm-active');
		});

	});

	$(document).on( 'click', '.wfsm-controls .wfsm-about', function() {

		$(this).find('em').toggleClass('wfsm-active');

		return false;

	});

	$(document).on( 'click', '.wfsm-controls .wfsm-expand', function() {

		var curr = $('.wfsm-quick-editor .wfsm-headline:not(.wfsm-active)');

		curr.each( function() {
			var curr_selected = $(this).next();

			curr_selected.slideDown( 60, function() {
				curr.addClass('wfsm-active');
			});

		});

		return false;

	});

	$(document).on( 'click', '.wfsm-controls .wfsm-contract', function() {

		var curr = $('.wfsm-quick-editor .wfsm-headline.wfsm-active');

		curr.each( function() {
			var curr_selected = $(this).next();

			curr_selected.slideUp( 60, function() {
				curr.removeClass('wfsm-active');
			});

		});

		return false;

	});

	$(document).on( 'click', '.wfsm-controls .wfsm-side-edit', function() {

		window.location.href = $('.wfsm-buttons.wfsm-active .wfsm-edit').attr('href');

		return false;

	});

	$(document).on( 'click', '.wfsm-controls .wfsm-side-save, .wfsm-editing', function() {

		$('.wfsm-buttons.wfsm-active .wfsm-save').click();

		return false;

	});

	$(document).on( 'click', '.wfsm-controls .wfsm-side-discard', function() {

		$('.wfsm-buttons.wfsm-active .wfsm-discard').click();

		return false;

	});

})(jQuery);