<?php
/**
 * WCFMu plugin view
 *
 * Marketplace WC Marketplace Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/dashboards
 * @version   3.3.5
 */
 
global $WCFM;

$user_id = get_current_user_id();
$wp_user_avatar_id = get_user_meta( $user_id, 'wp_user_avatar', true );
$wp_user_avatar = wp_get_attachment_url( $wp_user_avatar_id );
if ( !$wp_user_avatar ) {
	$wp_user_avatar = $WCFM->plugin_url . 'assets/images/user.png';
}
?>

<?php do_action( 'wcfm_before_dashboard_welcome_box' ); ?>

<?php if( apply_filters( 'wcfm_is_pref_welcome_box', true ) ) { ?>
	<div class="wcfm_dashboard_welcome">
		<div class="wcfm_dashboard_welcome_avatar">
			<?php if( $wcfm_is_allow_profile = apply_filters( 'wcfm_is_allow_profile', true ) ) { ?>
				<a href="<?php echo get_wcfm_profile_url(); ?>">
			<?php } ?>
					<img src="<?php echo $wp_user_avatar; ?>" />
			<?php if( $wcfm_is_allow_profile = apply_filters( 'wcfm_is_allow_profile', true ) ) { ?>
				</a>
			<?php } ?>
		</div>
		<div class="wcfm_dashboard_welcome_container">
			<div class="wcfm_dashboard_welcome_content">
				<?php
					$userdata = get_userdata( $user_id );
					$first_name = $userdata->first_name;
					$last_name  = $userdata->last_name;
					$display_name  = $userdata->display_name;
					$previous_login = get_user_meta( $user_id, '_previous_login', true );
				?>
				<h2><?php echo apply_filters( 'wcfm_dashboard_welcometext', sprintf( __('Welcome to %s Dashboard', 'wc-frontend-manager' ), get_bloginfo() ) ); ?></h2>
				<div class="wcfm-clearfix"></div>
				<div class="wcfm_dashboard_welcome_content_userinfo">
					<div class="wcfm_dashboard_welcome_content_userinfo_name">
						<span class="fa fa-user"></span>
						<?php
						if( $first_name ) {
							echo apply_filters( 'wcfm_dashboard_username', $first_name . ' ' . $last_name );
						} else {
							echo apply_filters( 'wcfm_dashboard_username', $display_name );
						}
						?>
					</div>
					<?php if( $previous_login ) { ?>
						<div class="wcfm_dashboard_welcome_content_userinfo_lastvist" title="<?php _e( 'Last Login', 'wc-frontend-manager' ); ?>">
							<span class="fa fa-clock-o"></span>
							<?php echo date_i18n( wc_date_format(), $previous_login ); ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<div class="wcfm-clearfix"></div>
<?php } ?>

<?php do_action( 'wcfm_after_dashboard_welcome_box' ); ?>