<?php
	/* Support for the GeneratePress theme */
	$style = '	    
		.entry-header {
			background-color: #fff;	
		}		
		.entry-header .grid-container {
			padding: 10px 10px;
		}
	';
	wp_add_inline_style( 'generate-style', $style );
