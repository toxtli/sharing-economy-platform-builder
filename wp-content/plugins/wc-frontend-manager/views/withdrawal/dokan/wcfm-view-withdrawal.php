<?php
/**
 * WCFM plugin view
 *
 * WCFM Dokan Withdrawal Request View
 *
 * @author 		WC Lovers
 * @package 	wcfm/withdrawal/dokan/view
 * @version   3.3.0
 */
 
global $WCFM, $woocommerce, $wpdb;

$wcfm_is_allow_withdrawal = apply_filters( 'wcfm_is_allow_withdrawal', true );
if( !$wcfm_is_allow_withdrawal ) {
	wcfm_restriction_message_show( "Withdrawal" );
	return;
}

$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

$wpdb->dokan_withdraw = $wpdb->prefix . 'dokan_withdraw';

$status = $wpdb->get_results( $wpdb->prepare(
		"SELECT id
		 FROM {$wpdb->prefix}dokan_withdraw
		 WHERE user_id = %d AND status = 0", $vendor_id
) );

$balance        = dokan_get_seller_balance( $vendor_id, true );
$withdraw_limit = dokan_get_option( 'withdraw_limit', 'dokan_withdraw', -1 );
$threshold      = dokan_get_option( 'withdraw_date_limit', 'dokan_withdraw', -1 );

$message = sprintf( __('Current Balance: %s ', 'dokan-lite' ), $balance );

if ( $withdraw_limit != -1 ) {
		$message .= sprintf( __( '<br>Minimum Withdraw amount: %s ', 'dokan-lite' ), wc_price( $withdraw_limit ) );
}
if ( $threshold != -1 ) {
		$message .= sprintf( __( '<br>Withdraw Threshold: %d days ', 'dokan-lite' ), $threshold );
}

$withdraw_limit =  dokan_get_option( 'withdraw_limit', 'dokan_withdraw', 0 );
$payment_methods = dokan_withdraw_get_active_methods();
$payment_methods_arr = array();
foreach ( $payment_methods as $method_name ) {
	$payment_methods_arr[esc_attr( $method_name )] = dokan_withdraw_get_method_title( $method_name ); 
}
?>
<div class="collapse wcfm-collapse" id="wcfm_withdrawal_listing">
  <div class="wcfm-page-headig">
		<span class="fa fa-currency"><?php echo get_woocommerce_currency_symbol(); ?></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Withdrawal Request', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php echo $message; ?></h2>
			
			<?php
			if( $wcfm_is_allow_payments = apply_filters( 'wcfm_is_allow_payments', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.wcfm_payments_url().'" data-tip="'. __('Transaction History', 'wc-frontend-manager') .'"><span class="fa fa-credit-card"></span><span class="text">' . __('Transactions', 'dc-woocommerce-multi-vendor' ) . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_withdrawal' ); ?>
		
		<?php
		if ( $status ) {
			$pending_warning = sprintf( "<p>%s</p><p>%s</p>", __( 'You already have pending withdraw request(s).', 'dokan-lite' ), __( 'Please submit your request after approval or cancellation of your previous request.', 'dokan-lite' ) );
		
			dokan_get_template_part( 'global/dokan-error', '', array(
					'deleted' => false,
					'message' => $pending_warning
			) );
		} elseif ( $balance < $withdraw_limit ) {
			dokan_get_template_part( 'global/dokan-error', '', array(
					'deleted' => false,
					'message' => __( 'You don\'t have sufficient balance for a withdraw request!', 'dokan-lite' )
			) );
		} else {
		?>
			<form metod="post" id="wcfm_withdrawal_manage_form">
				<div class="wcfm-container">
					<div id="wcfm_withdrawal_listing_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_withdrawal_fields_dokan', array( "withdraw_amount" => array('label' => __('Withdraw Amount', 'dokan-lite'), 'type' => 'number', 'attributes' => array( 'min' => $withdraw_limit ), 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'placeholder' => '0.00' ),
																																																								"withdraw_method" => array('label' => __('Payment Method', 'dokan-lite'), 'type' => 'select', 'options' => $payment_methods_arr, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title' ),
																																																							) ) );
						?>
						<div class="wcfm-clearfix"></div>
					</div>	
				</div>	
				<div class="wcfm-clearfix"></div>
				
				<div id="wcfm_products_simple_submit" class="wcfm_form_simple_submit_wrapper">
				  <div class="wcfm-message" tabindex="-1"></div>
				  
					<input type="submit" name="withdrawal-data" value="<?php _e( 'Request', 'wc-frontend-manager' ); ?>" id="wcfm_withdrawal_request_button" class="wcfm_submit_button" />
				</div>
				<div class="wcfm-clearfix"></div>
			</form>
		<?php } ?>
		
		<?php
		do_action( 'after_wcfm_withdrawal' );
		?>
	</div>
</div>