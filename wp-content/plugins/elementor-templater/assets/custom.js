jQuery( document ).ready(
	function( $ ) {

			$(
				function () {
					var nav = $( '.elementor-widget-wp-widget-nav_menu' );
					$( window ).scroll(
						function () {
							if ($( this ).scrollTop() > 85) {
								nav.addClass( 'anchor-menu-fixed anchor-menu' );
							} else {
								nav.removeClass( 'anchor-menu-fixed anchor-menu' );
							}
						}
					);
				}
			);

	}
);
