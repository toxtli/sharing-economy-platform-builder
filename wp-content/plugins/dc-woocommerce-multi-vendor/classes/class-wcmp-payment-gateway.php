<?php

if (!defined('ABSPATH')) {
    exit;
}

abstract class WCMp_Payment_Gateway {
    /* is enable gateway */

    public $enabled = 'Enable';
    /* Gateway id */
    public $payment_gateway;
    /* WCMp vendor object */
    public $vendor;
    /* array of commission ids */
    public $commissions = array();
    /* Transaction id */
    public $transaction_id;
    public $currency;
    public $transaction_mode;

    public function validate_request() {
        return true;
    }

    public function process_payment($vendor, $commissions = array(), $transaction_mode = 'auto') {
        return array();
    }

    public function record_transaction() {
        if ($this->transaction_mode == 'manual' && $this->payment_gateway == 'direct_bank') {
            $commission_status = 'wcmp_processing';
        } else {
            $commission_status = 'wcmp_completed';
        }
        $transaction_args = array(
            'post_type' => 'wcmp_transaction',
            'post_title' => sprintf(__('Transaction - %s', 'dc-woocommerce-multi-vendor'), strftime(_x('%B %e, %Y @ %I:%M %p', 'Transaction date parsed by strftime', 'dc-woocommerce-multi-vendor'))),
            'post_status' => $commission_status,
            'ping_status' => 'closed',
            'post_author' => $this->vendor->term_id
        );
        $this->transaction_id = wp_insert_post($transaction_args);
        if (!is_wp_error($this->transaction_id) && $this->transaction_id) {
            $this->update_meta_data($commission_status);
            $this->email_notify($commission_status);
        }
    }

    public function get_transaction_total() {
        $transaction_total = 0;
        if (is_array($this->commissions)) {
            foreach ($this->commissions as $commission) {
                $commission_amount = get_wcmp_vendor_order_amount(array('commission_id' => $commission, 'vendor_id' => $this->vendor->id));
                $transaction_total += (float) $commission_amount['total'];
            }
        }
        return apply_filters('wcmp_commission_transaction_amount', $transaction_total, $this->vendor->id, $this->commissions, $this->payment_gateway);
    }

    public function transfer_charge() {
        global $WCMp;
        $transfer_charge = 0;
        if ($this->transaction_mode == 'manual') {
            $no_of_orders = isset($WCMp->vendor_caps->payment_cap['no_of_orders']) && $WCMp->vendor_caps->payment_cap['no_of_orders'] ? $WCMp->vendor_caps->payment_cap['no_of_orders'] : 0;
            if (count($WCMp->transaction->get_transactions($this->vendor->term_id)) >= $no_of_orders) {
                $transfer_charge = (float) get_wcmp_vendor_settings('commission_transfer', 'payment', '', 0);
            }
        }
        return apply_filters('wcmp_commission_transfer_charge_amount', $transfer_charge, $this->get_transaction_total(), $this->vendor, $this->commissions, $this->payment_gateway);
    }

    public function gateway_charge() {
        $gateway_charge = 0;
        $is_enable_gateway_charge = get_wcmp_vendor_settings('payment_gateway_charge', 'payment');
        if ($is_enable_gateway_charge == 'Enable') {
            if(get_wcmp_vendor_settings("gateway_charge_{$this->payment_gateway}", "payment")){
                $gateway_charge_percent = floatval(get_wcmp_vendor_settings("gateway_charge_{$this->payment_gateway}", "payment"));
                $gateway_charge = ($this->get_transaction_total() * $gateway_charge_percent) / 100;
            }
        }
        return apply_filters('wcmp_commission_gateway_charge_amount', $gateway_charge, $this->get_transaction_total(), $this->vendor, $this->commissions, $this->payment_gateway);
    }

    public function update_meta_data($commission_status = 'wcmp_processing') {
        update_post_meta($this->transaction_id, 'transaction_mode', $this->payment_gateway);
        update_post_meta($this->transaction_id, 'payment_mode', $this->transaction_mode);
        $transfar_charge = $this->transfer_charge($this->transaction_mode);
        update_post_meta($this->transaction_id, 'transfer_charge', $transfar_charge);
        $gateway_charge = $this->gateway_charge();
        update_post_meta($this->transaction_id, 'gateway_charge', $gateway_charge);
        $transaction_amount = $this->get_transaction_total();
        update_post_meta($this->transaction_id, 'amount', $transaction_amount);
        $total_amount = $transaction_amount - $transfar_charge - $gateway_charge;
        update_post_meta($this->transaction_id, 'total_amount', $total_amount);
        update_post_meta($this->transaction_id, 'commission_detail', $this->commissions);

        foreach ($this->commissions as $commission) {
            update_post_meta($commission, '_paid_request', $this->payment_gateway);
            if ($commission_status == 'wcmp_completed') {
                wcmp_paid_commission_status($commission);
                update_post_meta($this->transaction_id, 'paid_date', date("Y-m-d H:i:s"));
            }
        }
        do_action('wcmp_transaction_update_meta_data', $commission_status, $this->transaction_id, $this->vendor);
    }

    public function email_notify($commission_status = 'wcmp_processing') {
        switch ($this->payment_gateway) {
            case 'direct_bank':
                $email_vendor = WC()->mailer()->emails['WC_Email_Vendor_Direct_Bank'];
                $email_vendor->trigger($this->transaction_id, $this->vendor->term_id);
                $email_admin = WC()->mailer()->emails['WC_Email_Admin_Widthdrawal_Request'];
                $email_admin->trigger($this->transaction_id, $this->vendor->term_id);
                break;
            case 'paypal_masspay':
            case 'paypal_payout':
                $email_admin = WC()->mailer()->emails['WC_Email_Admin_Widthdrawal_Request'];
                $email_admin->trigger($this->transaction_id, $this->vendor->term_id);
                break;
            default :
                break;
        }
        do_action('wcmp_transaction_email_notification', $this->payment_gateway, $commission_status, $this->transaction_id, $this->vendor);
    }

}
