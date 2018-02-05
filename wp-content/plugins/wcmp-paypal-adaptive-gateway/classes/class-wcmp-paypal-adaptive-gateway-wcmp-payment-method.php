<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Gateway_Paypal_Adaptive extends WCMp_Payment_Gateway {

    public $id;
    public $message = array();

    public function __construct() {
        $this->id = 'paypal_adaptive';
        $this->payment_gateway = $this->id;
        $this->enabled = get_wcmp_vendor_settings('payment_method_paypal_adaptive', 'payment');
    }

    public function process_payment($vendor, $commissions = array(), $transaction_mode = 'gateway') {
        $this->vendor = $vendor;
        $this->commissions = $commissions;
        $this->currency = get_woocommerce_currency();
        $this->transaction_mode = $transaction_mode;
        if ($this->transaction_mode != 'gateway') {
            $this->message[] = array('message' => __('you Can not process payment manualy by paypal adaptive gateway', 'wcmp-paypal-adaptive-gateway'), 'type' => 'error');
            return $this->message;
        }
        $this->record_transaction();
        if ($this->transaction_id) {
            return array('message' => __('New transaction has been initiated', 'wcmp-paypal-adaptive-gateway'), 'type' => 'success', 'transaction_id' => $this->transaction_id);
        }
    }

}
