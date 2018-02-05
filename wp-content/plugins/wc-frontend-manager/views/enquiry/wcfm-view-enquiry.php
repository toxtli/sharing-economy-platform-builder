<?php
/**
 * WCFMu plugin view
 *
 * WCFM Enquiry view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/enquiry
 * @version   3.0.6
 */
 
global $WCFM;


if( !apply_filters( 'wcfm_is_pref_enquiry', true ) || !apply_filters( 'wcfm_is_allow_enquiry', true ) ) {
	wcfm_restriction_message_show( "Enquiry Board" );
	return;
}

$args = array(
					'posts_per_page'   => -1,
					'offset'           => 0,
					'category'         => '',
					'category_name'    => '',
					'orderby'          => 'date',
					'order'            => 'DESC',
					'include'          => '',
					'exclude'          => '',
					'meta_key'         => '',
					'meta_value'       => '',
					'post_type'        => 'product',
					'post_mime_type'   => '',
					'post_parent'      => '',
					//'author'	   => get_current_user_id(),
					'post_status'      => array('publish'),
					'suppress_filters' => 0 
				);
$args = apply_filters( 'wcfm_products_args', $args );

$products_objs = get_posts( $args );
$products_array = array( '' => __( 'Filter by Product', 'wc-frontend-manager' ) . ' ...' );
if( !empty($products_objs) ) {
	foreach( $products_objs as $products_obj ) {
		$products_array[esc_attr( $products_obj->ID )] = esc_html( $products_obj->post_title );
	}
}

$ranges = array(
	'7day'         => __( 'Last 7 Days', 'wc-frontend-manager' ),
	'month'        => __( 'This Month', 'wc-frontend-manager' ),
	'last_month'   => __( 'Last Month', 'wc-frontend-manager' ),
	'year'         => __( 'Year', 'wc-frontend-manager' ),
);

?>

<div class="collapse wcfm-collapse" id="wcfm_enquiry_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-question-circle-o"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Enquiry Board', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Enquiries', 'wc-frontend-manager' ); ?></h2>
			
			<?php
			//echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_enquiry_manage_url().'" data-tip="' . __('Add New Topic', 'wc-frontend-manager') . '"><span class="fa fa-question-circle-o"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager' ) . '</span></a>';
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_enquiry' ); ?>
	  
		<div class="wcfm_enquiry_filter_wrap wcfm_filters_wrap">
		  <label style="margin-left: 10px;">
				<?php
				echo '&nbsp;&nbsp;<select id="dropdown_report_filter" name="dropdown_report_filter" class="dropdown_report_filter" style="width: 150px;">';
					if ( $ranges ) {
						foreach( $ranges as $range => $range_label ) {
							echo '<option value="' . $range . '">' . $range_label . '</option>';
						}
					}
				echo '</select>';
				?>
			</label>
		  <?php 
		  $WCFM->wcfm_fields->wcfm_generate_form_field( array( "enquiry_product" => array( 'type' => 'select', 'attributes' => array( 'style' => 'width: 150px;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $products_array ) ) ); 
		  
		  if( $wcfm_is_products_vendor_filter = apply_filters( 'wcfm_is_products_vendor_filter', true ) ) {
				$is_marketplace = wcfm_is_marketplace();
				if( $is_marketplace ) {
					if( !wcfm_is_vendor() ) {
						$vendor_arr = $WCFM->wcfm_vendor_support->wcfm_get_vendor_list();
						$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																											"dropdown_vendor" => array( 'type' => 'select', 'options' => $vendor_arr, 'attributes' => array( 'style' => 'width: 150px;' ) )
																											 ) );
					}
				}
			}
		  ?>
		</div>

		<div class="wcfm-container">
			<div id="wcfm_enquiry_listing_expander" class="wcfm-content">
				<table id="wcfm-enquiry" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Query', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Customer', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Store', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Reply', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Query', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Customer', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Store', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Reply', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
			
		<?php do_action( 'after_wcfm_enquiry' ); ?>
	</div>
</div>
