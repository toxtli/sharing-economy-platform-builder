<?php
	/* Support for the Twenty Fourteen theme */
	$style = '
	    .page-template-builder-fullwidth-std .elementor-page .site {
            max-width: 100%;
			overflow: hidden;
        }
        .page-template-builder-fullwidth-std .site::before {
            display: none;
        }
        .page-template-builder-fullwidth-std .site-header {
            max-width: 100%;
        }
	';
	wp_add_inline_style( 'twentyfourteen-style', $style );
