<?php
/**
 * Setup panel.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Builder_Panel_Setup extends WPForms_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define panel information
		$this->name  = __( 'Setup', 'wpforms' );
		$this->slug  = 'setup';
		$this->icon  = 'fa-cog';
		$this->order = 5;
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 1.0.0
	 */
	public function enqueues() {

		// CSS
		wp_enqueue_style(
			'wpforms-builder-setup',
			WPFORMS_PLUGIN_URL . 'assets/css/admin-builder-setup.css',
			null,
			WPFORMS_VERSION
		);
	}

	/**
	 * Outputs the Settings panel primary content.
	 *
	 * @since 1.0.0
	 */
	public function panel_content() {

		$core_templates       = apply_filters( 'wpforms_form_templates_core', array() );
		$additional_templates = apply_filters( 'wpforms_form_templates',      array() );
		$additional_count     = count( $additional_templates );
		?>
		<div id="wpforms-setup-form-name">
			<span><?php _e( 'Form Name', 'wpforms' ); ?></span>
			<input type="text" id="wpforms-setup-name" placeholder="<?php _e( 'Enter your form name here&hellip;', 'wpforms' ); ?>">
		</div>

		<div class="wpforms-setup-title core">
			<?php _e( 'Select a Template', 'wpforms' ); ?>
		</div>

		<p class="wpforms-setup-desc core">
			<?php _e( 'To speed up the process, you can select from one of our pre-made templates or start with a <strong><a href="#" class="wpforms-trigger-blank">blank form.</a></strong>', 'wpforms' ); ?>
		</p>

		<?php $this->template_select_options( $core_templates, 'core' ); ?>

		<div class="wpforms-setup-title additional">
			<?php _e( 'Additional Templates', 'wpforms' ); ?>
			<?php echo ! empty( $additional_count ) ? '<span class="count">(' . $additional_count . ')</span>' : ''; ?>
		</div>

		<?php if ( ! empty( $additional_count ) ) : ?>

			<p class="wpforms-setup-desc additional">
				<?php
				/* translators: %1$s - opening tag, %2$s - closing tag, %3$s - opening tag, %4$s - closing tag. */
				printf(
					__(
						'Have a suggestion for a new template? %1$sWe\'d love to hear it%2$s. Also, you can %3$screate your own templates%4$s!',
						'wpforms'
					),
					'<a href="https://wpforms.com/form-template-suggestion/" target="_blank" rel="noopener noreferrer">',
					'</a>',
					'<a href="https://wpforms.com/docs/how-to-create-a-custom-form-template/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				);
				?>
			</p>

			<div class="wpforms-setup-template-search-wrap">
				<i class="fa fa-search" aria-hidden="true"></i><input type="text" id="wpforms-setup-template-search" value="" placeholder="<?php _e( 'Search additional templates...', 'wpforms' ); ?>">
			</div>

			<?php $this->template_select_options( $additional_templates, 'additional' ); ?>

		<?php else : ?>

			<p class="wpforms-setup-desc additional">
				<?php
				/* translators:%1$s - opening a tag, %2$s - close tag, %3$s - opening tag, %4$s - close tag. */
				printf(
					__(
						'More are available in the %1$sForm Templates Pack addon%2$s or by %3$screating your own%4$s.',
						'wpforms'
					),
					'<a href="https://wpforms.com/addons/form-templates-pack-addon/" target="_blank" rel="noopener noreferrer">',
					'</a>',
					'<a href="https://wpforms.com/docs/how-to-create-a-custom-form-template/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				);
				?>
			</p>

		<?php
		endif;
		do_action( 'wpforms_setup_panel_after' );
	}

	/**
	 * Generate a block of templates to choose from.
	 *
	 * @since 1.4.0
	 * @param array $templates
	 * @param string $slug
	 */
	public function template_select_options( $templates, $slug ) {

		if ( ! empty( $templates ) ) {

			echo '<div id="wpforms-setup-templates-' . $slug . '" class="wpforms-setup-templates ' . $slug . ' wpforms-clear">';

				echo '<div class="list">';

				// Loop through each available template.
				foreach ( $templates as $template ) {

					$selected = ! empty( $this->form_data['meta']['template'] ) && $this->form_data['meta']['template'] === $template['slug'] ? true : false;
					?>
					<div class="wpforms-template <?php echo $selected ? 'selected' : ''; ?>" id="wpforms-template-<?php echo sanitize_html_class( $template['slug'] ); ?>">

						<div class="wpforms-template-inner">

							<div class="wpforms-template-name wpforms-clear">
								<?php echo esc_html( $template['name'] ); ?>
								<?php echo $selected ? '<span class="selected">' . __( 'Selected', 'wpforms' ) . '</span>' : ''; ?>
							</div>

							<?php if ( ! empty( $template['description'] ) ) : ?>
							<div class="wpforms-template-details">
								<p class="desc"><?php echo esc_html( $template['description'] ); ?></p>
							</div>
							<?php endif; ?>

							<div class="wpforms-template-overlay">
								<a href="#" class="wpforms-template-select" data-template-name-raw="<?php echo esc_attr( $template['name'] ); ?>" data-template-name="<?php printf( _x( '%s template', 'Template name', 'wpforms' ), esc_attr( $template['name'] ) ); ?>" data-template="<?php echo esc_attr( $template['slug'] ); ?>"><?php printf( _x( 'Create a %s', 'Template name', 'wpforms' ), esc_html( $template['name'] ) ); ?></a>
							</div>

						</div>

					</div>
					<?php
				}

				echo '</div>';

			echo '</div>';
		}
	}
}
new WPForms_Builder_Panel_Setup;
