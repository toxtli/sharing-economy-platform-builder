<?php
/*
 * Plugin Name: Page Templater For Elementor
 * Plugin URI: https://themeisle.com/
 * Description: A helper plugin for users of Elementor Pagebuilder. Adds 2 new templates for complete full width experience while using the page builder - support for a number of popular themes is built-in.
 * Version: 1.2.2
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
	die; }

/*
 Constants
------------------------------------------ */

/* Set plugin version constant. */
define( 'ET_VERSION', '1.2.2' );

/* Set constant path to the plugin directory. */
define( 'ET_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set the constant path to the plugin directory URI. */
define( 'ET_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/* ElemenTemplater Class */
require_once( ET_PATH . 'elementemplater-class.php' );

/* Custom Post Template Class */
if ( version_compare( floatval( $GLOBALS['wp_version'] ), '4.7', '<' ) ) { // 4.6.1 and older
	require_once( ET_PATH . 'custom-posttype-class.php' );
}

/* Template Functions */
require_once( ET_PATH . 'inc/elementemplater-functions.php' );

/* Load TGM */
require_once( ET_PATH . 'inc/class-tgm-plugin-activation.php' );

/**
 * Configure TGMPA.
 */
function elementor_templater_register_required_plugins() {
	$plugins = array(
		array(
			'name'     => 'Elementor Addons & Widgets',
			'slug'     => 'elementor-addon-widgets',
			'required' => false,
		),
	);

	$config = array(
		'id'           => 'elementor-templater',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'parent_slug'  => 'plugins.php',
		'capability'   => 'manage_options',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}

add_action( 'tgmpa_register', 'elementor_templater_register_required_plugins' );


/* Require vendor file. */
$vendor_file = ET_PATH . 'vendor/autoload.php';
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
function elementor_templater_register_sdk( $products ) {
	$products[] = __FILE__;
	return $products;
}

add_filter( 'themeisle_sdk_products', 'elementor_templater_register_sdk', 10, 1 );
