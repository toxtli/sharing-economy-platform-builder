<?php

/**
 * WCMp MassPay Cron Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_MassPay_Cron {

    public function __construct() {
        add_action('masspay_cron_start', array(&$this, 'do_mass_payment'));
    }

    /**
     * Calculate the amount and selete payment method.
     *
     *
     */
    function do_mass_payment() {
        global $WCMp;
        $payment_admin_settings = get_option('wcmp_payment_settings_name');
        if (!isset($payment_admin_settings['wcmp_disbursal_mode_admin'])) {
            return;
        }
        $commission_to_pay = array();
        $commissions = $this->get_query_commission();
        if ($commissions && is_array($commissions)) {
            foreach ($commissions as $commission) {
                $commission_id = $commission->ID;
                $vendor_term_id = get_post_meta($commission_id, '_commission_vendor', true);
                $commission_to_pay[$vendor_term_id][] = $commission_id;
            }
        }
        foreach ($commission_to_pay as $vendor_term_id => $commissions) {
            $vendor = get_wcmp_vendor_by_term($vendor_term_id);
            if ($vendor) {
                $payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                if ($payment_method && $payment_method != 'direct_bank') {
                    if (array_key_exists($payment_method, $WCMp->payment_gateway->payment_gateways)) {
                        $WCMp->payment_gateway->payment_gateways[$payment_method]->process_payment($vendor, $commissions);
                    }
                }
            }
        }
    }

    /**
     * Get Commissions
     *
     * @return object $commissions
     */
    public function get_query_commission() {
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'meta_key' => '_paid_status',
            'meta_value' => 'unpaid',
            'posts_per_page' => 5
        );
        $commissions = get_posts($args);
        return $commissions;
    }

}
