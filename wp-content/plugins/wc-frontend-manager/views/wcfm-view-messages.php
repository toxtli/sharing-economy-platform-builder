<?php
/**
 * WCFMu plugin view
 *
 * WCFM Messages view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.3.2
 */
 
global $WCFM;

if( !apply_filters( 'wcfm_is_pref_direct_message', true ) || !apply_filters( 'wcfm_is_allow_notifications', true ) ) {
	wcfm_restriction_message_show( "Notifications" );
	return;
}

$wcfm_messages = '';

$is_marketplace = wcfm_is_marketplace();
$user_arr = array();
if( $is_marketplace ) {
	$user_arr = $WCFM->wcfm_vendor_support->wcfm_get_vendor_list(true);
}

$message_status = 'unread';
$message_types = get_wcfm_message_types();
?>

<div class="collapse wcfm-collapse" id="wcfm_messages_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-bell-o"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Notification Dashboard', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<?php do_action( 'before_wcfm_messages_form' ); ?>
		
		<?php if( apply_filters( 'wcfm_is_allow_direct_message', true ) ) { ?>
			<?php if( !wcfm_is_vendor() || ( wcfm_is_vendor() && WCFM_Dependencies::wcfmu_plugin_active_check() ) ) { ?>
				<form id="wcfm_messages_form" class="wcfm">
				  <div class="wcfm-container wcfm-top-element-container">
						<h2><?php if( wcfm_is_vendor() ) { _e('To Store Admin', 'wc-frontend-manager' ); } else { _e('To Store Vendors', 'wc-frontend-manager' ); } ?></h2>
						<div class="wcfm-clearfix"></div>
					</div>
					<div class="wcfm-clearfix"></div><br />
					<div class="wcfm-container">
						<div id="wcfm_messages_listing_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_messages_field_users', array(
																																																			"wcfm_messages" => array( 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_messages ),
																																																			) ) );
							?>
							
							<div id="wcfm_messages_users_block">
								<?php
								if( $is_marketplace && !wcfm_is_vendor() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_messages_fields', array(
																																																		"direct_to" => array( 'label' => __( 'Direct TO:', 'wc-frontend-manager' ), 'type' => 'select', 'options' => $user_arr, 'attributes' => array( 'style' => 'width: 150px;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => 1 ),
																																																		) ) );
								}
								?>
							</div>
							<div id="wcfm_messages_submit">
								<input type="submit" name="save-data" value="<?php _e( 'Send', 'wc-frontend-manager' ); ?>" id="wcfm_messages_save_button" class="wcfm_submit_button" />
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
		
		<?php do_action( 'before_wcfm_messages' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Notifications', 'wc-frontend-manager' ); ?></h2>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />
		
		<div class="wcfm_messages_filter_wrap wcfm_filters_wrap">
		  <input type="submit" id="wcfm_bulk_mark_read" class="wcfm_bulk_mark_read wcfm_submit_button" value="<?php _e( 'Mark Read', 'wc-frontend-manager' ); ?>" />
		  <select name="filter-by-status" id="filter-by-status" style="width: 150px;">
				<option value='unread' <?php echo selected( $message_status, 'unread', false ); ?>><?php esc_html_e( 'Only Unread', 'wc-frontend-manager' ); ?></option>
				<option value='read' <?php echo selected( $message_status, 'read', false ); ?>><?php esc_html_e( 'Only Read', 'wc-frontend-manager' ); ?></option>
			</select>
			<select name="filter-by-type" id="filter-by-type" style="width: 150px;">
				<option value='all'><?php esc_html_e( 'All', 'wc-frontend-manager' ); ?></option>
				<?php foreach( $message_types as $message_type => $message_type_label ) { ?>
					<option value='<?php echo $message_type; ?>'><?php echo $message_type_label; ?></option>
				<?php } ?>
			</select>
		</div>
		
		<div class="wcfm-clearfix"></div>
		<div class="wcfm-container">
			<div id="wcfm_messages_listing_expander" class="wcfm-content">
				<table id="wcfm-messages" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>        
						  <th>
								<input type="checkbox" class="wcfm-checkbox bulk_action_checkbox_all text_tip" name="bulk_action_checkbox_all_top" value="yes" data-tip="<?php _e( 'Select all for mark as read', 'wc-frontend-manager' ); ?>" />
						  </th>
						  <th><?php _e( 'Type', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Message', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'From', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'To', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
						  <th>
								<input type="checkbox" class="wcfm-checkbox bulk_action_checkbox_all text_tip" name="bulk_action_checkbox_all_top" value="yes" data-tip="<?php _e( 'Select all for mark as read', 'wc-frontend-manager' ); ?>" />
						  </th>
						  <th><?php _e( 'Type', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Message', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'From', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'To', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_messages' );
		?>
	</div>
</div>
