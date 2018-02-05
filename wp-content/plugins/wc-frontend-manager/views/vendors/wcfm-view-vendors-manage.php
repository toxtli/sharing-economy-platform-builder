<?php
/**
 * WCFM plugin views
 *
 * Plugin Vendor Details Views
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/vendors
 * @version   3.4.1
 */
global $wp, $WCFM, $WCFMu;

$is_allow_vendors = apply_filters( 'wcfm_is_allow_vendors', true );
if( !$is_allow_vendors ) {
	wcfm_restriction_message_show( "Vendors" );
	return;
}

$vendor_id = 0;
$user_name = '&ndash;';
$user_email = '&ndash;';
$first_name = '&ndash;';
$last_name = '&ndash;';
$vendor_store = '&ndash;';
$vendor_paypal = '&ndash;';
$seller_info = '';

$logo = ( get_option( 'wcfm_site_logo' ) ) ? get_option( 'wcfm_site_logo' ) : '';
$logo_image_url = wp_get_attachment_image_src( $logo, 'thumbnail' );

if ( !empty( $logo_image_url ) ) {
	$logo_image_url = $logo_image_url[0];
} else {
	$logo_image_url = $WCFM->plugin_url . 'assets/images/your-logo-here.png';
}

$store_logo = $logo_image_url;

$has_custom_capability = 'no';

if( isset( $wp->query_vars['wcfm-vendors-manage'] ) && !empty( $wp->query_vars['wcfm-vendors-manage'] ) ) {
	$vendor_id = $wp->query_vars['wcfm-vendors-manage'];
	
	if( $vendor_id ) {
		$vendor_store = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $vendor_id );
		$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$vendor_user = get_userdata( $vendor_id );
			$user_name = $vendor_user->user_login;
			$user_email = $vendor_user->user_email;
			$first_name = $vendor_user->first_name;
			$last_name = $vendor_user->last_name;
			$vendor_paypal = get_user_meta( $vendor_id, 'pv_paypal', true );
			$seller_info = get_user_meta( $vendor_id, 'pv_seller_info', true );
		
			$logo = get_user_meta( $vendor_id, '_wcv_store_icon_id', true );
			$logo_image_url = wp_get_attachment_image_src( $logo, 'thumbnail' );
			if ( !empty( $logo_image_url ) ) {
				$store_logo = $logo_image_url[0];
			}
		} elseif( $marketplece == 'wcmarketplace' ) {
			$vendor_user = get_userdata( $vendor_id );
			$user_name = $vendor_user->user_login;
			$user_email = $vendor_user->user_email;
			$first_name = $vendor_user->first_name;
			$last_name = $vendor_user->last_name;
			$vendor_paypal = get_user_meta( $vendor_id, '_vendor_paypal_email', true );
			$seller_info = get_user_meta( $vendor_id, '_vendor_description', true );
		
			$logo_image_url = get_user_meta( $vendor_id, '_vendor_image', true );
			if ( !empty( $logo_image_url ) ) {
				$store_logo = $logo_image_url;
			}
		} elseif( $marketplece == 'wcpvendors' ) {
			$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );
			
			if( is_array( $vendor_data['admins'] ) ) {
				$admin_ids = array_map( 'absint', $vendor_data['admins'] );
			} else {
				$admin_ids = array_filter( array_map( 'absint', explode( ',', $vendor_data['admins'] ) ) );
			}
			foreach( $admin_ids as $admin_id ) {
				if( $admin_id ) {
					if ( WC_Product_Vendors_Utils::is_admin_vendor( $admin_id ) ) {
						$vendor_user = get_userdata( $admin_id );
						$user_name = $vendor_user->user_login;
						$first_name = $vendor_user->first_name;
						$last_name = $vendor_user->last_name;
						break;
					}
				}
			}
			
			$user_email            = ! empty( $vendor_data['email'] ) ? $vendor_data['email'] : '';
			$vendor_paypal         = ! empty( $vendor_data['paypal'] ) ? $vendor_data['paypal'] : '';
			$seller_info           = ! empty( $vendor_data['profile'] ) ? $vendor_data['profile'] : '';
			
			$logo = ! empty( $vendor_data['logo'] ) ? $vendor_data['logo'] : '';
			$logo_image_url = wp_get_attachment_image_src( $logo, 'full' );
			if ( !empty( $logo_image_url ) ) {
				$store_logo = $logo_image_url[0];
			}
		} elseif( $marketplece == 'dokan' ) {
  		$vendor_user = get_userdata( $vendor_id );
  		$vendor_data = get_user_meta( $vendor_id, 'dokan_profile_settings', true );
			$user_name = $vendor_user->user_login;
			$user_email = isset( $vendor_data['show_email'] ) ? esc_attr( $vendor_data['show_email'] ) : 'no';
			$first_name = $vendor_user->first_name;
			$last_name = $vendor_user->last_name;
			
			
			$vendor_paypal = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
		
			$logo = isset( $vendor_data['gravatar'] ) ? absint( $vendor_data['gravatar'] ) : 0;
			$logo_image_url = $logo ? wp_get_attachment_url( $logo ) : '';
			if ( !empty( $logo_image_url ) ) {
				$store_logo = $logo_image_url[0];
			}
		}
		
		if( !$first_name ) $first_name = '&ndash;';
		if( !$last_name ) $last_name = '&ndash;';
		if( !$vendor_paypal ) $vendor_paypal = '&ndash;';
		
		//$has_custom_capability = get_user_meta( $vendor_id, '_wcfm_user_has_custom_capability', true ) ? get_user_meta( $vendor_id, '_wcfm_user_has_custom_capability', true ) : 'no';

	}
}

