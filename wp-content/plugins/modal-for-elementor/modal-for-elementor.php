<?php
/**
* Plugin Name: PopBox For Elementor
* Description: Create content-rich popboxes for your site using the power of Elementor Page Builder
* Version: 1.0.5
* Author: Zulfikar Nore
* Author URI: https://designsbynore.com/
* Plugin URI: https://designsbynore.com/popups/popbox/
* Text Domain: modal-for-elementor
* License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'MODAL_ELEMENTOR_VERSION', '1.0.5' );

define( 'MODAL_ELEMENTOR__FILE__', __FILE__ );
define( 'MODAL_ELEMENTOR_PLUGIN_BASE', plugin_basename( MODAL_ELEMENTOR__FILE__ ) );
define( 'MODAL_ELEMENTOR_PATH', plugin_dir_path( MODAL_ELEMENTOR__FILE__ ) );
define( 'MODAL_ELEMENTOR_MODULES_PATH', MODAL_ELEMENTOR_PATH . 'modules/' );
define( 'MODAL_ELEMENTOR_URL', plugins_url( '/', MODAL_ELEMENTOR__FILE__ ) );
define( 'MODAL_ELEMENTOR_ASSETS_URL', MODAL_ELEMENTOR_URL . 'assets/' );
define( 'MODAL_ELEMENTOR_MODULES_URL', MODAL_ELEMENTOR_URL . 'modules/' );

// Load the plugin after Elementor (and other plugins) are loaded
add_action( 'plugins_loaded', function() {
	// Load localization file
	load_plugin_textdomain( 'modal-for-elementor', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	// Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'modal_for_elementor_fail_load' );
		return;
	}

	// Check version required
	$elementor_version_required = '1.8.5';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'modal_for_elementor_fail_load_out_of_date' );
		return;
	}

	// Require the main plugin file
	require( MODAL_ELEMENTOR_PATH . 'plugin.php' );
} );

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function modal_for_elementor_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

		$message = '<p>' . __( 'Elementor Starter is not working because you need to activate the Elementor plugin.', 'modal-for-elementor' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', 'modal-for-elementor' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		$message = '<p>' . __( 'Modal For Elementor is not working because you need to install the Elemenor plugin', 'modal-for-elementor' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Elementor Now', 'modal-for-elementor' ) ) . '</p>';
	}

	echo '<div class="error"><p>' . $message . '</p></div>';
}

function modal_for_elementor_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'Modal For Elementor is not working because you are using an old version of Elementor.', 'modal-for-elementor' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'modal-for-elementor' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}

add_action( 'wp_enqueue_scripts', 'register_popup_style' );
function register_popup_style() {
	wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.css' );
	wp_enqueue_style( 'modal-popup', plugin_dir_url( __FILE__ ) . 'css/popup.css', array( 'bootstrap' ) );
	
	if ( is_rtl() ) {
		wp_enqueue_style(
			'modal-popup-rtl',
			plugin_dir_url( __FILE__ ) . 'css/rtl.popup.css',
			array ( 'modal-popup' )
		);
	}
	
	wp_enqueue_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'modal-popup-js', plugin_dir_url( __FILE__ ) . 'js/popup.js', array( 'jquery', 'bootstrap' ), null, true );
}

/* create new custom post type named popup */
add_action( 'init', 'create_popup_post_type' );
//flush rewrite rules
add_action('init', 'flush_rewrite_rules', 10 );

function create_popup_post_type() {
	register_post_type( 'elementor-popup',
		array(
			'labels' => array(
				'name' 			=> __( 'PopBoxes', 'modal-for-elementor' ),
				'singular_name' => __( 'PopBox', 'modal-for-elementor' ),
				'all_items' 	=> __( 'All PopBoxes', 'modal-for-elementor' ),
				'add_new_item' 	=> __( 'Add New PopBox', 'modal-for-elementor' ),
				'new_item' 		=> __( 'Add New PopBox', 'modal-for-elementor' ),
				'add_new' 		=> __( 'Add New PopBox', 'modal-for-elementor' ),
				'edit_item' 	=> __( 'Edit PopBox', 'modal-for-elementor' ),
			),
			'has_archive' 			=> false,
			'rewrite' 				=> array( 'slug' => 'elementor-popup', 'with_front' => false ),
			'query_var' 			=> false,
			'menu_icon'   			=> 'dashicons-slides',
			'public' 				=> true,
			'exclude_from_search' 	=> true,
			'capability_type' 		=> 'post'
		)
	);
	add_post_type_support( 'elementor-popup', 'elementor' );
}	