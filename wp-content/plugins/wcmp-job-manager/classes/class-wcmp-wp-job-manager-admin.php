<?php

class WCMP_Wp_Job_Manager_Admin {

    public $settings;

    public function __construct() {
        add_filter('wcmp_capabilities', array(&$this, 'add_wp_job_manager_capabilities'), 10, 1);
        add_filter('settings_capabilities_tab_new_input', array(&$this, 'save_wp_job_manager_capabilities'), 10, 2);
        add_filter('wcmp_vendor_product_types', array(&$this, 'add_job_package'), 10, 1);
        add_filter('settings_product_tab_new_input', array(&$this, 'save_job_package_type'), 10, 2);
    }

    /*
     * add job manager capabilities
     * @input array $wcmp_capabilities
     * @return array $wcmp_capabilities
     */

    function add_wp_job_manager_capabilities($wcmp_capabilities) {
        global $WCMp;
        $wcmp_capabilities["wpjob_settings_section"] = array(
            "ref" => &$this,
            "title" => __('WCMp For WP Job Manager', $WCMp->text_domain),
            "fields" => array(
                "core" => array('title' => __('Manage Job Listings', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'core', 'label_for' => 'core', 'desc' => __('Allow vendors to Manage Job Listings.', $WCMp->text_domain), 'name' => 'core', 'value' => 'Enable'), // Checkbox
                "job_listing" => array('title' => __('Job Listing', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'job_listing', 'label_for' => 'job_listing', 'desc' => __('Allow vendors to Job Listing.', $WCMp->text_domain), 'name' => 'job_listing', 'value' => 'Enable'), // Checkbox
            )
        );
        return $wcmp_capabilities;
    }

    /*
     * save job manager extra capabilities
     * @input array $new_input, $input
     * @return array $new_input
     */

    function save_wp_job_manager_capabilities($new_input, $input) {
        if (isset($input['core'])) {
            $new_input['core'] = sanitize_text_field($input['core']);
        }
        if (isset($input['job_listing'])) {
            $new_input['job_listing'] = sanitize_text_field($input['job_listing']);
        }
        return $new_input;
    }

    /*
     * add job manager product type
     * @input array $types
     * @return array $types
     */

    function add_job_package($types) {
        global $WCMp;
        $types["job_package"] = array('title' => __('Job Package', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'job_package', 'label_for' => 'job_package', 'name' => 'job_package', 'value' => 'Enable');
        return $types;
    }

    /*
     * save job mamager product type
     * @input array $new_input, $input
     * @return array $new_input
     */

    function save_job_package_type($new_input, $input) {
        if (isset($input['job_package'])) {
            $new_input['job_package'] = sanitize_text_field($input['job_package']);
        }
        return $new_input;
    }

    /*
     * callback function for wcmp capabilities setting panel
     */

    public function wpjob_settings_section_info() {
        global $WCMP_Job_Manager;
    }

    function load_class($class_name = '') {
        global $WCMP_Wp_Job_Manager;
        if ('' != $class_name) {
            require_once ($WCMP_Wp_Job_Manager->plugin_path . '/admin/class-' . esc_attr($WCMP_Wp_Job_Manager->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

}