if( !$vendor_id ) {
	wcfm_restriction_message_show( "No Vendor" );
	return;
}

$vendor_capability_options = (array) apply_filters( 'wcfmgs_user_capability', get_option( 'wcfm_capability_options' ), $vendor_id );

$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="fa fa-user-o"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Vendor', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
	    <img class="vendor_store_logo" src="<?php echo $store_logo; ?>" alt="Store Logo" />
	    <h2>
	      <?php 
	      	echo strip_tags( $vendor_store );
	      	if( $first_name ) echo "&nbsp;&ndash;&nbsp;" . $first_name;
	      	if( $last_name ) echo "&nbsp;" . $last_name;
	      ?>
	    </h2>
	    
	    <label class="wcfm_vendor_manage_change_vendor">
				<?php
				if( $wcfm_is_products_vendor_filter = apply_filters( 'wcfm_is_products_vendor_filter', true ) ) {
					$is_marketplace = wcfm_is_marketplace();
					if( $is_marketplace ) {
						if( !wcfm_is_vendor() ) {
							$vendor_arr = $WCFM->wcfm_vendor_support->wcfm_get_vendor_list( true );
							if( isset( $vendor_arr[0] ) ) unset($vendor_arr[0]);
							$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																												"dropdown_vendor" => array( 'type' => 'select', 'options' => $vendor_arr, 'attributes' => array( 'style' => 'width: 250px;' ), 'value' => $vendor_id )
																												 ) );
						}
					}
				}
				?>
			</label>
			
			<?php
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_vendors_url().'" data-tip="' . __( 'Vendors', 'wc-frontend-manager' ) . '"><span class="fa fa-user-o"></span></a>';
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<?php do_action( 'begin_wcfm_vendors_manage' ); ?>
		
		<?php if( apply_filters( 'wcfm_is_pref_stats_box', true ) ) { ?>
			<div class="wcfm_dashboard_stats">
				<div class="wcfm_dashboard_stats_block">
				  <a href="#" onclick="return false;">
						<span class="fa fa-currency"><?php echo get_woocommerce_currency_symbol() ; ?></span>
						<div>
							<strong>
								<?php
								$gross_sales = $WCFM->wcfm_vendor_support->wcfm_get_gross_sales_by_vendor( $vendor_id, 'month' );
								echo apply_filters( 'wcfm_vednors_gross_sales_data', wc_price( $gross_sales ), $vendor_id );
								?>
							</strong><br />
							<?php _e( 'gross sales in this month', 'wc-frontend-manager' ); ?>
						</div>
					</a>
				</div>
				
				<div class="wcfm_dashboard_stats_block">
				  <a href="#" onclick="return false;">
						<span class="fa fa-money"></span>
						<div>
							<strong>
								<?php 
								$earned = $WCFM->wcfm_vendor_support->wcfm_get_commission_by_vendor( $vendor_id, 'month' );
								if( $admin_fee_mode ) {
									$earned = $gross_sales - $earned;
								}
								echo apply_filters( 'wcfm_vednors_earned_commission_data', wc_price( $earned ), $vendor_id, 'month' );
								?>
							</strong><br />
							<?php if( $admin_fee_mode ) { _e( 'admin fees in this month', 'wc-frontend-manager' ); } else { _e( 'earnings in this month', 'wc-frontend-manager' ); } ?>
						</div>
					</a>
				</div>
				
				<div class="wcfm_dashboard_stats_block">
					<a href="#" onclick="return false;">
						<span class="fa fa-cubes"></span>
						<div>
							<?php 
							$total_products = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor( $vendor_id );
							$total_products = apply_filters( 'wcfm_vednors_total_products_data', count( $total_products ), $vendor_id );
							printf( _n( "<strong>%s product</strong><br />", "<strong>%s products</strong><br />", $total_products, 'wc-frontend-manager' ), $total_products ); 
							?>
							<?php _e( 'total products posted', 'wc-frontend-manager' ); ?>
						</div>
					</a>
				</div>
				
				<div class="wcfm_dashboard_stats_block">
				  <a href="#" onclick="return false;">
						<span class="fa fa-cart-plus"></span>
						<div>
							<?php 
							$total_item_sales = $WCFM->wcfm_vendor_support->wcfm_get_total_sell_by_vendor( $vendor_id, 'month' );
							$total_item_sales = apply_filters( 'wcfm_vednors_total_item_sales_data', $total_item_sales, $vendor_id, 'month' );
							printf( _n( "<strong>%s item</strong><br />", "<strong>%s items</strong><br />", $total_item_sales, 'wc-frontend-manager' ), $total_item_sales ); 
							?>
							<?php _e( 'sold in this month', 'wc-frontend-manager' ); ?>
						</div>
					</a>
				</div>
			</div>
			<div class="wcfm-clearfix"></div>
		<?php } ?>
			
		<form id="wcfm_vendors_manage_form" class="wcfm">
			
		  <?php do_action( 'begin_wcfm_vendors_manage_form' ); ?>
			
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="vendors_manage_general_expander" class="wcfm-content">
						<p class="store_name wcfm_ele wcfm_title"><strong><?php _e( 'Store', 'wc-frontend-manager' ); ?></strong></p>
						<span class="wcfm_vendor_store"><?php echo $vendor_store ?></span>
						<div class="wcfm_clearfix"></div>
						<?php
						  if( $vendor_id ) {
						  	$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "user_name" => array( 'label' => __('Store Admin', 'wc-frontend-manager') , 'type' => 'text', 'attributes' => array( 'readonly' => true ), 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $user_name ) ) );
						  } else {
						  	$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "user_name" => array( 'label' => __('Store Admin', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $user_name ) ) );
						  }
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendor_vendor_fields_general', array(  
																																						"user_email" => array( 'label' => __('Email', 'wc-frontend-manager') , 'type' => 'text', 'attributes' => array( 'readonly' => true ), 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $user_email),
																																						"first_name" => array( 'label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'attributes' => array( 'readonly' => true ), 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $first_name),
																																						"last_name" => array( 'label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'attributes' => array( 'readonly' => true ), 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $last_name),
																																						"paypal_email" => array( 'label' => __('PayPal Email', 'wc-frontend-manager') , 'type' => 'text', 'attributes' => array( 'readonly' => true ), 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $vendor_paypal),
																																						"vendor_id" => array('type' => 'hidden', 'value' => $vendor_id )
																																				 ) ) );
							
							if( $seller_info ) {
							?>
								<p class="store_name wcfm_ele wcfm_title"><strong><?php _e( 'Seller Info', 'wc-frontend-manager' ); ?></strong></p>
								<span class="wcfm_vendor_store_info"><?php echo $seller_info ?></span>
								<div class="wcfm_clearfix"></div>
							<?php
							}
							if( WCFM_Dependencies::wcfmgs_plugin_active_check() ) {
								//$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																																	//	"has_custom_capability" => array( 'label' => __('Custom Capability', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $has_custom_capability),
																																//	) );
							}
						?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			
			<?php if( WCFM_Dependencies::wcfmgs_plugin_active_check() ) { ?>
				<div class="user_custom_capability" style="display: none;">
					<?php do_action( 'wcfmgs_capability_vendor', $vendor_capability_options ); ?>
				</div>
			<?php } ?>
			
			<!-- collapsible -->
			<div class="page_collapsible vendor_manage_membership" id="wcfm_vendor_manage_form_membership_head"><label class="fa fa-user-plus"></label><?php _e( 'Membership', 'wc-frontend-manager' ); ?><span></span></div>
			<div class="wcfm-container">
				<div id="wcfm_vendor_manage_form_membership_expander" class="wcfm-content">
				  <?php 
				  if( WCFM_Dependencies::wcfmvm_plugin_active_check() ) {
				  	do_action( 'wcfm_vendor_manage_membrship_details', $vendor_id );
				  } else {
						echo "<h2>";
						_e( 'Vendor not yet subscribed for a membership!', 'wc-frontend-manager' );
						echo "</h2><div class=\"wcfm_clearfix\"></div><br />";
					}
					?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			
			<!-- collapsible -->
				<?php if( apply_filters( 'wcfm_is_allow_direct_message', true ) ) { ?>
				<div class="page_collapsible vendor_manage_message" id="wcfm_vendor_manage_form_message_head"><label class="fa fa-send-o"></label><?php _e( 'Send Message', 'wc-frontend-manager' ); ?><span></span></div>
				<div class="wcfm-container">
					<div id="wcfm_vendor_manage_form_message_expander" class="wcfm-content">
						<?php
						if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_messages_field_vendor_manage', array(
																																																			"wcfm_messages" => array( 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title' ),
																																																			"direct_to" => array( 'type' => 'hidden', 'value' => $vendor_id ),
																																																			) ) );
							?>
							<div id="wcfm_messages_submit">
								<input type="submit" name="save-data" value="<?php _e( 'Send', 'wc-frontend-manager' ); ?>" id="wcfm_messages_save_button" class="wcfm_submit_button" />
							</div>
							<div class="wcfm-clearfix"></div>
							<div class="wcfm-message" tabindex="-1"></div>
							<div class="wcfm-clearfix"></div>
						<?php 
						 } else {
						 	 if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						 	 	 wcfmu_feature_help_text_show( __( 'Direct Message', 'wc-frontend-manager' ), false, true );
						 	 }
						 }
						 ?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div><br />
			<?php } ?>
			<!-- end collapsible -->
			 
			<?php do_action( 'end_wcfm_vendors_manage_form', $vendor_id ); ?>
			
			<div id="wcfm_vendor_vendor_submit" class="wcfm_form_simple_submit_wrapper" style="display: none;">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
				<input type="submit" name="submit-data" value="<?php _e( 'Submit', 'wc-frontend-manager' ); ?>" id="wcfm_vendor_vendor_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_vendors_manage' );
			?>
		</form>
	</div>
</div>