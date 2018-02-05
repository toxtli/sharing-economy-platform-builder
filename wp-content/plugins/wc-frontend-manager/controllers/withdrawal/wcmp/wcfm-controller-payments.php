<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Marketplace Payments Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/wcmp/controllers
 * @version   2.5.2
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
		
		$start_date = date('01-m-Y');
    $end_date = date('t-m-Y');
    
    if( isset($_POST['start_date']) && !empty($_POST['start_date']) ) {
    	$start_date = date('d-m-Y', strtotime($_POST['start_date']) );
    }
    
    if( isset($_POST['end_date']) && !empty($_POST['end_date']) ) {
    	$end_date = date('d-m-Y', strtotime($_POST['end_date']) );
    }
    
    $vendor_term_id = apply_filters( 'wcfm_payments_args', 0 );
		
		$wcfm_payments_array = $WCMp->transaction->get_transactions( $vendor_term_id, $start_date, $end_date, false, $offset, $length );
		
		$filtered_payment_count = count( $wcfm_payments_array );
		
		// Generate Payments JSON
		$wcfm_payments_json = '';
		$wcfm_payments_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $filtered_payment_count . ',
															"recordsFiltered": ' . $filtered_payment_count . ',
															"data": ';
		if(!empty($wcfm_payments_array)) {
			$index = 0;
			$wcfm_payments_json_arr = array();
			foreach($wcfm_payments_array as $transaction_id => $wcfm_payments_single) {
				
				// Status
				if( $wcfm_payments_single['status'] == 'wcmp_completed' ) {
					$wcfm_payments_json_arr[$index][] =  '<span class="payment-status tips wcicon-status-completed text_tip" data-tip="' . __('Completed', 'woocommerce') . '"></span>';
				} else {
					$wcfm_payments_json_arr[$index][] =  '<span class="payment-status tips wcicon-status-processing text_tip" data-tip="' . __('Processing', 'woocommerce') . '"></span>';
				}
				
				// Transc.ID
				$wcfm_payments_json_arr[$index][] = '<span class="wcfm_dashboard_item_title"># ' . $transaction_id . '</span>';  
				
				// Commission IDs
				$wcfm_payments_json_arr[$index][] =  '<span class="wcfm_dashboard_item_title transaction_commission_ids">#'.  implode(', #', $wcfm_payments_single['commission_details'] ) . '</span>';
				
				// Fee
				$wcfm_payments_json_arr[$index][] = wc_price( $wcfm_payments_single['transfer_charge'] );  
				
				// Net Earnings
				$wcfm_payments_json_arr[$index][] = wc_price( $wcfm_payments_single['total_amount'] );  
				
				// Payment Mode
				if ( ( $wcfm_payments_single['mode'] == 'paypal_masspay' ) || ( $wcfm_payments_single['mode'] == 'paypal_payout' ) ) {
					$wcfm_payments_json_arr[$index][] = __('PayPal', 'wc-frontend-manager');
				} else if ($wcfm_payments_single['mode'] == 'stripe') {
					$wcfm_payments_json_arr[$index][] = __('Stripe', 'wc-frontend-manager');
				} else {
					$wcfm_payments_json_arr[$index][] = __('Direct Bank Transfer', 'wc-frontend-manager');
				}
				
				// Date
				$wcfm_payments_json_arr[$index][] = date( 'Y-m-d H:i A', strtotime( $wcfm_payments_single['post_date'] ) );
				
				
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