<?php
/**
 * The template for displaying single product page vendor tab 
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/policies_tab.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   2.3.0
 */
global $product, $WCMp, $post;
$wcmp_policy_settings = get_option("wcmp_general_policies_settings_name");


$cancellation_policy_product = '';
$cancellation_policy_user = '';
$refund_policy_product = '';
$refund_policy_user = '';
$shipping_policy_product = '';
$shipping_policy_user = '';
$cancellation_policy = isset($wcmp_policy_settings['cancellation_policy']) ? $wcmp_policy_settings['cancellation_policy'] : '';
$refund_policy = isset($wcmp_policy_settings['refund_policy']) ? $wcmp_policy_settings['refund_policy'] : '';
$shipping_policy = isset($wcmp_policy_settings['shipping_policy']) ? $wcmp_policy_settings['shipping_policy'] : '';
$cancellation_policy_label = isset($wcmp_policy_settings['cancellation_policy_label']) ? $wcmp_policy_settings['cancellation_policy_label'] :  __('Cancellation/Return/Exchange Policy','dc-woocommerce-multi-vendor');
$refund_policy_label = isset($wcmp_policy_settings['refund_policy_label']) ? $wcmp_policy_settings['refund_policy_label'] :  __('Refund Policy','dc-woocommerce-multi-vendor');
$shipping_policy_label = isset($wcmp_policy_settings['shipping_policy_label']) ? $wcmp_policy_settings['shipping_policy_label'] :  __('Shipping Policy','dc-woocommerce-multi-vendor');
$product_id = $product->get_id();

$author_id = $post->post_author;


if(isset($wcmp_policy_settings['can_vendor_edit_cancellation_policy'])){
	if(isset($wcmp_policy_settings['is_cancellation_product_level_on'])){		
		$cancellation_policy_product = get_post_meta($product_id, '_wcmp_cancallation_policy', true);		
	}	
	$cancellation_policy_user = get_user_meta($author_id, '_vendor_cancellation_policy', true);
	
}
else {
	if(isset($wcmp_policy_settings['is_cancellation_product_level_on'])){
		$cancellation_policy_product = get_post_meta($product_id, '_wcmp_cancallation_policy', true);		
	}	
}
if(isset($wcmp_policy_settings['can_vendor_edit_refund_policy'])){
	if(isset($wcmp_policy_settings['is_refund_product_level_on'])){
		$refund_policy_product = get_post_meta($product_id, '_wcmp_refund_policy', true);		
	}	
	$refund_policy_user = get_user_meta($author_id, '_vendor_refund_policy', true);
	
}
else {
	if(isset($wcmp_policy_settings['is_refund_product_level_on'])){
		$refund_policy_product = get_post_meta($product_id, '_wcmp_refund_policy', true);		
	}	
}

if(isset($wcmp_policy_settings['can_vendor_edit_shipping_policy'])){
	if(isset($wcmp_policy_settings['is_shipping_product_level_on'])){
		$shipping_policy_product = get_post_meta($product_id, '_wcmp_shipping_policy', true);		
	}
	
	$shipping_policy_user = get_user_meta($author_id, '_vendor_shipping_policy', true);
	
}
else {
	if(isset($wcmp_policy_settings['is_shipping_product_level_on'])){
		$shipping_policy_product = get_post_meta($product_id, '_wcmp_shipping_policy', true);		
	}	
}
if(!empty($cancellation_policy_product)) {
	$cancellation_policy = $cancellation_policy_product;
}
else if(!empty($cancellation_policy_user)) {
	$cancellation_policy = $cancellation_policy_user;
}

if(!empty($refund_policy_product)) {
	$refund_policy = $refund_policy_product;
}
else if(!empty($refund_policy_user)) {
	$refund_policy = $refund_policy_user;
}

if(!empty($shipping_policy_product)) {
	$shipping_policy = $shipping_policy_product;
}
else if(!empty($shipping_policy_user)) {
	$shipping_policy = $shipping_policy_user;
}
?>
<div class="wcmp-product-policies">
<?php if(!empty($cancellation_policy) && !empty($cancellation_policy_label) && isset($wcmp_policy_settings['is_cancellation_on']) ) { ?>
	<h2 class="wcmp_policies_heading"><?php echo $cancellation_policy_label; ?></h2>
	<div class="wcmp_policies_description" ><?php echo $cancellation_policy; ?></div>
<?php }?>
<?php if(!empty($refund_policy) && !empty($refund_policy_label) && isset($wcmp_policy_settings['is_refund_on']) ) { ?>
	<h2 class="wcmp_policies_heading"><?php echo $refund_policy_label; ?></h2>
	<div class="wcmp_policies_description" ><?php echo $refund_policy; ?></div>
<?php }?>
<?php if(!empty($shipping_policy) && !empty($shipping_policy_label) && isset($wcmp_policy_settings['is_shipping_on']) ) { ?>
	<h2 class="wcmp_policies_heading"><?php echo $shipping_policy_label; ?></h2>
	<div class="wcmp_policies_description" ><?php echo $shipping_policy; ?></div>
<?php }?>
</div>