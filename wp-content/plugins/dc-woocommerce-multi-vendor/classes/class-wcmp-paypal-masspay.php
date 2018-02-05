<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class 		WCMp Paypal Masspay Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */ 
class WCMp_Paypal_Masspay {
	
	public $is_masspay_enable;
	public $payment_schedule;
	public $api_username;
	public $api_pass;
	public $api_signature;	
	public $test_mode;
	
	public function __construct() {
		$masspay_admin_settings = get_option("wcmp_payment_settings_name");		
		if($masspay_admin_settings  && array_key_exists('payment_method_paypal_masspay', $masspay_admin_settings)) {
			$this->is_masspay_enable = true;
			$this->payment_schedule = $masspay_admin_settings['payment_schedule'];
			$this->api_username = get_wcmp_vendor_settings('api_username','payment','paypal_masspay');// $masspay_admin_settings['api_username'];
			$this->api_pass = get_wcmp_vendor_settings('api_pass','payment','paypal_masspay'); //$masspay_admin_settings['api_pass'];
			$this->api_signature = get_wcmp_vendor_settings('api_signature','payment','paypal_masspay'); //$masspay_admin_settings['api_signature'];
			if(get_wcmp_vendor_settings('is_testmode','payment','paypal_masspay') == 'Enable') {
				$this->test_mode = true;
			}
		}
                add_action('wcmp_payment_cron_paypal_masspay', array($this, 'do_paypal_masspay'));
	}
	
	/**
	 * Init payPal Mass pay api
	 */
	public function call_masspay_api($receiver_information) {
		global $WCMp;
		doProductVendorLOG(json_encode($receiver_information));
		require_once($WCMp->plugin_path.'lib/paypal/CallerService.php');
		//session_start();
		$emailSubject = urlencode('You have money!');
		$receiverType = urlencode('EmailAddress');
		$currency = urlencode(get_woocommerce_currency());
		$nvpstr = '';
		if($receiver_information) {
			$j = 0;
			foreach($receiver_information as $receiver) {				
				$receiverEmail = urlencode($receiver['recipient']);
				$amount = urlencode(round($receiver['total'],2));
				$uniqueID = urlencode($receiver['vendor_id']);
				$note = urlencode($receiver['payout_note']);
				$nvpstr.="&L_EMAIL$j=$receiverEmail&L_Amt$j=$amount&L_UNIQUEID$j=$uniqueID&L_NOTE$j=$note";
				$j++;
			}
			$nvpstr.="&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=$receiverType&CURRENCYCODE=$currency" ;			
			doProductVendorLOG($nvpstr);			
			$resArray=hash_call("MassPay",$nvpstr);			
			$ack = strtoupper($resArray["ACK"]);
			if($ack == "SUCCESS" ||  $ack == "SuccessWithWarning" ){
				doProductVendorLOG(json_encode($resArray));
				return $resArray;
			} else {
				doProductVendorLOG(json_encode($resArray));
				return false;
			}
		}
		return false;
	}

	/**
	 * Process PayPal masspay 
	 */
	public function do_paypal_masspay($data=array()) {
		global $WCMp;
                $commissions_data = isset($data['payment_data']) ? $data['payment_data'] : array();
                $transaction_data = isset($data['transaction_data']) ? $data['transaction_data'] : array();
		$vendors_data = array();
		//doProductVendorLOG(json_encode($commissions_data));
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
			$result = $this->call_masspay_api($vendors_data);
			if($result) {
				// create a new transaction by vendor
				$WCMp->transaction->insert_new_transaction($transaction_data, 'wcmp_completed', 'paypal_masspay', $result);
			}
		}
	}

	
}
?>