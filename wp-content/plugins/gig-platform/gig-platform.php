<?php
/**
 * Gig Platform
 *
 * @package     GitPlatform
 * @author      Carlos Toxtli
 * @copyright   2017 West Virginia University
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Gig Platform
 * Plugin URI:  https://gigapp.ga
 * Description: Gig Platform Customization.
 * Version:     1.0.0
 * Author:      Carlos Toxtli
 * Author URI:  https://gigapp.ga
 * Text Domain: gig-platform
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_action( 'wp_head', function( ) {
	echo "";
});

add_filter( 'caldera_forms_autopopulate_options_post_value_field', function( $value_field, $field ) {
    if( 'products' == $field[ 'slug' ] ) {
        $value_field = 'price';
    }
 
    return $value_field;
}, 24, 2 );

