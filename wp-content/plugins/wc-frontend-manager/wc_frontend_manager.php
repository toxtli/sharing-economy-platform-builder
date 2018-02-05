<?php
/**
 * Plugin Name: WooCommerce Frontend Manager
 * Plugin URI: https://wclovers.com
 * Description: WooCommerce is really Easy and Beautiful. We are here to make your life much more Easier and Peaceful.
 * Author: WC Lovers
 * Version: 3.4.5
 * Author URI: https://wclovers.com
 *
 * Text Domain: wc-frontend-manager
 * Domain Path: /lang/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 3.2.0
 *
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly


if ( ! class_exists( 'WCFM_Dependencies' ) )
	require_once 'helpers/class-wcfm-dependencies.php';

require_once 'helpers/wcfm-core-functions.php';
require_once 'wc_frontend_manager_config.php';

if(!defined('WCFM_TOKEN')) exit;
if(!defined('WCFM_TEXT_DOMAIN')) exit;

if(!WCFM_Dependencies::woocommerce_plugin_active_check()) {
	add_action( 'admin_notices', 'wcfm_woocommerce_inactive_notice' );
} else {

	if(!class_exists('WCFM')) {
		require_once( 'core/class-wcfm.php' );
		global $WCFM, $WCFM_Query;
		$WCFM = new WCFM( __FILE__ );
		$GLOBALS['WCFM'] = $WCFM;
		
		// Init WCFM Query
		require_once( 'core/class-wcfm-query.php' );
		$WCFM_Query = new WCFM_Query();
		$GLOBALS['WCFM_Query'] = $WCFM_Query;
		
		// Activation Hooks
		register_activation_hook( __FILE__, array( 'WCFM', 'activate_wcfm' ) );
		register_activation_hook( __FILE__, 'flush_rewrite_rules' );
		
		// Deactivation Hooks
		register_deactivation_hook( __FILE__, array( 'WCFM', 'deactivate_wcfm' ) );
	}
}
?>