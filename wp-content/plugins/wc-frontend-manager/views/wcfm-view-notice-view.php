<?php
/**
 * WCFM plugin view
 *
 * wcfm Notice Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   3.0.6
 */
 
global $wp, $WCFM, $WCFMu, $wcfm;

if( !apply_filters( 'wcfm_is_pref_notice', true ) || !apply_filters( 'wcfm_is_allow_notice', true ) || !apply_filters( 'wcfm_is_allow_view_notice', true ) ) {
	wcfm_restriction_message_show( "View Topic" );
	return;
}

$notice_id = 0;
$topic_title = '';
$topic_content = '';
$allow_reply = 'no';
$close_new_reply = 'no';

if( isset( $wp->query_vars['wcfm-notice-view'] ) && !empty( $wp->query_vars['wcfm-notice-view'] ) ) {
	$notice_post = get_post( $wp->query_vars['wcfm-notice-view'] );
	// Fetching Notice Data
	if($notice_post && !empty($notice_post)) {
		$notice_id = $wp->query_vars['wcfm-notice-view'];
		
		$topic_title = $notice_post->post_title;
		$topic_content = $notice_post->post_content;
		
		$allow_reply = get_post_meta( $notice_id, 'allow_reply', true ) ? get_post_meta( $notice_id, 'allow_reply', true ) : 'no';
		$close_new_reply = get_post_meta( $notice_id, 'close_new_reply', true ) ? get_post_meta( $notice_id, 'close_new_reply', true ) : 'no';
		
	}
}

do_action( 'before_wcfm_notice_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="fa fa-bullhorn"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Topic', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php echo $topic_title; ?></h2>
			
			<?php
			echo '<a id="add_new_notice_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_notices_url().'" data-tip="' . __('Topics', 'wc-frontend-manager') . '"><span class="fa fa-bullhorn"></span><span class="text">' . __( 'Topics', 'wc-frontend-manager') . '</span></a>';
			if( current_user_can('administrator') ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_notice_manage_url($notice_id).'" data-tip="' . __('Edit Topic', 'wc-frontend-manager') . '"><span class="fa fa-edit"></span><span class="text">' . __( 'Edit', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'begin_wcfm_notice_manage_form' ); ?>
	  
		<!-- collapsible -->
		<div class="wcfm-container">
			<div id="notice_manage_general_expander" class="wcfm-content">
				<?php echo $topic_content; ?>
				<div class="topic_date"><?php echo date_i18n( wc_date_format(), strtotime( $notice_post->date_created ) ); ?></div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div><br />
		<!-- end collapsible -->
		
		<?php if( $allow_reply == 'yes' )  { ?>
			<?php 
			if( $wcfm_is_allow_view_notice_reply_view = apply_filters( 'wcfmcap_is_allow_notice_reply_view', true ) ) {
				$args = array(
							'posts_per_page'   => -1,
							'offset'           => 0,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'ASC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'wcfm_notice',
							'post_mime_type'   => '',
							'post_parent'      => $notice_id,
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0 
						);
				
				$args = apply_filters( 'wcfm_notice_args', $args );
				
				$wcfm_notices_array = get_posts( $args );	
				
				echo '<h2>' . __( 'Replies', 'wc-frontend-manager' ) . ' (' . count( $wcfm_notices_array ) . ')</h2><div class="wcfm_clearfix"></div>';
				
				if( !empty( $wcfm_notices_array ) ) {
					foreach( $wcfm_notices_array as $wcfm_notice_reply ) {
					?>
					<!-- collapsible -->
					<div class="wcfm-container">
						<div id="topic_reply_<?php echo $wcfm_notice_reply->ID; ?>" class="topic_reply wcfm-content">
						  <div class="topic_reply_author">
						    <?php
						    $author_id = $wcfm_notice_reply->post_author;
								$wp_user_avatar_id = get_user_meta( $author_id, 'wp_user_avatar', true );
								$wp_user_avatar = wp_get_attachment_url( $wp_user_avatar_id );
								if ( !$wp_user_avatar ) {
									$wp_user_avatar = $WCFM->plugin_url . 'assets/images/user.png';
								}
								?>
								<img src="<?php echo $wp_user_avatar; ?>" /><br />
								<?php
								$userdata = get_userdata( $author_id );
								$first_name = $userdata->first_name;
								$last_name  = $userdata->last_name;
								$display_name  = $userdata->display_name;
								if( $first_name ) {
									echo $first_name . ' ' . $last_name;
								} else {
									echo $display_name;
								}
						    ?>
						    <br /><?php echo date_i18n( wc_date_format(), strtotime( $wcfm_notice_reply->date_created ) ); ?>
						  </div>
						  <div class="topic_reply_content">
								<?php echo $wcfm_notice_reply->post_content; ?>
							</div>
						</div>
					</div>
					<div class="wcfm_clearfix"></div><br />
					<!-- end collapsible -->
					<?php
					}
				}
			} 
			?>
			
			<?php if( $close_new_reply == 'no' ) { ?>
				<?php if( $wcfm_is_allow_view_notice_reply = apply_filters( 'wcfmcap_is_allow_notice_reply', true ) ) { ?>
					<?php do_action( 'before_wcfm_messages_form' ); ?>
					<form id="wcfm_topic_reply_form" class="wcfm">
						<h2><?php _e('New Reply', 'wc-frontend-manager' ); ?></h2>
						<div class="wcfm-clearfix"></div>
						<div class="wcfm-container">
							<div id="wcfm_new_reply_listing_expander" class="wcfm-content">
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_topic_reply_fields', array(
																																																				"topic_reply" => array( 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title' ),
																																																				"topic_id"    => array( 'type' => 'hidden', 'value' => $notice_id )
																																																				) ) );
								?>
								<div id="wcfm_notice_reply_submit">
									<input type="submit" name="save-data" value="<?php _e( 'Send', 'wc-frontend-manager' ); ?>" id="wcfm_reply_send_button" class="wcfm_submit_button" />
								</div>
								<div class="wcfm-clearfix"></div>
								<div class="wcfm-message" tabindex="-1"></div>
								<div class="wcfm-clearfix"></div>
							</div>
						</div>
					</form>
					<?php do_action( 'after_wcfm_messages_form' ); ?>
					<div class="wcfm-clearfix"></div><br />
				<?php } ?>
			<?php } ?>
		<?php } ?>
		
		<?php do_action( 'end_wcfm_notice_manage_form' ); ?>
		
		<?php
		do_action( 'after_wcfm_notice_manage' );
		?>
	</div>
</div>