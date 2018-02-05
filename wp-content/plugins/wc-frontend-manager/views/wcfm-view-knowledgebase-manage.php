<?php
/**
 * WCFM plugin view
 *
 * wcfm Knowledgebase Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.0.0
 */
 
global $wp, $WCFM, $WCFMu, $wcfm;

if( !apply_filters( 'wcfm_is_pref_knowledgebase', true ) || !apply_filters( 'wcfm_is_allow_knowledgebase', true ) || !apply_filters( 'wcfm_is_allow_manage_knowledgebase', true ) || wcfm_is_vendor() ) {
	wcfm_restriction_message_show( "Manage Knowledgebase" );
	return;
}

$knowledgebase_id = 0;
$title = '';
$content = '';

if( isset( $wp->query_vars['wcfm-knowledgebase-manage'] ) && !empty( $wp->query_vars['wcfm-knowledgebase-manage'] ) ) {
	$knowledgebase_post = get_post( $wp->query_vars['wcfm-knowledgebase-manage'] );
	// Fetching Knowledgebase Data
	if($knowledgebase_post && !empty($knowledgebase_post)) {
		$knowledgebase_id = $wp->query_vars['wcfm-knowledgebase-manage'];
		
		$title = $knowledgebase_post->post_title;
		$content = $knowledgebase_post->post_content;
		
	}
}

do_action( 'before_wcfm_knowledgebase_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="fa fa-book"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Knowledgebase', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
	    <h2><?php if( $knowledgebase_id ) { _e('Edit Knowledgebase', 'wc-frontend-manager' ); } else { _e('Add Knowledgebase', 'wc-frontend-manager' ); } ?></h2>
			
			<?php
			echo '<a id="add_new_knowledgebase_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_knowledgebase_url().'" data-tip="' . __('Knowledgebase', 'wc-frontend-manager') . '"><span class="fa fa-book"></span><span class="text">' . __( 'Knowledgebase', 'wc-frontend-manager') . '</span></a>';
			?>
			<div class="wcfm-clearfix"></div>
	  </div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<form id="wcfm_knowledgebase_manage_form" class="wcfm">
		
			<?php do_action( 'begin_wcfm_knowledgebase_manage_form' ); ?>
			
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="knowledgebase_manage_general_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'knowledgebase_manager_fields_general', array(  "title" => array('label' => __('Title', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $title),
																																															"wcfm_knowledgebase" => array('label' => __('Content', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $content),
																																															"knowledgebase_id" => array('type' => 'hidden', 'value' => $knowledgebase_id)
																																					) ) );
						?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			
			<?php do_action( 'end_wcfm_knowledgebase_manage_form' ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_knowledgebase_manager_submit">
				<input type="submit" name="knowledgebase-manager-data" value="<?php _e( 'Submit', 'wc-frontend-manager' ); ?>" id="wcfm_knowledgebase_manager_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_knowledgebase_manage' );
			?>
		</form>
	</div>
</div>