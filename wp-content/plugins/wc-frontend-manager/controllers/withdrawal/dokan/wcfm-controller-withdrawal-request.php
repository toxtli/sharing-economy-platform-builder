<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Dokan Withdrawal Request Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/withdrawal/dokan/controllers
 * @version   3.3.0
 */

class WCFM_Withdrawal_Request_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		$balance        = dokan_get_seller_balance( $vendor_id, false );
		$withdraw_limit =  dokan_get_option( 'withdraw_limit', 'dokan_withdraw', 0 );
		
		$wcfm_withdrawal_manage_form_data = array();
	  parse_str($_POST['wcfm_withdrawal_manage_form'], $wcfm_withdrawal_manage_form_data);
	  
	  $commissions = array();
	  if( isset( $wcfm_withdrawal_manage_form_data['withdraw_amount'] ) && !empty( $wcfm_withdrawal_manage_form_data['withdraw_amount'] ) ) {
	  	if( isset( $wcfm_withdrawal_manage_form_data['withdraw_method'] ) && !empty( $wcfm_withdrawal_manage_form_data['withdraw_method'] ) ) {
				$withdraw_amount = (float) $wcfm_withdrawal_manage_form_data['withdraw_amount'];
				$withdraw_method = $wcfm_withdrawal_manage_form_data['withdraw_method'];
				if ( $withdraw_amount < $balance ) {
					if ( $withdraw_amount > $withdraw_limit ) {
							$wpdb->dokan_withdraw = $wpdb->prefix . 'dokan_withdraw';
							$data = array(
									'user_id' => $vendor_id,
									'amount'  => floatval( $withdraw_amount ),
									'date'    => current_time( 'mysql' ),
									'status'  => 0,
									'method'  => $withdraw_method,
									'note'    => '',
									'ip'      => dokan_get_client_ip()
							);
			
							$format = array( '%d', '%f', '%s', '%d', '%s', '%s', '%s' );
							if ( $wpdb->insert( $wpdb->dokan_withdraw, $data, $format ) ) {
								do_action( 'dokan_after_withdraw_request', $vendor_id, $withdraw_amount, $withdraw_method );
								echo '{"status": true, "message": "' . __('Request successfully sent', 'wc-frontend-manager') . '", "redirect": "' . wcfm_payments_url() . '"}';
							} else {
								echo '{"status": false, "message": "' . __('Something went wrong please try again later', 'wc-frontend-manager') . '"}';
							}
					} else {
						echo '{"status": false, "message": "' . sprintf( __( 'Withdraw amount must be greater than %d', 'dokan-lite' ), $withdraw_amount ) . '"}';
					}
				} else {
					echo '{"status": false, "message": "' . __('You don\'t have enough balance for this request', 'dokan-lite') . $withdraw_amount .'>'. $balance . '"}';
				}
			} else {
				echo '{"status": false, "message": "' . __('Withdraw method required', 'dokan-lite') . '"}';
			}
	  } else {
	  	echo '{"status": false, "message": "' . __('Withdraw amount required ', 'dokan-lite') . '"}';
	  }
		
		die;
	}
}