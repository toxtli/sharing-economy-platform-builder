<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Dokan Payments Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/withdrawal/dokan/controllers
 * @version   3.3.0
 */

class WCFM_Payments_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCMp;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
    
    if( isset($_POST['start_date']) && !empty($_POST['start_date']) ) {
    	$start_date = date('Y-m-d', strtotime($_POST['start_date']) );
    }
    
    if( isset($_POST['end_date']) && !empty($_POST['end_date']) ) {
    	$end_date = date('Y-m-d', strtotime($_POST['end_date']) );
    }
    
    $status_filter = '';
    if( isset($_POST['status_type']) && ( $_POST['status_type'] != '' ) ) {
    	$status_filter = " AND `status` = " . $_POST['status_type'];
    }
    
    $vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
    
    $sql = "SELECT COUNT(id) FROM {$wpdb->prefix}dokan_withdraw";
		$sql .= " WHERE 1=1";
		$sql .= " AND `user_id` = %d";
		$sql .= $status_filter; 
		$sql .= " AND DATE( date ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		
		$wcfm_payment_items = $wpdb->get_var( $wpdb->prepare( $sql, $vendor_id ) );
		if( !$wcfm_payment_items ) $wcfm_payment_items = 0;
		
		$sql = "SELECT * FROM {$wpdb->prefix}dokan_withdraw";
		$sql .= " WHERE 1=1";
		$sql .= " AND `user_id` = %d";
		$sql .= $status_filter;
		$sql .= " AND DATE( date ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		$sql .= " LIMIT {$length}";
		$sql .= " OFFSET {$offset}";
		$wcfm_payments_array = $wpdb->get_results( $wpdb->prepare( $sql, $vendor_id ) );
		
		// Generate Payments JSON
		$wcfm_payments_json = '';
		$wcfm_payments_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $wcfm_payment_items . ',
															"recordsFiltered": ' . $wcfm_payment_items . ',
															"data": ';
		if(!empty($wcfm_payments_array)) {
			$index = 0;
			$wcfm_payments_json_arr = array();
			foreach($wcfm_payments_array as $transaction_id => $wcfm_payments_single) {
				
				// Status
				if( $wcfm_payments_single->status == 1 ) {
					$wcfm_payments_json_arr[$index][] =  '<span class="payment-status tips wcicon-status-completed text_tip" data-tip="' . __('Completed', 'woocommerce') . '"></span>';
				} elseif( $wcfm_payments_single->status == 0 ) {
					$wcfm_payments_json_arr[$index][] =  '<span class="payment-status tips wcicon-status-processing text_tip" data-tip="' . __('Processing', 'woocommerce') . '"></span>';
				} elseif( $wcfm_payments_single->status == 2 ) {
					$wcfm_payments_json_arr[$index][] =  '<span class="payment-status tips wcicon-status-cancelled text_tip" data-tip="' . __('Cancel', 'woocommerce') . '"></span>';
				}
				
				// Amount
				$wcfm_payments_json_arr[$index][] = '<span class="withdrawal_amount">' . wc_price( $wcfm_payments_single->amount ) . '</span>';  
				
				// Payment Mode
				if ( ( $wcfm_payments_single->method == 'paypal' ) || ( $wcfm_payments_single->method == 'paypal_payout' ) ) {
					$wcfm_payments_json_arr[$index][] = __('PayPal', 'wc-frontend-manager');
				} else if ($wcfm_payments_single->method == 'stripe') {
					$wcfm_payments_json_arr[$index][] = __('Stripe', 'wc-frontend-manager');
				} else {
					$wcfm_payments_json_arr[$index][] = __('Bank Transfer', 'wc-frontend-manager');
				}
				
				// Date
				$wcfm_payments_json_arr[$index][] = date( 'Y-m-d H:i A', strtotime( $wcfm_payments_single->date ) );
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_payments_json_arr) ) $wcfm_payments_json .= json_encode($wcfm_payments_json_arr);
		else $wcfm_payments_json .= '[]';
		$wcfm_payments_json .= '
													}';
													
		echo $wcfm_payments_json;
	}
}