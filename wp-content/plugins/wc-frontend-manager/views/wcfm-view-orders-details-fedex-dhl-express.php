<?php
/**
 * WCFM plugin view
 *
 * WCFM Order Details FedEX / DHL Express View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   3.3.3
 */

$wcfm_is_allow_orders = apply_filters( 'wcfm_is_allow_orders', true );
if( !$wcfm_is_allow_orders ) {
	return;
}

global $WCFM, $woodhlwrapperadmin, $woofedexwrapperadmin;

if( WCFM_Dependencies::wcfm_wc_fedex_shipping_active_check() ) {
	if ( class_exists( 'wf_fedex_woocommerce_shipping_admin' ) ) {
		//include_once ( 'includes/class-wf-fedex-woocommerce-shipping-admin.php' );
	
		$woofedexwrapperadmin = new wf_fedex_woocommerce_shipping_admin();
		//$packages	=	$woodhlwrapperadmin->wf_dhl_generate_packages();
		
		add_action( 'end_wcfm_orders_details', 'wcfm_orders_details_fedex_express', 9 );
	}
}

function wcfm_orders_details_fedex_express() { 
	global $wp, $WCFM, $woofedexwrapperadmin;
	
	$order_id = 0;
	if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
		$order_id = $wp->query_vars['wcfm-orders-details'];
	}
	if( !$order_id ) return;
	
	//$theorder = wc_get_order( $order_id );
	
	$order = $woofedexwrapperadmin->wf_load_order( $order_id );
	if (!$order) 
		return;			
	
	?>
	
	<div class="wcfm-clearfix"></div><br />
	<!-- collapsible -->
	<div class="page_collapsible wc_dhl_shipping" id="wcfm_wc_fedex_shipping_options"><?php _e('Fedex', 'wc-frontend-manager'); ?><span></span></div>
	<div class="wcfm-container">
		<div id="wc_fedex_shipping_expander" class="wcfm-content">
			<?php
			$shipmentIds = get_post_meta( $order_id, 'wf_woo_fedex_shipmentId', false);
			$shipment_void_ids = get_post_meta($order_id, 'wf_woo_fedex_shipment_void', false);
			
			$shipmentErrorMessage = get_post_meta($order_id, 'wf_woo_fedex_shipmentErrorMessage',true);
			$shipment_void_error_message = get_post_meta($order_id, 'wf_woo_fedex_shipment_void_errormessage',true);
			
			//Only Display error message if the process is not complete. If the Invoice link available then Error Message is unnecessary
			if(!empty($shipmentErrorMessage))
			{
				echo '<div class="error"><p>' . sprintf( __( 'Fedex Create Shipment Error:%s', 'wf-shipping-fedex' ), $shipmentErrorMessage) . '</p></div>';
			}
	
			if(!empty($shipment_void_error_message)){
				echo '<div class="error"><p>' . sprintf( __( 'Void Shipment Error:%s', 'wf-shipping-fedex' ), $shipment_void_error_message) . '</p></div>';
			}			
			echo '<ul>';
			if (!empty($shipmentIds)) {
				foreach($shipmentIds as $shipmentId) {
					//$selected_sevice = $woofedexwrapperadmin->wf_get_shipping_service($order,true,$shipmentId);	
					//if(!empty($selected_sevice))
						//echo "<li>Shipping Service: <strong>$selected_sevice</strong></li>";		
					
					echo '<li><strong>Shipment #:</strong> '.$shipmentId;
					$usps_trackingid = get_post_meta($order_id, 'wf_woo_fedex_usps_trackingid_'.$shipmentId, true);
					if(!empty($usps_trackingid)){
						echo "<br><strong>USPS Tracking #:</strong> ".$usps_trackingid;
					}
					if((is_array($shipment_void_ids) && in_array($shipmentId,$shipment_void_ids))){
						echo "<br> This shipment $shipmentId is terminated.";
					}
					echo '<hr>';
					$packageDetailForTheshipment = get_post_meta($order_id, 'wf_woo_fedex_packageDetails_'.$shipmentId, true);
					if(!empty($packageDetailForTheshipment)){
						foreach($packageDetailForTheshipment as $dimentionKey => $dimentionValue){
							if($dimentionValue){
								echo '<strong>' . $dimentionKey . ': ' . '</strong>' . $dimentionValue ;
								echo '<br />';
							}						
						}
						echo '<hr>';
					}
					$shipping_label = get_post_meta($order_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
					if(!empty($shipping_label)){
						$download_url = add_query_arg( 'wf_fedex_viewlabel', base64_encode($shipmentId.'|'.$order_id), get_wcfm_view_order_url( $order_id ) );?>
						<a class="button wcfm_submit_button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Print Label', 'wf-shipping-fedex'); ?>"><?php _e('Print Label', 'wf-shipping-fedex'); ?></a>
						<?php 
					}				
					$additional_labels = get_post_meta($order_id, 'wf_fedex_additional_label_'.$shipmentId, true);
					if(!empty($additional_labels) && is_array($additional_labels)){
						foreach($additional_labels as $additional_key => $additional_label){
							$download_add_label_url = add_query_arg( 'wf_fedex_additional_label', base64_encode($shipmentId.'|'.$order_id.'|'.$additional_key), get_wcfm_view_order_url( $order_id ) );?>
							<a class="button wcfm_submit_button tips" href="<?php echo $download_add_label_url; ?>" data-tip="<?php _e('Additional Label', 'wf-shipping-fedex'); ?>"><?php _e('Additional Label', 'wf-shipping-fedex'); ?></a>
							<?php
						}		
					}
					if((!is_array($shipment_void_ids) || !in_array($shipmentId,$shipment_void_ids))){
						$void_shipment_link = add_query_arg( 'wf_fedex_void_shipment', base64_encode($shipmentId.'||'.$order_id), get_wcfm_view_order_url( $order_id ) );?>				
						<a class="button wcfm_submit_button tips" href="<?php echo $void_shipment_link; ?>" data-tip="<?php _e('Void Shipment', 'wf-shipping-fedex'); ?>"><?php _e('Void Shipment', 'wf-shipping-fedex'); ?></a>
						<?php 
					}
					$shipping_return_label = get_post_meta($order_id, 'wf_woo_fedex_returnLabel_'.$shipmentId, true);
					$return_shipment_id = get_post_meta($order_id, 'wf_woo_fedex_returnShipmetId', true);
					if(!empty($shipping_return_label)){
						$download_url = add_query_arg( 'wf_fedex_viewReturnlabel', base64_encode($shipmentId.'|'.$order_id), get_wcfm_view_order_url( $order_id ) );
						echo '<li><strong>Return Shipment #:</strong> '.$return_shipment_id.'</li>';?>
						<a class="button wcfm_submit_button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Print Return Label', 'wf-shipping-fedex'); ?>"><?php _e('Print Return Label', 'wf-shipping-fedex'); ?></a>
						<?php 
					}
				} ?>		
				<?php 
				if(count($shipmentIds) == count($shipment_void_ids)){
					$clear_history_link = add_query_arg( 'wf_clear_history', base64_encode($order_id), get_wcfm_view_order_url( $order_id ) );?>				
						<a class="button wcfm_submit_button button-primary tips" href="<?php echo $clear_history_link; ?>" data-tip="<?php _e('Clear History', 'wf-shipping-fedex'); ?>"><?php _e('Clear History', 'wf-shipping-fedex'); ?></a>
						<?php 
				}					
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	</div>
	
	<?php
}

if( WCFM_Dependencies::wcfm_wc_dhl_shipping_active_check() ) {
	if ( ! class_exists( 'wcfm_dhl_woocommerce_shipping_admin' ) ) {
		include_once $WCFM->plugin_path . 'includes/libs/dhl-express/class-wcfm-dhl-woocommerce-shipping-admin.php';
	
		$woodhlwrapperadmin = new wcfm_dhl_woocommerce_shipping_admin();
		//$packages	=	$woodhlwrapperadmin->wf_dhl_generate_packages();
		
		add_action( 'end_wcfm_orders_details', 'wcfm_orders_details_dhl_express', 9 );
	}
}

function wcfm_orders_details_dhl_express() { 
	global $wp, $WCFM, $woodhlwrapperadmin;
	
	$order_id = 0;
	if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
		$order_id = $wp->query_vars['wcfm-orders-details'];
	}
	if( !$order_id ) return;
	
	$theorder = wc_get_order( $order_id );
	
	?>
	
	<div class="wcfm-clearfix"></div><br />
	<!-- collapsible -->
	<div class="page_collapsible wc_dhl_shipping" id="wcfm_wc_dhl_shipping_options"><?php _e('DHL Express', 'wc-frontend-manager'); ?><span></span></div>
	<div class="wcfm-container">
		<div id="wc_dhl_shipping_expander" class="wcfm-content">
			<?php
			if( $woodhlwrapperadmin )
				$woodhlwrapperadmin->wf_dhl_metabox_content( $order_id );
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	</div>
	
	<?php
}