<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Knowledgebase Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.3.2
 */

class WCFM_Knowledgebase_Controller {
	
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
							'post_type'        => 'wcfm_knowledgebase',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		$args = apply_filters( 'wcfm_knowledgebase_args', $args );
		
		$wcfm_knowledgebases_array = get_posts( $args );
		
		// Get Knowledgebase Count
		$knowledgebase_count = 0;
		$filtered_knowledgebase_count = 0;
		$wcfm_knowledgebases_count = wp_count_posts('wcfm_knowledgebase');
		$knowledgebase_count = ( $wcfm_knowledgebases_count->publish + $wcfm_knowledgebases_count->pending + $wcfm_knowledgebases_count->draft );
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_knowledgebases_array = get_posts( $args );
		$filtered_knowledgebase_count = count($wcfm_filterd_knowledgebases_array);
		
		
		// Generate Knowledgebases JSON
		$wcfm_knowledgebases_json = '';
		$wcfm_knowledgebases_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $knowledgebase_count . ',
															"recordsFiltered": ' . $filtered_knowledgebase_count . ',
															"data": ';
		if(!empty($wcfm_knowledgebases_array)) {
			$index = 0;
			$wcfm_knowledgebases_json_arr = array();
			foreach($wcfm_knowledgebases_array as $wcfm_knowledgebases_single) {
				// Knowledgebase
				$wcfm_knowledgebases_json_arr[$index][] =  '<a href="' . get_wcfm_knowledgebase_manage_url($wcfm_knowledgebases_single->ID) . '" class="wcfm_dashboard_item_title">' . $wcfm_knowledgebases_single->post_title . '</a>';
				
				// Action
				$actions = '<a class="wcfm-action-icon" href="' . get_wcfm_knowledgebase_manage_url($wcfm_knowledgebases_single->ID) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>';
				$actions .= '<a class="wcfm_knowledgebase_delete wcfm-action-icon" href="#" data-knowledgebaseid="' . $wcfm_knowledgebases_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>';
				$wcfm_knowledgebases_json_arr[$index][] = apply_filters ( 'wcfm_knowledgebase_actions', $actions, $wcfm_knowledgebases_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_knowledgebases_json_arr) ) $wcfm_knowledgebases_json .= json_encode($wcfm_knowledgebases_json_arr);
		else $wcfm_knowledgebases_json .= '[]';
		$wcfm_knowledgebases_json .= '
													}';
													
		echo $wcfm_knowledgebases_json;
	}
}