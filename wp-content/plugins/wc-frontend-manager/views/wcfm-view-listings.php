<?php
/**
 * WCFMu plugin view
 *
 * WCFM Listings view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.4.6
 */
 
global $WCFM;

$wcfm_is_allow_listings = apply_filters( 'wcfm_is_allow_listings', true );
if( !$wcfm_is_allow_listings ) {
	wcfm_restriction_message_show( "Listings" );
	return;
}

$post_a_job = get_permalink ( get_option( 'job_manager_submit_job_form_page_id' ) );
?>

<div class="collapse wcfm-collapse" id="wcfm_listings_listing">

  <div class="wcfm-page-headig">
		<span class="fa fa-briefcase"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Listings', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_listings' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<?php
			$wcfm_listings_menus = apply_filters( 'wcfm_listings_menus', array( 'all' => __( 'All', 'wc-frontend-manager'), 
																																					'publish' => __( 'Published', 'wc-frontend-manager'),
																																					'pending' => __( 'Pending', 'wc-frontend-manager'),
																																					'expired' => __( 'Expired', 'wc-frontend-manager'),
																																					'preview' => __( 'Preview', 'wc-frontend-manager'),
																																					//'pending_payment' => __( 'Pending Payment', 'wc-frontend-manager')
																																				) );
		
			$listing_status = ! empty( $_GET['listing_status'] ) ? sanitize_text_field( $_GET['listing_status'] ) : 'all';
			
			$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			if( current_user_can( 'administrator' ) ) $current_user_id = 0;
			$count_listings = array();
			$count_listings['publish'] = wcfm_get_user_posts_count( $current_user_id, 'job_listing', 'publish' );
			$count_listings['pending'] = wcfm_get_user_posts_count( $current_user_id, 'job_listing', 'pending' );
			$count_listings['expired'] = wcfm_get_user_posts_count( $current_user_id, 'job_listing', 'expired' );
			$count_listings['preview'] = wcfm_get_user_posts_count( $current_user_id, 'job_listing', 'preview' );
			$count_listings['all']     = $count_listings['publish'] + $count_listings['pending'] + $count_listings['expired'];
			?>
			<ul class="wcfm_listings_menus">
				<?php
				$is_first = true;
				foreach( $wcfm_listings_menus as $wcfm_listings_menus_key => $wcfm_listings_menu ) {
					?>
					<li class="wcfm_listings_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfm_listings_menus_key == $listing_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_listings_url( $wcfm_listings_menus_key ); ?>"><?php echo $wcfm_listings_menu . ' ('. $count_listings[$wcfm_listings_menus_key] .')'; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						?>
						<span class="wcfm_screen_manager_dummy text_tip" data-tip="<?php wcfmu_feature_help_text_show( 'Screen Manager', false, true ); ?>"><span class="fa fa-television"></span></span>
						<?php
					}
				} else {
					?>
					<a class="wcfm_screen_manager text_tip" href="#" data-screen="listing" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager' ); ?>"><span class="fa fa-television"></span></a>
					<?php
				}
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=job_listing'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			do_action( 'wcfm_listings_head_actions' );
			if( $has_new = apply_filters( 'wcfm_add_new_listing_sub_menu', true ) ) {
				echo '<a target="_blank" id="add_new_listing_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.$post_a_job.'" data-tip="' . __('Add New Listing', 'wc-frontend-manager') . '"><span class="fa fa-briefcase"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
			</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<div class="wcfm-container">
			<div id="wcfm_listings_listing_expander" class="wcfm-content">
				<table id="wcfm-listings" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Listing', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Filled?', 'wp-job-manager' ); ?></th>
							<th><span class="fa fa-eye text_tip" data-tip="<?php _e( 'Views', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Date Posted', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Listing Expires', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Listing', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Filled?', 'wp-job-manager' ); ?></th>
							<th><span class="fa fa-eye text_tip" data-tip="<?php _e( 'Views', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Date Posted', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Listing Expires', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_listings' );
		?>
	</div>
</div>