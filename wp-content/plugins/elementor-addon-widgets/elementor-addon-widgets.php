<?php
/*
 * Plugin Name: Elementor Addons & Widgets
 * Plugin URI: https://themeisle.com/
 * Description: Adds new Addons & Widgets that are specifically designed to be used in conjunction with the Elementor Page Builder.
 * Version: 1.1.4
 * Author: ThemeIsle
 * Author URI: https://themeisle.com/
 * Requires at least:   4.4
 * Tested up to:        4.9
 *
 * Requires License: no
 * WordPress Available: yes
 */

/* Do not access this file directly */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
Constants
------------------------------------------ */

/* Set plugin version constant. */
define( 'EA_VERSION', '1.1.4' );

/* Set constant path to the plugin directory. */
define( 'EA_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set the constant path to the plugin directory URI. */
define( 'EA_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/* ElemenTemplater Class */
require_once( EA_PATH . 'eaw-class.php' );

/**
 * Query WooCommerce activation
 */
if ( ! function_exists( 'eaw_is_woocommerce_active' ) ) {

	function eaw_is_woocommerce_active() {
		return class_exists( 'woocommerce' ) ? true : false;
	}
}

/**
 * Call a shortcode function by tag name.
 *
 * @since  1.0.0
 *
 * @param string $tag     The shortcode whose function to call.
 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
function eaw_do_shortcode( $tag, array $atts = array(), $content = null ) {

	global $shortcode_tags;

	if ( ! isset( $shortcode_tags[ $tag ] ) ) {
		return false;
	}

	return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/* Require vendor file. */
$vendor_file = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( is_readable( $vendor_file ) ) {
	require_once $vendor_file;
}

/**
 * Register SDK.
 *
 * @param $products
 *
 * @return array
 */
function elementor_addon_widgets_register_sdk( $products ) {
	$products[] = __FILE__;
	return $products;
}

add_filter( 'themeisle_sdk_products', 'elementor_addon_widgets_register_sdk', 10, 1 );
