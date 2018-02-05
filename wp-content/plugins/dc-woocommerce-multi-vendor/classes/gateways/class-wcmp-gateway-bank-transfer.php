<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Gateway_Bank_Transfer extends WCMp_Payment_Gateway {

    public $id;
    public $message = array();

    public function __construct() {
        $this->id = 'direct_bank';
        $this->payment_gateway = $this->id;
        $this->enabled = get_wcmp_vendor_settings('payment_method_direct_bank', 'payment');
    }

    public function process_payment($vendor, $commissions = array(), $transaction_mode = 'auto') {
        $this->vendor = $vendor;
        $this->commissions = $commissions;
        $this->currency = get_woocommerce_currency();
        $this->transaction_mode = $transaction_mode;
        if ($this->validate_request()) {
            $this->record_transaction();
            if ($this->transaction_id) {
                return array('message' => __('New transaction has been initiated', 'dc-woocommerce-multi-vendor'), 'type' => 'success', 'transaction_id' => $this->transaction_id);
            }
        } else {
            return $this->message;
        }
    }

    public function validate_request() {
        global $WCMp;
        if ($this->enabled != 'Enable') {
            $this->message[] = array('message' => __('Invalid payment method', 'dc-woocommerce-multi-vendor'), 'type' => 'error');
            return false;
        }
        if ($this->transaction_mode != 'admin') {
            /* handel thesold time */
            $threshold_time = isset($WCMp->vendor_caps->payment_cap['commission_threshold_time']) && !empty($WCMp->vendor_caps->payment_cap['commission_threshold_time']) ? $WCMp->vendor_caps->payment_cap['commission_threshold_time'] : 0;
            if ($threshold_time > 0) {
                foreach ($this->commissions as $index => $commission) {
                    if (intval((date('U') - get_the_date('U', $commission)) / (3600 * 24)) < $threshold_time) {
                        unset($this->commissions[$index]);
                    }
                }
            }
            /* handel thesold amount */
            $thesold_amount = isset($WCMp->vendor_caps->payment_cap['commission_threshold']) && !empty($WCMp->vendor_caps->payment_cap['commission_threshold']) ? $WCMp->vendor_caps->payment_cap['commission_threshold'] : 0;
            if ($this->get_transaction_total() > $thesold_amount) {
                return true;
            } else {
                $this->message[] = array('message' => __('Minimum thesold amount to withdrawal commission is ' . $thesold_amount, 'dc-woocommerce-multi-vendor'), 'type' => 'error');
                return false;
            }
        }
        return parent::validate_request();
    }

}
