<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class 		WCMp_Coupon
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */ 
class WCMp_Coupon {
	
	public function __construct() {
		
		/* Coupon Management */
		add_filter( 'woocommerce_coupon_discount_types', array( &$this, 'coupon_discount_types' ) );
		add_filter( 'woocommerce_json_search_found_products', array( &$this, 'json_filter_report_products' ) );
		
		/* Filter coupon list */
		add_action( 'request', array( &$this, 'filter_coupon_list' ) );
		add_filter( 'wp_count_posts', array( &$this, 'vendor_count_coupons' ), 10, 3 );

		// Validate vendor coupon in cart and checkout
		add_filter( 'woocommerce_coupon_is_valid', array(&$this, 'woocommerce_coupon_is_valid' ), 30, 2);
		add_filter( 'woocommerce_coupon_is_valid_for_product', array(&$this, 'woocommerce_coupon_is_valid_for_product' ), 30, 4);
	}
	
	/**
	* validate vendor coupon
	*
	* @param boolean $true
	* @return abject $coupon
	*/
	public function woocommerce_coupon_is_valid_for_product( $valid, $product, $coupon, $values) {
	  if ( $coupon->is_type( array( 'fixed_product', 'percent_product' ) ) ) {
	    $current_coupon = get_post( $coupon->get_id() );
	    if(is_user_wcmp_vendor($current_coupon->post_author)) {
	      $current_product = get_post($product->get_id());
	      if($current_product->post_author != $current_coupon->post_author) $valid = false;
	    }
	  }
	  return $valid;
	}
	
	/**
	* validate vendor coupon
	*
	* @param boolean $true
	* @return abject $coupon
	*/
	public function woocommerce_coupon_is_valid($true, $coupon) {
		$current_coupon = get_post( $coupon->get_id() );
		if(is_user_wcmp_vendor($current_coupon->post_author)) {
			if ($coupon->is_type( array( 'fixed_product', 'percent_product' ) ) ) {
				$is_coupon_valid = false;
				if ( ! WC()->cart->is_empty() ) {
					foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						if(isset($cart_item['product_id'])) {
							$vendor_product = get_wcmp_product_vendors($cart_item['product_id']);
							if($vendor_product) {
								if( $vendor_product->id ==  $current_coupon->post_author) {
									$is_coupon_valid = true;
								}
							}
						}
					}
					if(!$is_coupon_valid) $true = false;
				}
			}
		}
		return $true;
	}
	
	/**
	 * Filter coupon discount types as per vendor
	 *
	 * @param array $coupon_types
	 * @return array $coupon_types
	 */
	public function coupon_discount_types( $coupon_types ){
		$current_user = wp_get_current_user();
		if( is_user_wcmp_vendor($current_user) ){
			$to_unset = apply_filters( 'wcmp_multi_vendor_coupon_types', array( 'fixed_cart', 'percent' ) );
			foreach( $to_unset as $coupon_type_id ){
				unset( $coupon_types[ $coupon_type_id ] );
			}
		}
		return $coupon_types;
	}

        
	public function filter_coupon_list( $request ) {
		global $typenow;

		$current_user = get_current_vendor_id();

		if ( is_admin() && is_user_wcmp_vendor($current_user) && 'shop_coupon' == $typenow ) {
				$request[ 'author' ] = $current_user;
		}

		return $request;
	}

	
  /**
   * Get vendor coupon count
   */
	public function vendor_count_coupons( $counts, $type, $perm ) {
		$current_user = get_current_vendor_id();

		if ( is_user_wcmp_vendor($current_user) && 'shop_coupon' == $type ) {
				$args = array(
						'post_type'     => $type,
						'author'    => $current_user
				);

				/**
				 * Get a list of post statuses.
				 */
				$stati = get_post_stati();

				// Update count object
				foreach ( $stati as $status ) {
						$args['post_status'] = $status;
						$posts               = get_posts( $args );
						$counts->$status     = count( $posts );
				}
		}

		return $counts;
	}
	
	
	/**
	 * Filter product search with vendor specific
	 *
	 * @access public
	 * @return void
	*/	
	function json_filter_report_products($products) {
		$current_userid = get_current_vendor_id();
		
		$filtered_product = array();

		if ( is_user_wcmp_vendor($current_userid) ) {
			$vendor = get_wcmp_vendor($current_userid);
				$vendor_products = $vendor->get_products();
				if(!empty($vendor_products)) {
					foreach( $vendor_products as $vendor_product ) {
						if( isset( $products[ $vendor_product->ID ] ) ){
								$filtered_product[ $vendor_product->ID ] = $products[ $vendor_product->ID ];
						}
					}
				}
				$products = $filtered_product;
		}
		
		return $products;
	}
	
}
?>
