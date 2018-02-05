<?php

class WCMP_Wp_Job_Manager {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;
    public $library;
    public $shortcode;
    public $admin;
    public $frontend;
    public $template;
    public $ajax;
    private $file;
    public $settings;
    public $dc_wp_fields;

    public function __construct($file) {

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WCMP_WP_JOB_MANAGER_PLUGIN_TOKEN;
        $this->text_domain = WCMP_WP_JOB_MANAGER_TEXT_DOMAIN;
        $this->version = WCMP_WP_JOB_MANAGER_PLUGIN_VERSION;

        add_action('init', array(&$this, 'init'), 0);
    }

    /**
     * initilize plugin on WP init
     */
    function init() {

        // Init Text Domain
        $this->load_plugin_textdomain();

        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new WCMP_Wp_Job_Manager_Admin();
        }
        $this->load_class('capabilities');
        $this->vendor_caps = new WCMp_Job_Manager_Capabilities();

        register_activation_hook(__FILE__, 'flush_rewrite_rules');
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        $locale = apply_filters('plugin_locale', get_locale(), $this->token);

        load_textdomain($this->text_domain, WP_LANG_DIR . "/wcmp-wp-job-manager/wcmp-wp-job-manager-$locale.mo");
        load_textdomain($this->text_domain, $this->plugin_path . "/languages/wcmp-wp-job-manager-$locale.mo");
    }

    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    /** Cache Helpers ******************************************************** */

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     *
     * @access public
     * @return void
     */
    function nocache() {
        if (!defined('DONOTCACHEPAGE'))
            define("DONOTCACHEPAGE", "true");
        // WP Super Cache constant
    }

}
