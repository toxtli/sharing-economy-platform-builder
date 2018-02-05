<?php
/**
 * WCFMu plugin view
 *
 * WCFM Knowledgebase view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.3.2
 */
 
global $WCFM;


if( !apply_filters( 'wcfm_is_pref_knowledgebase', true ) || !apply_filters( 'wcfm_is_allow_knowledgebase', true ) ) {
	wcfm_restriction_message_show( "Knowledgesbase" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_knowledgebase_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-book"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Knowledgebase', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Guidelines for Store Users', 'wc-frontend-manager' ); ?></h2>
			
			<?php
			if( !wcfm_is_vendor() ) {
				if( $has_new = apply_filters( 'wcfm_add_new_knowledgebase_sub_menu', true ) ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_knowledgebase_manage_url().'" data-tip="' . __('Add New Knowledgebase', 'wc-frontend-manager') . '"><span class="fa fa-book"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager' ) . '</span></a>';
				}
			}
			?>
		
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<?php do_action( 'before_wcfm_knowledgebase' ); ?>
		<?php
		if( wcfm_is_vendor() ) {
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
							'post_type'        => 'wcfm_knowledgebase',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('publish'),
							'suppress_filters' => 0 
						);
			$wcfm_knowledgebases = get_posts( $args );
			
			if( !empty( $wcfm_knowledgebases ) ) {
				foreach( $wcfm_knowledgebases as $wcfm_knowledgebase ) {
					?>
					<div class="page_collapsible" id="wcfm_knowledgebase_listing_head-<?php echo $wcfm_knowledgebase->ID; ?>">
						<label class="fa fa-bookmark"></label>
						<?php echo $wcfm_knowledgebase->post_title; ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_knowledgebase_listing_expander-<?php echo $wcfm_knowledgebase->ID; ?>" class="wcfm_knowledgebase wcfm-content">
							<?php echo $wcfm_knowledgebase->post_content; ?>
						</div>
					</div>
					<div class="wcfm-clearfix"></div><br />
					<?php
				}
			}
		} else {
			?>
			<div class="wcfm-container">
				<div id="wcfm_knowledgebase_listing_expander" class="wcfm-content">
					<table id="wcfm-knowledgebase" class="display" cellspacing="0" width="100%">
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
			<?php
		}
		?>
		<?php do_action( 'after_wcfm_knowledgebase' ); ?>
	</div>
</div>
