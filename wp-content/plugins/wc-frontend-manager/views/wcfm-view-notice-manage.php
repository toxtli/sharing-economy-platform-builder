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

if( !apply_filters( 'wcfm_is_pref_notice', true ) || !apply_filters( 'wcfm_is_allow_notice', true ) || !apply_filters( 'wcfm_is_allow_manage_notice', true ) || wcfm_is_vendor() ) {
	wcfm_restriction_message_show( "Manage Topic" );
	return;
}

$notice_id = 0;
$title = '';
$content = '';
$allow_reply = 'yes';
$close_new_reply = 'no';

if( isset( $wp->query_vars['wcfm-notice-manage'] ) && !empty( $wp->query_vars['wcfm-notice-manage'] ) ) {
	$notice_post = get_post( $wp->query_vars['wcfm-notice-manage'] );
	// Fetching Notice Data
	if($notice_post && !empty($notice_post)) {
		$notice_id = $wp->query_vars['wcfm-notice-manage'];
		
		$title = $notice_post->post_title;
		$content = $notice_post->post_content;
		
		$allow_reply = get_post_meta( $notice_id, 'allow_reply', true ) ? get_post_meta( $notice_id, 'allow_reply', true ) : 'yes';
		$close_new_reply = get_post_meta( $notice_id, 'close_new_reply', true ) ? get_post_meta( $notice_id, 'close_new_reply', true ) : 'no';
		
	}
}

do_action( 'before_wcfm_notice_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="fa fa-bullhorn"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Topic', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		<form id="wcfm_notice_manage_form" class="wcfm">
		
			<div class="wcfm-container wcfm-top-element-container">
				<h2><?php if( $notice_id ) { _e('Edit Topic', 'wc-frontend-manager' ); } else { _e('Add Topic', 'wc-frontend-manager' ); } ?></h2>
				
				<?php
				echo '<a id="add_new_notice_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_notices_url().'" data-tip="' . __('Topics', 'wc-frontend-manager') . '"><span class="fa fa-bullhorn"></span><span class="text">' . __( 'Topics', 'wc-frontend-manager') . '</span></a>';
				if( $notice_id ) { echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_notice_view_url($notice_id).'" data-tip="' . __('View Topic', 'wc-frontend-manager') . '"><span class="fa fa-eye"></span><span class="text">' . __( 'View', 'wc-frontend-manager') . '</span></a>'; }
				?>
				<div class="wcfm-clearfix"></div>
			</div>
			<div class="wcfm-clearfix"></div><br />
			
			<?php do_action( 'begin_wcfm_notice_manage_form' ); ?>
	  
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="notice_manage_general_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'notice_manager_fields_general', array(  "title" => array('label' => __('Title', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $title),
																																															"allow_reply" => array('label' => __('Allow Reply', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => $allow_reply),
																																															"close_new_reply" => array('label' => __('Close for New Reply', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $close_new_reply),
																																															"wcfm_notice" => array('label' => __('Content', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $content),
																																															"notice_id" => array('type' => 'hidden', 'value' => $notice_id)
																																					) ) );
						?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			
			<?php do_action( 'end_wcfm_notice_manage_form' ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_notice_manager_submit">
				<input type="submit" name="notice-manager-data" value="<?php _e( 'Submit', 'wc-frontend-manager' ); ?>" id="wcfm_notice_manager_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_notice_manage' );
			?>
		</form>
	</div>
</div>