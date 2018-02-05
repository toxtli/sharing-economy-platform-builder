<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Enquiry Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/enquiry
 * @version   3.0.6
 */

class WCFM_Enquiry_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$vendor_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
		
		$enquiry_product = '';
		if ( ! empty( $_POST['enquiry_product'] ) ) {
			$enquiry_product = esc_sql( $_POST['enquiry_product'] );
		}
		
		$enquiry_vendor = '';
		if ( ! empty( $_POST['enquiry_vendor'] ) ) {
			$enquiry_vendor = esc_sql( $_POST['enquiry_vendor'] );
		}
		
		$is_private = '';
		if ( ! empty( $_POST['is_private'] ) ) {
			$is_private = esc_sql( $_POST['is_private'] );
		}
		
		$report_for = '7day';
		if( isset($_POST['report_for']) && !empty($_POST['report_for']) ) {
			$report_for = $_POST['report_for'];
		}
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
		$items_per_page = $length;
		
		$sql = "SELECT count(ID) FROM {$wpdb->prefix}wcfm_enquiries AS commission";
		$sql .= " WHERE 1 = 1";
		
		if( $enquiry_product ){
			$sql .= " AND `product_id` = {$enquiry_product}";
		}
		
		if( $is_private ){
			$sql .= " AND `is_private` = {$is_private}";
		}
		
		if( wcfm_is_vendor() ) { 
			$sql .= " AND `vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['enquiry_vendor'] ) ) {
			$sql .= " AND `vendor_id` = {$enquiry_vendor}";
		}
		$sql = wcfm_query_time_range_filter( $sql, 'posted', $report_for );
		$sql = apply_filters( 'wcfm_enquery_count_query', $sql);
		
		$total_enquiries = $wpdb->get_var( $sql );
		
		$enquiry_query = "SELECT * FROM {$wpdb->prefix}wcfm_enquiries AS commission";
		$enquiry_query .= " WHERE 1 = 1";
		
		if( $enquiry_product ){
			$enquiry_query .= " AND `product_id` = {$enquiry_product}";
		}
		
		if( $is_private ){
			$enquiry_query .= " AND `is_private` = {$is_private}";
		}
		
		if( wcfm_is_vendor() ) { 
			$enquiry_query .= " AND `vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['enquiry_vendor'] ) ) {
			$enquiry_query .= " AND `vendor_id` = {$enquiry_vendor}";
		}
		$enquiry_query = wcfm_query_time_range_filter( $enquiry_query, 'posted', $report_for );
		$enquiry_query = apply_filters( 'wcfm_enquery_list_query', $enquiry_query );
		
		$enquiry_query .= " ORDER BY commission.`{$the_orderby}` {$the_order}";

		$enquiry_query .= " LIMIT {$items_per_page}";

		$enquiry_query .= " OFFSET {$offset}";
		
		
		$wcfm_enquirys_array = $wpdb->get_results( $enquiry_query );
		
		// Generate Enquirys JSON
		$wcfm_enquirys_json = '';
		$wcfm_enquirys_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $total_enquiries . ',
															"recordsFiltered": ' . $total_enquiries . ',
															"data": ';
		if(!empty($wcfm_enquirys_array)) {
			$index = 0;
			$wcfm_enquirys_json_arr = array();
			foreach($wcfm_enquirys_array as $wcfm_enquirys_single) {
				// Enquiry
				$wcfm_enquirys_json_arr[$index][] =  '<a href="' . get_wcfm_enquiry_manage_url($wcfm_enquirys_single->ID) . '" class="wcfm_dashboard_item_title">' . $wcfm_enquirys_single->enquiry . '</a>';
				
				// Product
				$wcfm_enquirys_json_arr[$index][] =  '<a class="wcfm-enquiry-product" target="_blank" href="' . get_permalink($wcfm_enquirys_single->product_id) . '">' . get_the_title($wcfm_enquirys_single->product_id) . '</a>';
				
				// Customer
				$wcfm_enquirys_json_arr[$index][] =  $wcfm_enquirys_single->customer_name;
				
				// Vendor
				$vendor_name = '&ndash;';
				if( !$WCFM->is_marketplace || wcfm_is_vendor() ) {
					$wcfm_enquirys_json_arr[$index][] =  $vendor_name;
				} else {
					if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						$vendor_terms = wp_get_post_terms( $wcfm_enquirys_single->product_id, 'dc_vendor_shop' );
						foreach( $vendor_terms as $vendor_term ) {
							$vendor_name = $vendor_term->name;
						}
					} elseif( $WCFM->is_marketplace == 'wcpvendors' ) {
						$vendor_terms = wp_get_post_terms( $wcfm_enquirys_single->product_id, 'wcpv_product_vendors' );
						foreach( $vendor_terms as $vendor_term ) {
							$vendor_name = $vendor_term->name;
						}
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						$vendor_name = get_user_meta( $wcfm_enquirys_single->author_id, 'dokan_store_name', true );
					}
					$wcfm_enquirys_json_arr[$index][] =  $vendor_name;
				}
				
				// Reply
				if( $wcfm_enquirys_single->reply ) {
					$wcfm_enquirys_json_arr[$index][] =  $wcfm_enquirys_single->reply;
				} else {
					$wcfm_enquirys_json_arr[$index][] = '&ndash;'; 
				}
				
				// Date
				$wcfm_enquirys_json_arr[$index][] = date_i18n( wc_date_format(), strtotime( $wcfm_enquirys_single->posted ) );
				
				// Action
				$actions = '<a class="wcfm-action-icon" href="' . get_wcfm_enquiry_manage_url($wcfm_enquirys_single->ID) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>';
				$actions .= '<a class="wcfm_enquiry_delete wcfm-action-icon" href="#" data-enquiryid="' . $wcfm_enquirys_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>';
				
				
				$wcfm_enquirys_json_arr[$index][] = apply_filters ( 'wcfm_enquiry_actions', $actions, $wcfm_enquirys_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_enquirys_json_arr) ) $wcfm_enquirys_json .= json_encode($wcfm_enquirys_json_arr);
		else $wcfm_enquirys_json .= '[]';
		$wcfm_enquirys_json .= '
													}';
													
		echo $wcfm_enquirys_json;
	}
}