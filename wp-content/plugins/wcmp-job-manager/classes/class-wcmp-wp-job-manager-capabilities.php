<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Job_Manager_Capabilities {

    public $wp_job_manager_capabilities;
    public $wcmp_capabilities;

    public function __construct() {
        $this->wcmp_capabilities = get_option('wcmp_capabilities_settings_name');
        $this->wp_job_manager_capabilities = $this->get_wp_job_manager_capabilities();
        $this->init_wcmp_job_maneger_capabilities();
    }

    public function init_wcmp_job_maneger_capabilities() {
        global $wp_roles;
        if (class_exists('WP_Roles') && !isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        if (is_object($wp_roles)) {
            foreach ($this->wp_job_manager_capabilities as $key => $cap_group) {
                if (isset($this->wcmp_capabilities[$key]) && $this->wcmp_capabilities[$key] == 'Enable') {
                    foreach ($cap_group as $cap) {
                        $wp_roles->add_cap('dc_vendor', $cap);
                    }
                } else{
                    foreach ($cap_group as $cap) {
                        $wp_roles->remove_cap('dc_vendor', $cap);
                    }
                }  
            }
        }
    }

    /**
     * Get capabilities
     * @return array
     */
    public function get_wp_job_manager_capabilities() {
        return array(
            'core' => array(
                'manage_job_listings'
            ),
            'job_listing' => array(
                "edit_job_listing",
                "read_job_listing",
                "delete_job_listing",
                "edit_job_listings",
                "publish_job_listings",
                "read_private_job_listings",
                "delete_job_listings",
                "delete_private_job_listings",
                "delete_published_job_listings",
                "edit_private_job_listings",
                "edit_published_job_listings",
                "manage_job_listing_terms",
                "edit_job_listing_terms",
                "delete_job_listing_terms",
                "assign_job_listing_terms"
            )
        );
    }
}
