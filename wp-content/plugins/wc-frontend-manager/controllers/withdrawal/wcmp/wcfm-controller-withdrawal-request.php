<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Marketplace Withdrawal Request Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/wcmp/controllers
 * @version   2.5.2
 */

class WCFM_Withdrawal_Request_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCMp;
		
		$wcfm_withdrawal_manage_form_data = array();
	  parse_str($_POST['wcfm_withdrawal_manage_form'], $wcfm_withdrawal_manage_form_data);
	  
	  $commissions = array();
	  if( isset( $wcfm_withdrawal_manage_form_data['commissions'] ) && !empty( $wcfm_withdrawal_manage_form_data['commissions'] ) ) {
	  	$commissions = $wcfm_withdrawal_manage_form_data['commissions'];
	  	
	  	$vendor = get_wcmp_vendor( apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) );
			$payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
			if ($payment_method) {
				if (array_key_exists($payment_method, $WCMp->payment_gateway->payment_gateways)) {
						$response = $WCMp->payment_gateway->payment_gateways[$payment_method]->process_payment($vendor, $commissions, 'manual');
						if ($response) {
							if (isset($response['transaction_id'])) {
								echo '{"status": true, "message": "' . __('Request successfully sent', 'wc-frontend-manager') . ': #' . $response['transaction_id'] . '"}';
								// $response['transaction_id'];
							} else {
								foreach ($response as $message) {
									echo '{"status": false, "message": "' . $message['message'] . '"}';
								}
							}
						} else {
							echo '{"status": false, "message": "' . __('Something went wrong please try again later', 'dc-woocommerce-multi-vendor') . '"}';
						}
				} else {
					echo '{"status": false, "message": "' . __('Invalid payment method', 'dc-woocommerce-multi-vendor') . '"}';
				}
			} else {
				echo '{"status": false, "message": "' . __('No payment method selected for withdrawal commission', 'dc-woocommerce-multi-vendor') . '"}';
			}
	  } else {
	  	echo '{"status": false, "message": "' . __('No commission selected for withdrawal', 'dc-woocommerce-multi-vendor') . '"}';
	  }
		
		die;
	}
}