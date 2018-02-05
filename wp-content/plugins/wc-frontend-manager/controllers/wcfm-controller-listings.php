<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Listings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.4.6
 */

class WCFM_Listings_Controller {
	
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
							'post_type'        => 'job_listing',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish', 'pending_payment'),
							'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		if( isset($_POST['listing_status']) && !empty($_POST['listing_status']) && ( $_POST['listing_status'] != 'all' ) ) $args['post_status'] = $_POST['listing_status'];
		
		$args = apply_filters( 'wcfm_listing_args', $args );
		
		$wcfm_listings_array = get_posts( $args );
		
		// Get Filtered Post Count
		$filtered_listing_count = 0;
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_listings_array = get_posts( $args );
		$filtered_listing_count = count($wcfm_filterd_listings_array);
		
		$jobs_dashboard_url = get_permalink( get_option( 'job_manager_job_dashboard_page_id' ) );
		
		
		// Generate Listings JSON
		$wcfm_listings_json = '';
		$wcfm_listings_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $filtered_listing_count . ',
															"recordsFiltered": ' . $filtered_listing_count . ',
															"data": ';
		if(!empty($wcfm_listings_array)) {
			$index = 0;
			$wcfm_listings_json_arr = array();
			foreach($wcfm_listings_array as $wcfm_listings_single) {
				
				// Listing
				if( $wcfm_allow_listings_edit = apply_filters( 'wcfm_is_allow_listings_edit', true ) ) {
					$wcfm_listings_json_arr[$index][] =  '<a target="_blank" href="' . add_query_arg( array( 'action' => 'edit', 'job_id' => $wcfm_listings_single->ID ), $jobs_dashboard_url ) . '" class="wcfm_listing_title">' . $wcfm_listings_single->post_title . '</a>';
				} else {
					$wcfm_listings_json_arr[$index][] =  '<span class="wcfm_dashboard_item_title">' . $wcfm_listings_single->post_title . '</span>';
				}
				
				// Status
				$wcfm_listings_json_arr[$index][] =  '<span class="listing-types listing-status-' . $wcfm_listings_single->post_status . '">' . ucfirst( $wcfm_listings_single->post_status ) . '</span>';
				
				// Filled?
				$wcfm_listings_json_arr[$index][] =  is_position_filled( $wcfm_listings_single ) ? '&#10004;' : '&ndash;';
				
				// Views
				$wcfm_listings_json_arr[$index][] =  '<span class="view_count">' . (int) get_post_meta( $wcfm_listings_single->ID, '_wcfm_listing_views', true ) . '</span>';
				
				// Date Posted
				$wcfm_listings_json_arr[$index][] = date_i18n( get_option( 'date_format' ), strtotime( $wcfm_listings_single->post_date ) );

				// Listing Expires
				$wcfm_listings_json_arr[$index][] = $wcfm_listings_single->_job_expires ? date_i18n( get_option( 'date_format' ), strtotime( $wcfm_listings_single->_job_expires ) ) : '&ndash;';

				// Action
				$actions = '';
				$actions .= '<a target="_blank" class="wcfm-action-icon" href="' . get_permalink( $wcfm_listings_single->ID ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View', 'wc-frontend-manager' ) . '"></span></a>';
				
				if( $wcfm_allow_listings_edit = apply_filters( 'wcfm_is_allow_listings_edit', true ) ) {
					$actions .= '<a target="_blank" class="wcfm-action-icon" href="' . add_query_arg( array( 'action' => 'edit', 'job_id' => $wcfm_listings_single->ID ), $jobs_dashboard_url ) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>';
				}
				
				if( $wcfm_listings_single->post_status == 'publish' ) {
					if( $wcfm_allow_listings_mark_filled = apply_filters( 'wcfm_is_allow_listings_mark_filled', true ) ) {
						if ( is_position_filled( $wcfm_listings_single->ID ) ) {
							$actions .= '<a target="_blank" class="wcfm-action-icon" href="' . wp_nonce_url( add_query_arg( array( 'action' => 'mark_not_filled', 'job_id' => $wcfm_listings_single->ID ), $jobs_dashboard_url ), 'job_manager_my_job_actions' ) . '"><span class="fa fa-check-square-o text_tip" data-tip="' . esc_attr__( 'Mark not filled', 'wp-job-manager' ) . '"></span></a>';
						} else {
							$actions .= '<a target="_blank" class="wcfm-action-icon" href="' . wp_nonce_url( add_query_arg( array( 'action' => 'mark_filled', 'job_id' => $wcfm_listings_single->ID ), $jobs_dashboard_url ), 'job_manager_my_job_actions' ) . '"><span class="fa fa-check-square-o text_tip" data-tip="' . esc_attr__( 'Mark filled', 'wp-job-manager' ) . '"></span></a>';
						}
					}
					
					if( $wcfm_allow_listings_duplicate = apply_filters( 'wcfm_is_allow_listings_duplicate', true ) ) {
						$actions .= '<a target="_blank" class="wcfm-action-icon" href="' . wp_nonce_url( add_query_arg( array( 'action' => 'duplicate', 'job_id' => $wcfm_listings_single->ID ), $jobs_dashboard_url ), 'job_manager_my_job_actions' ) . '"><span class="fa fa-copy text_tip" data-tip="' . esc_attr__( 'Duplicate', 'wp-job-manager' ) . '"></span></a>';
					}
				}
				
				if( $wcfm_allow_listings_relist = apply_filters( 'wcfm_is_allow_listings_relist', true ) ) {
					if( $wcfm_listings_single->post_status == 'expired' ) {
						if ( job_manager_get_permalink( 'submit_job_form' ) ) {
							$actions .= '<a target="_blank" class="wcfm-action-icon" href="' . wp_nonce_url( add_query_arg( array( 'action' => 'relist', 'job_id' => $wcfm_listings_single->ID ), $jobs_dashboard_url ), 'job_manager_my_job_actions' ) . '"><span class="fa fa-retweet text_tip" data-tip="' . esc_attr__( 'Relist', 'wp-job-manager' ) . '"></span></a>';
						}
					}
				}
				
				if( $wcfm_allow_listings_delete = apply_filters( 'wcfm_is_allow_listings_delete', true ) ) {
					$actions .= '<a target="_blank" class="wcfm-action-icon" href="' . wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'job_id' => $wcfm_listings_single->ID ), $jobs_dashboard_url ), 'job_manager_my_job_actions' ) . '"><span class="fa fa-trash text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>';
				}
				
				
				$wcfm_listings_json_arr[$index][] = apply_filters ( 'wcfm_listings_actions', $actions, $wcfm_listings_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_listings_json_arr) ) $wcfm_listings_json .= json_encode($wcfm_listings_json_arr);
		else $wcfm_listings_json .= '[]';
		$wcfm_listings_json .= '
													}';
													
		echo $wcfm_listings_json;
	}
}