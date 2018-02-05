<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Reports Out of Stock Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Reports_Out_Of_Stock_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$max_items = 0;
		$items     = array();

		// Get products using a query - this is too advanced for get_posts :(
		$stock = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );

		$query_from = apply_filters( 'wcfm_report_out_of_stock_query_from', apply_filters( 'woocommerce_report_out_of_stock_query_from', "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
		" ), $stock );

		$items     = $wpdb->get_results( $wpdb->prepare( "SELECT posts.ID as id, posts.post_parent as parent {$query_from} GROUP BY posts.ID ORDER BY posts.post_title DESC LIMIT %d, %d;", ( $current_page - 1 ) * $offset, $length ) );
		$max_items = $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" );
		
		// Get Product Count
		$out_of_stock_count = 0;
		$filtered_out_of_stock_count = 0;
		$out_of_stock_count = count($max_items);
		// Get Filtered Post Count
		$filtered_out_of_stock_count = count($items);
		
		
		// Generate Products JSON
		$wcfm_out_of_stocks_json = '';
		$wcfm_out_of_stocks_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $out_of_stock_count . ',
															"recordsFiltered": ' . $filtered_out_of_stock_count . ',
															"data": ';
		if(!empty($items)) {
			$index = 0;
			$wcfm_out_of_stocks_json_arr = array();
			foreach($items as $wcfm_product_single) {
				$the_product = wc_get_product( $wcfm_product_single->id );
				// Product
				$pro_title = '';
				if ( $sku = $the_product->get_sku() ) {
					$pro_title = $sku . ' - ';
				}
				$pro_title .= $the_product->get_title();
				
				// Get variation data
				if ( $the_product->is_type( 'variation' ) ) {
					$list_attributes = array();
					$attributes = $the_product->get_variation_attributes();

					foreach ( $attributes as $name => $attribute ) {
						$list_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': <strong>' . $attribute . '</strong>';
					}

					$pro_title .= '<div class="description">' . implode( ', ', $list_attributes ) . '</div>';
				}
					 
				$wcfm_out_of_stocks_json_arr[$index][] = $pro_title;
				
				// Parent
				if ( $wcfm_product_single->parent ) {
					$wcfm_out_of_stocks_json_arr[$index][] = get_the_title( $wcfm_product_single->parent );
				} else {
					$wcfm_out_of_stocks_json_arr[$index][] = '-';
				}
				
				// Units of Stock
				$wcfm_out_of_stocks_json_arr[$index][] = $the_product->get_stock_quantity();
				
				// Stock Status
				if ( $the_product->is_in_stock() ) {
					$stock_html = '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark>';
				} else {
					$stock_html = '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</mark>';
				}
				$wcfm_out_of_stocks_json_arr[$index][] = apply_filters( 'woocommerce_admin_stock_html', $stock_html, $the_product );
				
				// Action
				$actions = '<a class="wcfm-action-icon" target="_blank" href="' . get_permalink( $wcfm_product_single->id ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View', 'wc-frontend-manager' ) . '"></span></a>';
				if( $the_product->post->post_status == 'publish' ) {
					$actions .= ( current_user_can( 'edit_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_edit_product_url($wcfm_product_single->id, $the_product) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
					$actions .= ( current_user_can( 'delete_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) ? '<a class="wcfm_product_delete wcfm-action-icon" href="#" data-proid="' . $wcfm_product_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>' : '';
				} else {
					$actions .= ( current_user_can( 'edit_products' ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_edit_product_url($wcfm_product_single->id, $the_product) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
					$actions .= ( current_user_can( 'delete_products' ) ) ? '<a class="wcfm_product_delete wcfm-action-icon" href="#" data-proid="' . $wcfm_product_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>' : '';
				}
				$wcfm_out_of_stocks_json_arr[$index][] =  apply_filters ( 'wcfm_products_actions',  $actions, $the_product );
				
				$index++;
			}												
		}
		if( !empty($wcfm_out_of_stocks_json_arr) ) $wcfm_out_of_stocks_json .= json_encode($wcfm_out_of_stocks_json_arr);
		else $wcfm_out_of_stocks_json .= '[]';
		$wcfm_out_of_stocks_json .= '
													}';
													
		echo $wcfm_out_of_stocks_json;
	}
}