jQuery(document).ready(function ($) {

    // Tabs
    $( ".inline-list" ).each( function() {
        $( this ).find( "li" ).each( function(i) {
            $( this).click( function(){
                $( this ).addClass( "current" ).siblings().removeClass( "current" )
                    .parents( "#wpbody" ).find( "div.panel-left" ).removeClass( "visible" ).end().find( 'div.panel-left:eq('+i+')' ).addClass( "visible" );
                return false;
            } );
        } );
    } );


    // Scroll to anchor
    $( ".anchor-nav a, .toc a" ).click( function(e) {
        e.preventDefault();

        var href = $( this ).attr( "href" );
        $( "html, body" ).animate( {
            scrollTop: $( href ).offset().top - 50
        }, 'slow', 'swing' );
    } );


    // Back to top links
    $( ".livemesh-doc .panel-left h3" ).append( $( "<a class='back-to-top' href='#panel'><span class='dashicons dashicons-arrow-up-alt2'></span> Back to top</a>" ) );


    // Add lightbox to cloud links
    $( "a[href*='cl.ly']:not(.direct-link)" ).each( function() {

        // Add thickbox class to each cloud link
        $( this ).addClass( 'thickbox' );

        // Add the iframe code to each cloud link
        var imgUrl = $( this ).attr( "href" ) + '?TB_iframe=true&width=1200&height=700';

        // Set the new url
        $( this ).attr( "href", imgUrl );
    } );


    // Sticky sidebar for upgrade to pro window
    $(window).on("resize load", function () {
        var current_width = $(window).width();

        // Above tablet size
        if (current_width > 768) {

            // Calculate the offset due to padding on the sidebar
            var paddingTop = $("#panel").css("padding-top");

            var paddingTopInteger = parseInt(paddingTop, 10);

            // While we're scrolling let's do this stuff
            $(window).scroll(function () {

                // Get current height of sticky sidebar
                var height = $(".panel-right .panel-inner").height();

                // Get desired width of sticky sidebar from the parent of sticky sidebar
                var width = $(".panel-right").width();

                // Get height of viewport
                viewportHeight = $(window).height();

                // Get amount already scolled
                var scroll = $(this).scrollTop();

                if (scroll < $("#panel").offset().top) {
                    // If amount scolled from top is less than the position of panel (sidebar container) relative to document
                    $(".panel-right .panel-inner").css({
                        'position': 'absolute',
                        'top': '0',
                        'width': '100%'
                    });

                } else if (height > viewportHeight || $(window).width() <= 768 ) {
                    // If the sidebar is taller than the viewport, don't stick the sidebar or remove stickiness if already stuck
                    $(".panel-right .panel-inner").css({
                        'position': 'relative',
                        'width': '100%',
                        'top': 'initial'
                    });
                } else {
                    // Make the sidebar fixed while scrolling, if scroll position is past the panel containing sidebar
                    $(".panel-right .panel-inner").css({
                        'position': 'fixed',
                        'top': paddingTopInteger,
                        'width': width + 'px'
                    });
                }
            });
        }
    });

});