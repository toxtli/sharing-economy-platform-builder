<?php

class WCMp_Stripe_Gateway_Library {

    public $lib_path;
    public $lib_url;
    public $php_lib_path;
    public $php_lib_url;
    public $jquery_lib_path;
    public $jquery_lib_url;

    public function __construct() {
        global $WCMp_Stripe_Gateway;

        $this->lib_path = $WCMp_Stripe_Gateway->plugin_path . 'lib/';

        $this->lib_url = $WCMp_Stripe_Gateway->plugin_url . 'lib/';

        $this->php_lib_path = $this->lib_path . 'php/';

        $this->php_lib_url = $this->lib_url . 'php/';

        $this->jquery_lib_path = $this->lib_path . 'jquery/';

        $this->jquery_lib_url = $this->lib_url . 'jquery/';
    }

    /**
     * PHP WP fields Library
     */
    public function load_wp_fields() {
        global $WCMp_Stripe_Gateway;
        if (!class_exists('DC_WP_Fields'))
            require_once ($this->php_lib_path . 'class-dc-wp-fields.php');
        $DC_WP_Fields = new DC_WP_Fields();
        return $DC_WP_Fields;
    }

    /**
     * Jquery qTip library
     */
    public function load_qtip_lib() {
        global $WCMp_Stripe_Gateway;
        wp_enqueue_script('qtip_js', $this->jquery_lib_url . 'qtip/qtip.js', array('jquery'), $WCMp_Stripe_Gateway->version, true);
        wp_enqueue_style('qtip_css', $this->jquery_lib_url . 'qtip/qtip.css', array(), $WCMp_Stripe_Gateway->version);
    }

    /**
     * WP Media library
     */
    public function load_upload_lib() {
        global $WCMp_Stripe_Gateway;
        wp_enqueue_media();
        wp_enqueue_script('upload_js', $this->jquery_lib_url . 'upload/media-upload.js', array('jquery'), $WCMp_Stripe_Gateway->version, true);
        wp_enqueue_style('upload_css', $this->jquery_lib_url . 'upload/media-upload.css', array(), $WCMp_Stripe_Gateway->version);
    }

    /**
     * WP ColorPicker library
     */
    public function load_colorpicker_lib() {
        global $WCMp_Stripe_Gateway;
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('colorpicker_init', $this->jquery_lib_url . 'colorpicker/colorpicker.js', array('jquery', 'wp-color-picker'), $WCMp_Stripe_Gateway->version, true);
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * WP DatePicker library
     */
    public function load_datepicker_lib() {
        global $WCMp_Stripe_Gateway;
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    }

    /**
     * Stripe Library
     */
    public function stripe_library() {
        global $WCMp_Stripe_Gateway;
        require( $this->lib_path . 'stripe-php/init.php' );
    }

}
