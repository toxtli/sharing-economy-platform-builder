<?php
/**
 * WCFM plugin view
 *
 * WCFM WCMp Withdrawal List View
 *
 * @author 		WC Lovers
 * @package 	wcfm/wcmp/view
 * @version   2.5.2
 */
 
global $WCFM, $woocommerce, $WCMp;

$wcfm_is_allow_withdrawal = apply_filters( 'wcfm_is_allow_withdrawal', true );
if( !$wcfm_is_allow_withdrawal ) {
	wcfm_restriction_message_show( "Withdrawal" );
	return;
}

$get_vendor_thresold = 0;
if (isset($WCMp->vendor_caps->payment_cap['commission_threshold']) && $WCMp->vendor_caps->payment_cap['commission_threshold']) {
  $get_vendor_thresold = $WCMp->vendor_caps->payment_cap['commission_threshold'];
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
			<h2>
				<?php _e( 'Threshold for withdrawals: ', 'wc-frontend-manager' ); ?> 
				<span class=""><?php echo wc_price($get_vendor_thresold); ?></span>
			</h2>
			
			<?php
			if( $wcfm_is_allow_payments = apply_filters( 'wcfm_is_allow_payments', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.wcfm_payments_url().'" data-tip="'. __('Transaction History', 'wc-frontend-manager') .'"><span class="fa fa-credit-card"></span><span class="text">' . __('Transactions', 'dc-woocommerce-multi-vendor' ) . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_withdrawal' ); ?>
		
		<form metod="post" id="wcfm_withdrawal_manage_form">
		  <div class="wcfm-container">
				<div id="wcfm_withdrawal_listing_expander" class="wcfm-content">
					<table id="wcfm-withdrawal" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Send Request', 'wc-frontend-manager' ); ?>"></span></th>
								<th><?php _e( 'Order ID', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Commission ID', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'My Earnings', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Send Request', 'wc-frontend-manager' ); ?>"></span></th>
								<th><?php _e( 'Order ID', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Commission ID', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'My Earnings', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							</tr>
						</tfoot>
					</table>
					<div class="wcfm-clearfix"></div>
				</div>	
			</div>	
			<div class="wcfm-clearfix"></div>
			
			<div id="wcfm_products_simple_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
			  <?php
				if (isset($WCMp->vendor_caps->payment_cap['wcmp_disbursal_mode_vendor']) && $WCMp->vendor_caps->payment_cap['wcmp_disbursal_mode_vendor'] == 'Enable') {
					$vendor = get_wcmp_vendor( apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) );
					$total_vendor_due = $vendor->wcmp_vendor_get_total_amount_due();
					if ($total_vendor_due > $get_vendor_thresold) {
					?>
					  <input type="submit" name="withdrawal-data" value="<?php _e( 'Request', 'wc-frontend-manager' ); ?>" id="wcfm_withdrawal_request_button" class="wcfm_submit_button" />
				<?php }
				}
				?>
			</div>
			<div class="wcfm-clearfix"></div>
		</form>
		<?php
		do_action( 'after_wcfm_withdrawal' );
		?>
	</div>
</div>