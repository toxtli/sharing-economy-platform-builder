<?php

/*
  Plugin Name: WCMp For WP Job Manager
  Plugin URI: https://wc-marketplace.com/
  Description: A Free Extension Which Integrate WC Marketplace with WP Job Manager
  Author: WC Marketplace
  Version: 1.0.0
  Author URI: https://wc-marketplace.com/
 */

if (!class_exists('WCMp_Job_Manager_Dependencies')) {
    require_once 'includes/class-dc-dependencies.php';
}
require_once 'includes/wcmp-wp-job-manager-core-functions.php';
require_once 'config.php';
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('WCMP_WP_JOB_MANAGER_PLUGIN_TOKEN')) {
    exit;
}
if (!defined('WCMP_WP_JOB_MANAGER_TEXT_DOMAIN')) {
    exit;
}

if (!WCMp_Job_Manager_Dependencies::woocommerce_plugin_active_check()) {
    add_action('admin_notices', 'wjm_woocommerce_inactive_notice');
}
if (!WCMp_Job_Manager_Dependencies::wc_marketplace_plugin_active_check()) {
    add_action('admin_notices', 'wjm_wcmp_inactive_notice');
}
if (!WCMp_Job_Manager_Dependencies::wp_job_manager_plugin_active_check()) {
    add_action('admin_notices', 'wjm_job_manager_inactive_notice');
}

if (!WCMp_Job_Manager_Dependencies::wp_job_manager_paid_listing_plugin_active_check()) {
    add_action('admin_notices', 'wjm_job_manager_paid_listing_inactive_notice');
}
if (class_exists('WCMp')) {
    if (!class_exists('WCMP_Wp_Job_Manager')) {
        require_once( 'classes/class-wcmp-wp-job-manager.php' );
        global $WCMP_Wp_Job_Manager;
        $WCMP_Wp_Job_Manager = new WCMP_Wp_Job_Manager(__FILE__);
        $GLOBALS['WCMP_Wp_Job_Manager'] = $WCMP_Wp_Job_Manager;
    }
}