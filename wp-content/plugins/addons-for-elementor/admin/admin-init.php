<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class LAE_Admin {


    protected $plugin_slug = 'livemesh_el_addons';

    public function __construct() {

        $this->includes();
        $this->init_hooks();

    }

    public function includes() {

        // load class admin ajax function
        require_once LAE_PLUGIN_DIR . 'admin/admin-ajax.php';

        /**
         * Classes responsible for displaying admin notices.
         */
        require_once LAE_PLUGIN_DIR . 'admin/notices/admin-notice.php';
        require_once LAE_PLUGIN_DIR . 'admin/notices/admin-notice-rate.php';

    }

    public function init_hooks() {

        // Build admin menu/pages
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        add_action('current_screen', array($this, 'remove_admin_notices'));


        /**
         * Notice: Rate plugin
         */
        $rate = new LAE_Notice_Rate('rate', LAE_PLUGIN_DIR . 'admin/notices/templates/rate.php');

        add_action('load-plugins.php', array($rate, 'defer_first_time'));
        add_action('admin_notices', array($rate, 'display_notice'));
        add_action('admin_post_lae_dismiss_notice', array($rate, 'dismiss_notice'));

    }

    public function remove_admin_notices($screen) {

        // If this screen is Addons for Elementor plugin options page, remove annoying admin notices
        if (strpos($screen->id, $this->plugin_slug) !== false) {
            add_action('admin_notices', array(&$this, 'remove_notices_start'));
            add_action('admin_notices', array(&$this, 'remove_notices_end'), 999);
        }
    }

    public function remove_notices_start() {

        // Turn on output buffering
        ob_start();

    }

    public function remove_notices_end() {

        // Get current buffer contents and delete current output buffer
        $content = ob_get_contents();
        ob_clean();

    }

    public function add_plugin_admin_menu() {

        add_menu_page(
            'Addons for Elementor',
            __('Elementor Addons', 'livemesh-el-addons'),
            'manage_options',
            $this->plugin_slug,
            array($this, 'display_settings_page'),
            LAE_PLUGIN_URL . 'admin/assets/images/logo-shape16.png'
        );

        // add plugin settings submenu page
        add_submenu_page(
            $this->plugin_slug,
            'Widgets Settings',
            __('Settings', 'livemesh-el-addons'),
            'manage_options',
            $this->plugin_slug,
            array($this, 'display_settings_page')
        );

        // add import/export submenu page
        add_submenu_page(
            $this->plugin_slug,
            'Widgets Documentation',
            __('Documentation', 'livemesh-el-addons'),
            'manage_options',
            $this->plugin_slug . '_documentation',
            array($this, 'display_plugin_documentation')
        );

        // add global settings submenu page
        add_submenu_page(
            $this->plugin_slug,
            'Upgrade to Pro Version',
            __('Upgrade to Pro', 'livemesh-el-addons'),
            'manage_options',
            $this->plugin_slug . '_pro_upgrade',
            array($this, 'display_plugin_premium_upgrade')
        );

    }

    public function display_settings_page() {

        require_once('views/admin-header.php');
        require_once('views/admin-banner2.php');
        require_once('views/settings.php');
        require_once('views/admin-footer.php');

    }

    public function display_plugin_documentation() {


        require_once('views/admin-header.php');
        require_once('views/admin-banner1.php');
        require_once('views/documentation.php');
        require_once('views/admin-footer.php');

    }

    public function display_plugin_premium_upgrade() {


        require_once('views/admin-header.php');
        require_once('views/admin-banner3.php');
        require_once('views/premium-upgrade.php');
        require_once('views/admin-footer.php');

    }

    public function enqueue_admin_scripts() {

        // Use minified libraries if LAE_SCRIPT_DEBUG is turned off
        $suffix = (defined('LAE_SCRIPT_DEBUG') && LAE_SCRIPT_DEBUG) ? '' : '.min';

        // get current admin screen
        $screen = get_current_screen();

        // If screen is a part of Addons for Elementor plugin options page
        if (strpos($screen->id, $this->plugin_slug) !== false) {

            wp_enqueue_script('jquery-ui-datepicker');

            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');

            wp_register_style('lae-admin-styles', LAE_PLUGIN_URL . 'admin/assets/css/lae-admin.css', array(), LAE_VERSION);
            wp_enqueue_style('lae-admin-styles');

            wp_register_script('lae-admin-scripts', LAE_PLUGIN_URL . 'admin/assets/js/lae-admin' . $suffix . '.js', array(), LAE_VERSION, true);
            wp_enqueue_script('lae-admin-scripts');

            wp_register_style('lae-admin-page-styles', LAE_PLUGIN_URL . 'admin/assets/css/lae-admin-page.css', array(), LAE_VERSION);
            wp_enqueue_style('lae-admin-page-styles');
        }

        if (strpos($screen->id, $this->plugin_slug . '_documentation') !== false || strpos($screen->id, $this->plugin_slug . '_pro_upgrade') !== false) {

            // Load scripts and styles for documentation
            wp_register_script('lae-doc-scripts', LAE_PLUGIN_URL . 'admin/assets/js/documentation' . $suffix . '.js', array(), LAE_VERSION, true);
            wp_enqueue_script('lae-doc-scripts');

            wp_register_style('lae-doc-styles', LAE_PLUGIN_URL . 'admin/assets/css/documentation.css', array(), LAE_VERSION);
            wp_enqueue_style('lae-doc-styles');

            // Thickbox
            add_thickbox();

        }

        if (strpos($screen->id, $this->plugin_slug . '_pro_upgrade') !== false) {

            // Load scripts and styles for premium upgrade
            wp_register_script('lae-pro-upgrade-scripts', LAE_PLUGIN_URL . 'admin/assets/js/premium-upgrade' . $suffix . '.js', array(), LAE_VERSION, true);
            wp_enqueue_script('lae-pro-upgrade-scripts');

            wp_register_style('lae-pro-upgrade-styles', LAE_PLUGIN_URL . 'admin/assets/css/premium-upgrade.css', array(), LAE_VERSION);
            wp_enqueue_style('lae-pro-upgrade-styles');

        }

    }

}

new LAE_Admin;