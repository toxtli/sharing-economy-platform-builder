jQuery(document).ready(function(){
	if ( jQuery( 'form.cart' ).length > 0 ) {
		waypoint = new Waypoint({
			element: jQuery( 'form.cart' ),
			handler: function( direction ) {
				if ( 'up' === direction ) {
					jQuery( '.ssatc-sticky-add-to-cart' ).addClass( 'slideOutUp' );
					jQuery( '.ssatc-sticky-add-to-cart' ).removeClass( 'slideInDown' );
				}

				if ( 'down' === direction ) {
					jQuery( '.ssatc-sticky-add-to-cart' ).addClass( 'slideInDown' );
					jQuery( '.ssatc-sticky-add-to-cart' ).removeClass( 'slideOutUp' );
				}
			}
		});
	}
});