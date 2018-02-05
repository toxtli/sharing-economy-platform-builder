<?php

/**
 * WCMp Vendor Dashboard Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		WC Marketplace
 */
class WCMp_Vendor_Dashboard_Shortcode {

    public function __construct() {
        
    }

    /**
     * Output the vendor dashboard shortcode.
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($attr) {
        global $WCMp, $wp;
        $WCMp->nocache();
        if (!defined('MNDASHBAOARD')) {
            define('MNDASHBAOARD', true);
        }
        if (!is_user_logged_in()) {
            echo '<div class="woocommerce">';
            wc_get_template('myaccount/form-login.php');
            echo '</div>';
        } else if (!is_user_wcmp_vendor(get_current_vendor_id())) {
            $WCMp->template->get_template('shortcode/non_vendor_dashboard.php');
        } else {
            $WCMp->template->get_template('shortcode/vendor_dashboard.php');
        }
    }

}
