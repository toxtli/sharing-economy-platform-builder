<?php
$tcp_options = get_option( 'elcpt_options' );
$tcp_pts     = isset( $tcp_options['objects'] ) ? $tcp_options['objects'] : array();
?>

<div class="wrap">
	<?php screen_icon( 'plugins' ); ?>
	<h2><?php _e( 'Post Type Template Settings', 'elementor-templater' ); ?></h2>
	<?php if ( isset( $_GET['msg'] ) ) : ?>
		<div id="message" class="updated below-h2">
			<?php if ( $_GET['msg'] == 'update' ) : ?>
				<p><?php _e( 'Settings Updated.', 'elementor-templater' ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<form method="post">

		<?php
		if ( function_exists( 'wp_nonce_field' ) ) {
			wp_nonce_field( 'nonce_elcpt' );}
?>

		<div id="tcp_select_objects">

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<p><?php _e( 'Check to Apply Templates for Custom Post Types', 'elementor-templater' ); ?></p>
					</tr>
					<tr>
						<td>
							<?php
							$post_types = get_post_types(
								array(
									'public' => true,
								), 'objects'
							);

							foreach ( $post_types as $post_type ) {
								if ( $post_type->name == 'attachment' || $post_type->name == 'page' ) {
									continue;
								}
								?>
								<label><input type="checkbox" name="objects[]" value="<?php echo $post_type->name; ?>" 
																									<?php
																									if ( isset( $tcp_pts ) && is_array( $tcp_pts ) ) {
																										if ( in_array( $post_type->name, $tcp_pts ) ) {
																											echo 'checked="checked"';
																										}
																									}
									?>
									>&nbsp;<?php echo $post_type->label; ?></label><br>
									<?php
							}
								?>
						</td>
					</tr>
				</tbody>
			</table>

		</div>

				<p class="submit">
			<input id="submit" class="button button-primary" name="elcpt_submit" value="<?php _e( 'Save Changes', 'elementor-templater' ); ?>" type="submit">
		</p>

	</form>
</div>
