<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class WCMp_Paypal_Payout {
    
    public $client_id;
    public $client_secret;
    public $test_mode;
    public $access_token;
    public $token_type;

    public function __construct() {        
        
        add_action('wcmp_payment_cron_paypal_payout', array($this, 'do_paypal_payout'));
    }
    
    public function call_paypal_payout_api($receiver) {
        global $WCMp;
        if(empty($this->access_token)) return false;
        $authorization = "Authorization: {$this->token_type} {$this->access_token}";
        $api_endpoint = ($this->test_mode == 'yes') ? 'https://api.sandbox.paypal.com/v1/payments/payouts?sync_mode=true' : 'https://api.paypal.com/v1/payments/payouts?sync_mode=true';
        $request_params = '{
		"sender_batch_header": {
                    "sender_batch_id":"'.uniqid().'",
                    "email_subject": "You have a payment",
                    "recipient_type": "EMAIL"
                },
                "items": [
                  {
                    "recipient_type": "EMAIL",
                    "amount": {
                      "value": '.round($receiver['total'],2).',
                      "currency": "'.$receiver['currency'].'"
                    },
                    "receiver": "'.$receiver['recipient'].'",
                    "note": "'.$receiver['payout_note'].'",
                    "sender_item_id": "'.$receiver['vendor_id'].'"
                  }
                ]
	}';
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type:application/json', $authorization));
	curl_setopt($curl, CURLOPT_VERBOSE, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_URL, $api_endpoint);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $request_params);
	curl_setopt($curl, CURLOPT_SSLVERSION, 6);
	
	$result = curl_exec($curl);
	curl_close($curl);
	$result_array = json_decode($result, true);
        //doProductVendorLOG($result);
        $batch_status = $result_array['batch_header']['batch_status'];
        $transaction_status = $result_array['items'][0]['transaction_status'];
        if( $batch_status == 'SUCCESS' && $transaction_status == 'SUCCESS' ) {
            doProductVendorLOG(json_encode($result_array));
            return $result_array;
        } else {
            doProductVendorLOG(json_encode($result_array));
            return false;
        }
    }
    
    public function generate_token() {
        $masspay_admin_settings = get_option("wcmp_payment_settings_name");		
        if($masspay_admin_settings  && array_key_exists('payment_method_paypal_payout', $masspay_admin_settings)) {
            $this->client_id = get_wcmp_vendor_settings('client_id','payment','paypal_payout'); //isset($masspay_admin_settings['client_id']) ? $masspay_admin_settings['client_id'] : '';
            $this->client_secret = get_wcmp_vendor_settings('client_secret','payment','paypal_payout'); //isset($masspay_admin_settings['client_secret']) ? $masspay_admin_settings['client_secret'] : '';
            if(get_wcmp_vendor_settings('is_testmode','payment','paypal_payout') == 'Enable') {
                $this->test_mode = true;
            }
        }
        $api_endpoint = ($this->test_mode) ? 'https://api.sandbox.paypal.com/v1/oauth2/token' : 'https://api.paypal.com/v1/oauth2/token';
        $request_params = 'grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Accept-Language: en_US'));
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $api_endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $this->client_id.':'.$this->client_secret);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_params);
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);

        $result = curl_exec($curl);
        curl_close($curl);
        $result_array = json_decode($result, true);
        //doProductVendorLOG($result);
        $this->access_token = isset($result_array['access_token']) ? $result_array['access_token'] : '';
        $this->token_type = isset($result_array['token_type']) ? $result_array['token_type'] : '';
    }

    /**
     * Process PayPal masspay 
     */
    public function do_paypal_payout($data=array()) {
        global $WCMp;
        $this->generate_token();
        //doProductVendorLOG(json_encode($data));die('dfghdf');
        $commissions_data = isset($data['payment_data']) ? $data['payment_data'] : array();
        $transaction_data = isset($data['transaction_data']) ? $data['transaction_data'] : array();
        $vendors_data = array();
        
        foreach( $commissions_data as $commission_data ) {  
            // Get vendor data
            $vendor = get_wcmp_vendor_by_term($commission_data['vendor_id']);
            $vendor_paypal_email = get_user_meta($vendor->id, '_vendor_paypal_email', true);
            // Set vendor recipient field
            if( isset( $vendor_paypal_email ) && strlen( $vendor_paypal_email ) > 0 ) {
                $recipient = $vendor_paypal_email;
                $vendors_data[] = array( 
                    'recipient' => $recipient,
                    'total' => round($commission_data['total'], 2),
                    'currency' => $commission_data['currency'],
                    'vendor_id' =>$commission_data['vendor_id'],
                    'payout_note' =>$commission_data['payout_note']
                );
            }
        }
        if(!empty($vendors_data)) {
            foreach($vendors_data as $vendor_data) {
                doProductVendorLOG(json_encode($vendor_data));
                $result = $this->call_paypal_payout_api($vendor_data);
                if($result) {
                    // create a new transaction by vendor
                    $WCMp->transaction->insert_new_transaction($transaction_data, 'wcmp_completed', 'paypal_payout', $result);
                }
            }
        }
    }


    
    public function process_paypal_single_payout($commission_data=array()) {
        global $WCMp;
        $this->generate_token();
        return $this->call_paypal_payout_api($commission_data);
    }
    
}