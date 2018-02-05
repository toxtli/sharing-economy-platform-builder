<?php
/**
 * WCFMu plugin view
 *
 * WCFM Notice view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   3.0.6
 */
 
global $WCFM;


if( !apply_filters( 'wcfm_is_pref_notice', true ) || !apply_filters( 'wcfm_is_allow_notice', true ) ) {
	wcfm_restriction_message_show( "Notice Board" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_notice_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-bullhorn"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Notice Board', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Topics', 'wc-frontend-manager' ); ?></h2>
			
			<?php
			if( !wcfm_is_vendor() ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_notice_manage_url().'" data-tip="' . __('Add New Topic', 'wc-frontend-manager') . '"><span class="fa fa-bullhorn"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager' ) . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<?php do_action( 'before_wcfm_notice' ); ?>

		<div class="wcfm-container">
			<div id="wcfm_notice_listing_expander" class="wcfm-content">
				<table id="wcfm-notice" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Title', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Title', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
			
		<?php do_action( 'after_wcfm_notice' ); ?>
	</div>
</div>
