<?php
/**
 * Plugin Name: NavMenu Addon For Elementor
 * Description: Adds new NavMenus to the Elementor Page Builder plugin. Now with Site Branding options, search box, basic MegaMenu and Fullscreen Menu Overlay
 * Plugin URI: https://themeisle.com/
 * Author: ThemeIsle
 * Version: 1.1.2
 * Author URI: https://themeisle.com/
 *
 * Text Domain: navmenu-addon-for-elementor
 * Requires License: no
 * WordPress Available: yes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'ELEMENTOR_MENUS_VERSION', '1.1.2' );

define( 'ELEMENTOR_MENUS__FILE__', __FILE__ );
define( 'ELEMENTOR_MENUS_PLUGIN_BASE', plugin_basename( ELEMENTOR_MENUS__FILE__ ) );
define( 'ELEMENTOR_MENUS_PATH', plugin_dir_path( ELEMENTOR_MENUS__FILE__ ) );
define( 'ELEMENTOR_MENUS_MODULES_PATH', ELEMENTOR_MENUS_PATH . 'modules/' );
define( 'ELEMENTOR_MENUS_URL', plugins_url( '/', ELEMENTOR_MENUS__FILE__ ) );
define( 'ELEMENTOR_MENUS_ASSETS_URL', ELEMENTOR_MENUS_URL . 'assets/' );
define( 'ELEMENTOR_MENUS_MODULES_URL', ELEMENTOR_MENUS_URL . 'modules/' );

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_menus_load_plugin() {
	load_plugin_textdomain( 'navmenu-addon-for-elementor' );

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'elementor_menus_fail_load' );

		return;
	}

	$elementor_version_required = '1.0.6';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'elementor_menus_fail_load_out_of_date' );

		return;
	}

	require( ELEMENTOR_MENUS_PATH . 'plugin.php' );
}

add_action( 'plugins_loaded', 'elementor_menus_load_plugin' );

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_menus_fail_load() {
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

		$message  = '<p>' . __( 'Elementor NavMenu is not working because you need to activate the Elementor plugin.', 'navmenu-addon-for-elementor' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', 'navmenu-addon-for-elementor' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		$message  = '<p>' . __( 'Elementor NavMenu is not working because you need to install the Elemenor plugin', 'navmenu-addon-for-elementor' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Elementor Now', 'navmenu-addon-for-elementor' ) ) . '</p>';
	}

	echo '<div class="error"><p>' . $message . '</p></div>';
}

function elementor_menus_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message      = '<p>' . __( 'Elementor NavMenu is not working because you are using an old version of Elementor.', 'navmenu-addon-for-elementor' ) . '</p>';
	$message     .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'navmenu-addon-for-elementor' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path         = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}

function navmenu_navbar_menu_choices() {
	$menus = wp_get_nav_menus();
	$items = array();
	$i     = 0;
	foreach ( $menus as $menu ) {
		if ( $i == 0 ) {
			$default = $menu->slug;
			$i ++;
		}
		$items[ $menu->slug ] = $menu->name;
	}

	return $items;
}

remove_filter( 'nav_menu_description', 'strip_tags' );

/**
 * Add descriptions to menu items
 */
function navmenu_nav_description( $item_output, $item, $depth, $args ) {

	if ( $args->theme_location != 'nav_mega_menu' ) {
		return $item_output;
	}

	if ( $item->description ) {
		$item_output = str_replace( $args->link_after . '</a>', '<div class="menu-item-description">' . $item->description . '</div>' . $args->link_after . '</a>', $item_output );
	}

	return $item_output;

}

add_filter( 'walker_nav_menu_start_el', 'navmenu_nav_description', 10, 4 );


add_filter( 'wp_nav_menu_objects', 'navmenu_thumb_filter_menu', 10, 2 );

function navmenu_thumb_filter_menu( $sorted_menu_objects, $args ) {

	// check for the right menu to filter
	if ( $args->theme_location != 'nav_mega_menu' ) {
		return $sorted_menu_objects;
	}
	// edit the menu objects
	foreach ( $sorted_menu_objects as $menu_object ) {
		// searching for menu items linking to posts or pages
		// can add as many post types to the array
		if ( in_array( $menu_object->object, array( 'post', 'page', 'product' ) ) ) {
			// set the title to the post_thumbnail if available
			// thumbnail size is the second parameter of get_the_post_thumbnail()
			$menu_object->description = has_post_thumbnail( $menu_object->object_id ) ? get_the_post_thumbnail( $menu_object->object_id, 'medium' ) : $menu_object->description;
		}
	}

	return $sorted_menu_objects;

}

function nav_menu_body_classes( $classes ) {

	$classes[] = 'has-navmenu';
	$classes[] = 'has-megamenu';

	return $classes;
}

add_filter( 'body_class', 'nav_menu_body_classes' );

/* Load TGM */
require_once( ELEMENTOR_MENUS_PATH . 'includes/class-tgm-plugin-activation.php' );

/**
 * Configure TGMPA.
 */
function elementor_nav_menus_register_required_plugins() {
	$plugins = array(
		array(
			'name'     => 'Elementor Addons & Widgets',
			'slug'     => 'elementor-addon-widgets',
			'required' => false,
		),
	);

	$config = array(
		'id'           => 'navmenu-addon-for-elementor',
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

add_action( 'tgmpa_register', 'elementor_nav_menus_register_required_plugins' );


$vendor_file = ELEMENTOR_MENUS_PATH . '/vendor/autoload.php';
if ( is_readable( $vendor_file ) ) {
	require_once $vendor_file;
}

add_filter( 'themeisle_sdk_products', 'navmenu_elementor_register_sdk', 10, 1 );
function navmenu_elementor_register_sdk( $products ) {
	$products[] = ELEMENTOR_MENUS__FILE__;
	return $products;
}
