<?php

class WCMp_Stripe_Gateway_Shortcode {

    public function __construct() {
        // stripe connect shortcodes
    }

    /**
     * Helper Functions
     */

    /**
     * Shortcode Wrapper
     *
     * @access public
     * @param mixed $function
     * @param array $atts (default: array())
     * @return string
     */
    public function shortcode_wrapper($function, $atts = array()) {
        ob_start();
        call_user_func($function, $atts);
        return ob_get_clean();
    }

    /**
     * Shortcode CLass Loader
     *
     * @access public
     * @param mixed $class_name
     * @return void
     */
    public function load_class($class_name = '') {
        global $WCMp_Stripe_Gateway;
        if ('' != $class_name && '' != $WCMp_Stripe_Gateway->token) {
            require_once ('shortcode/class-' . esc_attr($WCMp_Stripe_Gateway->token) . '-shortcode-' . esc_attr($class_name) . '.php');
        }
    }

}

?>
