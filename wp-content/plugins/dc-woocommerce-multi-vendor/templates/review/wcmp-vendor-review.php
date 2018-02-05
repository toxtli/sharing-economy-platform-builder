<?php
/**
 * Vendor Review Comments Lists Template

 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/review/wcmp-vendor-review.php.
 *
 * 
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 3.3.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
global $WCMp;
if(isset($reviews_lists) && count($reviews_lists) > 0) {
	foreach($reviews_lists as $reviews_list) {
		
		$WCMp->template->get_template( 'review/review.php', array('comment' => $reviews_list, 'vendor_term_id'=> $vendor_term_id));
	}	
}?>
