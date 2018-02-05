jQuery(document).ready(function($){

	jQuery( '.ssatc-content .button.variable' ).click( function() {
	    jQuery( 'html, body' ).animate({
	        scrollTop: jQuery( 'div.product' ).offset().top - 32
	    }, 750 );
	});

});