<?php
global $wp, $WCFM;                         

$wcfm_is_allow_manage_coupons = apply_filters( 'wcfm_is_allow_manage_coupons', true );
if( !current_user_can( 'edit_shop_coupons' ) || !$wcfm_is_allow_manage_coupons ) {
	wcfm_restriction_message_show( "Coupons" );
	return;
}

$coupon_id = 0;
$title = '';
$description = '';
$discount_type = '';
$coupon_amount = 0;
$free_shipping = '';
$expiry_date = '';

if( isset( $wp->query_vars['wcfm-coupons-manage'] ) && !empty( $wp->query_vars['wcfm-coupons-manage'] ) ) {
	$coupon_post = get_post( $wp->query_vars['wcfm-coupons-manage'] );
	// Fetching Coupon Data
	if($coupon_post && !empty($coupon_post)) {
		$coupon_id = $wp->query_vars['wcfm-coupons-manage'];
		$wcfm_coupons_single = get_post( $coupon_id );
		$wc_coupon = new WC_Coupon( $coupon_id );
		
		$title = $coupon_post->post_title;
		$description = $coupon_post->post_excerpt;
		$discount_type = get_post_meta( $coupon_id, 'discount_type', true);
		$coupon_amount = get_post_meta( $coupon_id, 'coupon_amount', true);
		$free_shipping = ( get_post_meta( $coupon_id, 'free_shipping', true) == 'yes' ) ? 'enable' : '';
		$expiry_date   = $wc_coupon->get_date_expires() ? $wc_coupon->get_date_expires()->date( 'Y-m-d' ) : '';
	}
}

do_action( 'before_wcfm_coupons_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="fa fa-gift"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Coupon', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<h2><?php if( $coupon_id ) { _e('Edit Coupon', 'wc-frontend-manager' ); } else { _e('Add Coupon', 'wc-frontend-manager' ); } ?></h2>
			<?php
			if( $coupon_id ) {
				?>
				<span class="coupon-types coupon-types-<?php echo get_post_meta( $wcfm_coupons_single->ID, 'discount_type', true ); ?>"><?php echo esc_html( wc_get_coupon_type( get_post_meta( $wcfm_coupons_single->ID, 'discount_type', true ) ) ); ?></span>
				<?php
			}
			
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('post-new.php?post_type=shop_coupon'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_coupon_sub_menu', true ) ) {
				echo '<a id="add_new_coupon_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_coupons_manage_url().'" data-tip="' . __('Add New Coupon', 'wc-frontend-manager') . '"><span class="fa fa-gift"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
	  </div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<form id="wcfm_coupons_manage_form" class="wcfm">
		
		  <?php do_action( 'begin_wcfm_coupons_manage_form' ); ?>
	
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="coupons_manage_general_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'coupon_manager_fields_general', array(  "title" => array('label' => __('Code', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $title),
																																															"description" => array('label' => __('Description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $description),
																																															"discount_type" => array('label' => __('Discount Type', 'wc-frontend-manager'), 'type' => 'select', 'options' => apply_filters( 'wcfm_coupon_types', array('percent' => __('Percentage discount', 'wc-frontend-manager'), 'fixed_cart' => __('Fixed Cart Discount', 'wc-frontend-manager'), 'fixed_product' => __('Fixed Product Discount', 'wc-frontend-manager') ) ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $discount_type),
																																															"coupon_amount" => array('label' => __('Coupon Amount', 'wc-frontend-manager'), 'type' => 'text', 'placeholder' => wc_format_localized_price( 0 ), 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $coupon_amount),
																																															"free_shipping" => array('label' => __('Allow free shipping', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'enable', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __('Check this box if the coupon grants free shipping. The free shipping method must be enabled and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'wc-frontend-manager'), 'dfvalue' => $free_shipping),
																																															"expiry_date" => array('label' => __('Coupon expiry date', 'wc-frontend-manager'), 'type' => 'text', 'placeholder' => 'YYYY-MM-DD', 'class' => 'wcfm-text wcfm_ele wcfm_datepicker', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $expiry_date),
																																															"coupon_id" => array('type' => 'hidden', 'value' => $coupon_id)
																																					) ) );
						?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			 
			<?php do_action( 'end_wcfm_coupons_manage_form' ); ?>
			
			<div id="wcfm_coupon_manager_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
				<input type="submit" name="submit-data" value="<?php if( current_user_can( 'publish_shop_coupons' ) ) { _e( 'Submit', 'wc-frontend-manager' ); } else { _e( 'Submit for Review', 'wc-frontend-manager' ); } ?>" id="wcfm_coupon_manager_submit_button" class="wcfm_submit_button" />
				<?php if( ( isset( $wp->query_vars['wcfm-coupons-manage'] ) && empty( $wp->query_vars['wcfm-coupons-manage'] ) ) || ( isset( $wp->query_vars['wcfm-coupons-manage'] ) && !empty( $wp->query_vars['wcfm-coupons-manage'] ) && ( get_post_status( $wp->query_vars['wcfm-coupons-manage'] ) == 'draft' ) ) ) { ?>
				<input type="submit" name="draft-data" value="<?php _e( 'Draft', 'wc-frontend-manager' ); ?>" id="wcfm_coupon_manager_draft_button" class="wcfm_submit_button" />
				<?php } ?>
			</div>
			<?php
			do_action( 'after_wcfm_coupons_manage' );
			?>
		</form>
	</div>
</div>