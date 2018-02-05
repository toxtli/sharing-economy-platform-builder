<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Gateway_Paypal_Payout extends WCMp_Payment_Gateway {

    public $id;
    public $message = array();
    private $client_id;
    private $client_secret;
    private $test_mode = false;
    private $payout_mode = 'true';
    private $reciver_email;
    private $api_endpoint;
    private $token_endpoint;
    private $access_token;
    private $token_type;

    public function __construct() {
        $this->id = 'paypal_payout';
        $this->payment_gateway = $this->id;
        $this->enabled = get_wcmp_vendor_settings('payment_method_paypal_payout', 'payment');
        $this->client_id = get_wcmp_vendor_settings('client_id', 'payment', 'paypal_payout');
        $this->client_secret = get_wcmp_vendor_settings('client_secret', 'payment', 'paypal_payout');
        if (get_wcmp_vendor_settings('is_asynchronousmode', 'payment', 'paypal_payout') == 'Enable') {
            $this->payout_mode = 'false';
        }
        $this->api_endpoint = 'https://api.paypal.com/v1/payments/payouts?sync_mode='.$this->payout_mode;
        $this->token_endpoint = 'https://api.paypal.com/v1/oauth2/token';
        if (get_wcmp_vendor_settings('is_testmode', 'payment', 'paypal_payout') == 'Enable') {
            $this->test_mode = true;
            $this->api_endpoint = 'https://api.sandbox.paypal.com/v1/payments/payouts?sync_mode='.$this->payout_mode;
            $this->token_endpoint = 'https://api.sandbox.paypal.com/v1/oauth2/token';
        }
    }

    public function process_payment($vendor, $commissions = array(), $transaction_mode = 'auto') {
        $this->vendor = $vendor;
        $this->commissions = $commissions;
        $this->currency = get_woocommerce_currency();
        $this->transaction_mode = $transaction_mode;
        $this->reciver_email = get_user_meta($this->vendor->id, '_vendor_paypal_email', true);
        if ($this->validate_request()) {
            $this->generate_access_token();
            $paypal_response = $this->process_paypal_payout();
            if ($paypal_response) {
                $this->record_transaction();
                if ($this->transaction_id) {
                    return array('message' => __('New transaction has been initiated', 'dc-woocommerce-multi-vendor'), 'type' => 'success', 'transaction_id' => $this->transaction_id);
                }
            } else {
                return false;
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
        } else if (!$this->client_id && !$this->client_secret) {
            $this->message[] = array('message' => __('Paypal payout setting is not configured properly please contact site administrator', 'dc-woocommerce-multi-vendor'), 'type' => 'error');
            return false;
        } else if (!$this->reciver_email) {
            $this->message[] = array('message' => __('Please update your paypal email to receive commission', 'dc-woocommerce-multi-vendor'), 'type' => 'error');
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

    private function generate_access_token() {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Accept-Language: en_US'));
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $this->token_endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $this->client_id . ':' . $this->client_secret);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);
        $response = curl_exec($curl);
        curl_close($curl);
        $response_array = json_decode($response, true);
        $this->access_token = isset($response_array['access_token']) ? $response_array['access_token'] : '';
        $this->token_type = isset($response_array['token_type']) ? $response_array['token_type'] : '';
    }

    private function process_paypal_payout() {
        $api_authorization = "Authorization: {$this->token_type} {$this->access_token}";
        $amount_to_pay = round($this->get_transaction_total() - $this->transfer_charge($this->transaction_mode) - $this->gateway_charge(), 2);
        $note = sprintf(__('Total commissions earned from %1$s as at %2$s on %3$s', 'dc-woocommerce-multi-vendor'), get_bloginfo('name'), date('H:i:s'), date('d-m-Y'));
        $request_params = '{
		"sender_batch_header": {
                    "sender_batch_id":"' . uniqid() . '",
                    "email_subject": "You have a payment",
                    "recipient_type": "EMAIL"
                },
                "items": [
                  {
                    "recipient_type": "EMAIL",
                    "amount": {
                      "value": ' . $amount_to_pay . ',
                      "currency": "' . $this->currency . '"
                    },
                    "receiver": "' . $this->reciver_email . '",
                    "note": "' . $note . '",
                    "sender_item_id": "' . $this->vendor->id . '"
                  }
                ]
	}';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type:application/json', $api_authorization));
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $this->api_endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_params);
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);
        $result = curl_exec($curl);
        curl_close($curl);
        $result_array = json_decode($result, true);
        $batch_status = $result_array['batch_header']['batch_status'];
        if($this->payout_mode == 'true'){
            $transaction_status = $result_array['items'][0]['transaction_status'];
            if ($batch_status == 'SUCCESS' && $transaction_status == 'SUCCESS') {
                return $result_array;
            } else {
                doProductVendorLOG(json_encode($result_array));
                return false;
            }
        }else{
            $batch_payout_status = apply_filters('wcmp_paypal_payout_batch_status', array('PENDING', 'PROCESSING', 'SUCCESS', 'NEW'));
            if (in_array($batch_status, $batch_payout_status) ) {
                return $result_array;
            } else {
                doProductVendorLOG(json_encode($result_array));
                return false;
            }
        }
    }

}
