<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Coupons Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Coupons_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'shop_coupon',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		$args = apply_filters( 'wcfm_coupons_args', $args );
		
		$wcfm_coupons_array = get_posts( $args );
		
		// Get Coupon Count
		$coupon_count = 0;
		$filtered_coupon_count = 0;
		$wcfm_coupons_count = wp_count_posts('shop_coupon');
		$coupon_count = ( $wcfm_coupons_count->publish + $wcfm_coupons_count->pending + $wcfm_coupons_count->draft );
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_coupons_array = get_posts( $args );
		$filtered_coupon_count = count($wcfm_filterd_coupons_array);
		
		
		// Generate Coupons JSON
		$wcfm_coupons_json = '';
		$wcfm_coupons_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $coupon_count . ',
															"recordsFiltered": ' . $filtered_coupon_count . ',
															"data": ';
		if(!empty($wcfm_coupons_array)) {
			$index = 0;
			$wcfm_coupons_json_arr = array();
			foreach($wcfm_coupons_array as $wcfm_coupons_single) {
				$wc_coupon = new WC_Coupon( $wcfm_coupons_single->ID );
				// Code
				if( $wcfm_coupons_single->post_status != 'publish' ) {
					$wcfm_coupons_json_arr[$index][] =  '<a href="' . get_wcfm_coupons_manage_url($wcfm_coupons_single->ID) . '" class="wcfm_dashboard_item_title">' . $wcfm_coupons_single->post_title . '</a>' . ' -- ' . ucfirst( $wcfm_coupons_single->post_status );
				} elseif( current_user_can( 'edit_published_shop_coupons' ) ) {
					$wcfm_coupons_json_arr[$index][] =  '<a href="' . get_wcfm_coupons_manage_url($wcfm_coupons_single->ID) . '" class="wcfm_dashboard_item_title">' . $wcfm_coupons_single->post_title . '</a>';
				} else {
					$wcfm_coupons_json_arr[$index][] =  '<span class="wcfm_dashboard_item_title">' . $wcfm_coupons_single->post_title . '</span>';
				}
				
				// Type
				$wcfm_coupons_json_arr[$index][] =  '<span class="coupon-types coupon-types-' . get_post_meta( $wcfm_coupons_single->ID, 'discount_type', true ) . '">' . esc_html( wc_get_coupon_type( get_post_meta( $wcfm_coupons_single->ID, 'discount_type', true ) ) ) . '</span>';
				
				// Coupon Amount
				$wcfm_coupons_json_arr[$index][] =  esc_html( get_post_meta( $wcfm_coupons_single->ID, 'coupon_amount', true ) );
				
				// Usage Limit
				$wcfm_coupons_json_arr[$index][] = $wc_coupon->get_usage_limit() ? $wc_coupon->get_usage_limit() : '&ndash;';

				// Expire Date
				$wcfm_coupons_json_arr[$index][] = $wc_coupon->get_date_expires() ? date_i18n( wc_date_format(), strtotime( $wc_coupon->get_date_expires() ) ) : '&ndash;';

				// Action
				$actions = '';
				if( $wcfm_coupons_single->post_status == 'publish' ) {
				  $actions .= ( current_user_can( 'edit_published_shop_coupons' ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_coupons_manage_url($wcfm_coupons_single->ID) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
				} else {
					$actions .= ( current_user_can( 'edit_shop_coupons' ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_coupons_manage_url($wcfm_coupons_single->ID) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
				}
				$wcfm_coupons_json_arr[$index][] = apply_filters ( 'wcfm_coupons_actions', $actions, $wcfm_coupons_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_coupons_json_arr) ) $wcfm_coupons_json .= json_encode($wcfm_coupons_json_arr);
		else $wcfm_coupons_json .= '[]';
		$wcfm_coupons_json .= '
													}';
													
		echo $wcfm_coupons_json;
	}
}