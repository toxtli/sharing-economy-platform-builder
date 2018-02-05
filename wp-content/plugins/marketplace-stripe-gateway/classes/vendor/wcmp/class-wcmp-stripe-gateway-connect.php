<?php

if (!defined('ABSPATH')) {
    exit;
}

use Stripe\Stripe;
use Stripe\Transfer;

class WCMp_Gateway_Stripe_Connect extends WCMp_Payment_Gateway {

    public $id;
    public $message = array();
    public $is_connected = false;
    private $stripe_user_id;
    private $stripe_settings;
    private $is_testmode;
    private $secret_key;

    public function __construct() {
        $this->id = 'stripe_masspay';
        $this->payment_gateway = $this->id;
        $this->enabled = get_wcmp_vendor_settings('payment_method_stripe_masspay', 'payment');
    }

    public function process_payment($vendor, $commissions = array(), $transaction_mode = 'auto') {
        $this->vendor = $vendor;
        $this->commissions = $commissions;
        $this->currency = get_woocommerce_currency();
        $this->transaction_mode = $transaction_mode;
        $this->is_connected = get_user_meta($this->vendor->id, 'vendor_connected', true);
        $this->stripe_user_id = get_user_meta($this->vendor->id, 'stripe_user_id', true);
        $this->stripe_settings = get_option('woocommerce_stripe_settings');
        $this->is_testmode = $this->stripe_settings['testmode'] === "yes" ? true : false;
        $this->secret_key = $this->is_testmode ? $this->stripe_settings['test_secret_key'] : $this->stripe_settings['secret_key'];
        if ($this->validate_request()) {
            if($this->process_stripe_payment()){
                $this->record_transaction();
                if ($this->transaction_id) {
                    return array('message' => __('New transaction has been initiated', 'marketplace-stripe-gateway'), 'type' => 'success', 'transaction_id' => $this->transaction_id);
                }
            } else{
                return $this->message;
            }
        } else{
            return $this->message;
        }
    }

    public function validate_request() {
        global $WCMp;
        if ($this->enabled != 'Enable') {
            $this->message[] = array('message' => __('Invalid payment method', 'marketplace-stripe-gateway'), 'type' => 'error');
            return false;
        } else if (!$this->is_connected && !$this->stripe_user_id) {
            $this->message[] = array('message' => __('Please connect with stripe account', 'marketplace-stripe-gateway'), 'type' => 'error');
            return false;
        } else if (!$this->secret_key) {
            $this->message[] = array('message' => __('Stripe setting is not configured properly please contact site administrator', 'marketplace-stripe-gateway'), 'type' => 'error');
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
                $this->message[] = array('message' => __('Minimum thesold amount to withdrawal commission is ' . $thesold_amount, 'marketplace-stripe-gateway'), 'type' => 'error');
                return false;
            }
        }
        return parent::validate_request();
    }

    private function process_stripe_payment() {
        try {
            Stripe::setApiKey($this->secret_key);
            $transfer_args = array(
                'amount' => $this->get_stripe_amount(),
                'currency' => $this->currency,
                'destination' => $this->stripe_user_id
            );
            return Transfer::create($transfer_args);
        } catch (\Stripe\Error\InvalidRequest $e) {
            $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
            doWooStripeLOG(print_r($e->getMessage(), true));
        } catch (\Stripe\Error\Authentication $e) {
            $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
            doWooStripeLOG(print_r($e->getMessage(), true));
        } catch (\Stripe\Error\ApiConnection $e) {
            $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
            doWooStripeLOG(print_r($e->getMessage(), true));
        } catch (\Stripe\Error\Base $e) {
            $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
            doWooStripeLOG(print_r($e->getMessage(), true));
        } catch (Exception $e) {
            $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
            doWooStripeLOG(print_r($e->getMessage(), true));
        }
        return false;
    }
    
    private function get_stripe_amount(){
        $amount_to_pay = round($this->get_transaction_total() - $this->transfer_charge($this->transaction_mode) - $this->gateway_charge(), 2);
        switch (strtoupper($this->currency)) {
            // Zero decimal currencies.
            case 'BIF' :
            case 'CLP' :
            case 'DJF' :
            case 'GNF' :
            case 'JPY' :
            case 'KMF' :
            case 'KRW' :
            case 'MGA' :
            case 'PYG' :
            case 'RWF' :
            case 'VND' :
            case 'VUV' :
            case 'XAF' :
            case 'XOF' :
            case 'XPF' :
                $amount_to_pay = absint($amount_to_pay);
                break;
            default :
                $amount_to_pay = round($amount_to_pay, 2) * 100; // In cents.
                break;
        }
        return $amount_to_pay;
    }

}
