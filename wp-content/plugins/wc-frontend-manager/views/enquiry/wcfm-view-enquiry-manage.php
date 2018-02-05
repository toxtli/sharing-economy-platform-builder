<?php
/**
 * WCFM plugin view
 *
 * wcfm Enquiry Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view/enquiry
 * @version   3.2.8
 */
 
global $wp, $WCFM, $wpdb;

if( !apply_filters( 'wcfm_is_pref_enquiry', true ) || !apply_filters( 'wcfm_is_allow_enquiry', true ) ) {
	wcfm_restriction_message_show( "Enquiry Board" );
	return;
}

$enquiry_id = 0;
$product_id = 0;
$enquiry = '';
$reply = '';
$is_private = 'no';

if( isset( $wp->query_vars['wcfm-enquiry-manage'] ) && !empty( $wp->query_vars['wcfm-enquiry-manage'] ) ) {
	$enquiry_id = $wp->query_vars['wcfm-enquiry-manage'];
	
	if( !$enquiry_id ) {
		wcfm_restriction_message_show( "Enquiry Board" );
		return;
	}
	
	$enquiry_datas = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wcfm_enquiries WHERE `ID` = {$enquiry_id}" );
	
	if( !empty($enquiry_datas ) ) {
		foreach( $enquiry_datas as $enquiry_data ) {
			$enquiry = $enquiry_data->enquiry;
			$reply = $enquiry_data->reply;
		}
	}
	
	$product_id = $enquiry_data->product_id;
	$is_private = ( $enquiry_data->is_private == 0 ) ? 'no' : 'yes';
}

do_action( 'before_wcfm_enquiry_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="fa fa-question-circle-o"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Enquiry', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
	  	<h2><?php if( $enquiry_id ) { _e('Edit Enquiry', 'wc-frontend-manager' ); } else { _e('Add Enquiry', 'wc-frontend-manager' ); } ?></h2>
			
			<?php
			echo '<a id="add_new_enquiry_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_enquiry_url().'" data-tip="' . __('Enquiries', 'wc-frontend-manager') . '"><span class="fa fa-question-circle-o"></span><span class="text">' . __( 'Enquiries', 'wc-frontend-manager') . '</span></a>';
			if( $enquiry_id ) { echo '<a class="add_new_wcfm_ele_dashboard text_tip" target="_permalink" href="'.get_permalink($product_id).'" data-tip="' . __('View Product', 'wc-frontend-manager') . '"><span class="fa fa-eye"></span></a>'; }
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_enquiry_manage' ); ?>
	  
		<form id="wcfm_enquiry_manage_form" class="wcfm">
		
			<?php do_action( 'begin_wcfm_enquiry_manage_form' ); ?>
			
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="enquiry_manage_general_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'enquiry_manager_fields_general', array(  "enquiry" => array('label' => __('Enquiry', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $enquiry),
																																															"reply" => array('label' => __('Reply', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $reply),
																																															"is_private" => array('label' => __('Is Private?', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => $is_private),
																																															"notify" => array('label' => __('Notify Customer', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes' ),
																																															"enquiry_id" => array('type' => 'hidden', 'value' => $enquiry_id)
																																					) ) );
						?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			
			<?php do_action( 'end_wcfm_enquiry_manage_form' ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_enquiry_manager_submit">
				<input type="submit" name="enquiry-manager-data" value="<?php _e( 'Submit', 'wc-frontend-manager' ); ?>" id="wcfm_enquiry_manager_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_enquiry_manage' );
			?>
		</form>
	</div>
</div>