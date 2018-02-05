<?php
/**
 * Settings class.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Settings {

	/**
	 * The current active tab.
	 *
	 * @since 1.3.9
	 * @var array
	 */
	public $view;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Maybe load settings page.
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Determing if the user is viewing the settings page, if so, party on.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		// Only load if we are actually on the settings page.
		if ( 'wpforms-settings' === $page ) {

			// Include API callbacks and functions.
			require_once WPFORMS_PLUGIN_DIR . 'includes/admin/settings-api.php';

			// Watch for triggered save.
			$this->save_settings();

			// Determine the current active settings tab.
			$this->view = isset( $_GET['view'] ) ? esc_html( $_GET['view'] ) : 'general';

			add_action( 'admin_enqueue_scripts',         array( $this, 'enqueues'             ) );
			add_action( 'wpforms_admin_settings_after',  array( $this, 'captcha_addon_notice' ) );
			add_action( 'wpforms_admin_page',            array( $this, 'output'               ) );

			// Hook for addons.
			do_action( 'wpforms_settings_init' );
		}
	}

	/**
	 * Sanitize and save setings.
	 *
	 * @since 1.3.9
	 */
	public function save_settings() {

		// Check nonce and other various security checks.
		if ( ! isset( $_POST['wpforms-settings-submit'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], 'wpforms-settings-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( apply_filters( 'wpforms_manage_cap', 'manage_options' ) ) ) {
			return;
		}

		if ( empty( $_POST['view'] ) ) {
			return;
		}

		// Get registered fields and current settings.
		$fields   = $this->get_registered_settings( $_POST['view'] );
		$settings = get_option( 'wpforms_settings', array() );

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}

		// Sanitize and prep each field.
		foreach ( $fields as $id => $field ) {

			// Certain field types are not valid for saving and are skipped.
			$exclude = apply_filters( 'wpforms_settings_exclude_type', array( 'content', 'license', 'providers' ) );

			if ( empty( $field['type'] ) || in_array( $field['type'], $exclude, true ) ) {
				continue;
			}

			$value     = isset( $_POST[ $id ] ) ? trim( $_POST[ $id ] ) : false;
			$vaue_prev = isset( $settings[ $id ] ) ? $settings[ $id ] : false;

			// Custom filter can be provided for sanitizing, otherwise use
			// defaults.
			if ( ! empty( $field['filter'] ) && function_exists( $field['filter'] ) ) {

				$value = call_user_func( $field['filter'], $value, $id, $field, $value_prev );

			} else {

				switch ( $field['type'] ) {
					case 'checkbox':
						$value = (bool) $value;
						break;
					case 'image':
						$value = esc_url_raw( $value );
						break;
					case 'color':
						$value = wpforms_sanitize_hex_color( $value );
						break;
					case 'text':
					case 'radio':
					case 'select':
					default:
						$value = sanitize_text_field( $value );
						break;
				}
			}

			// Add to settings.
			$settings[ $id ] = $value;
		}

		// Save settings.
		update_option( 'wpforms_settings', $settings );

		WPForms_Admin_Notice::success( __( 'Settings were successfully saved.', 'wpforms' ) );
	}

	/**
	 * Enqueue assets for the settings page.
	 *
	 * @since 1.0.0
	 */
	public function enqueues() {

		// Hook for addons.
		do_action( 'wpforms_settings_enqueue' );
	}

	/**
	 * Return registered settings tabs.
	 *
	 * @since 1.3.9
	 * @return array
	 */
	public function get_tabs() {

		$tabs = array(
			'general' => array(
				'name' => __( 'General', 'wpforms' ),
				'form'   => true,
				'submit' => __( 'Save Settings', 'wpforms' ),
			),
			'email' => array(
				'name' => __( 'Email', 'wpforms' ),
				'form'   => true,
				'submit' => __( 'Save Settings', 'wpforms' ),
			),
			'recaptcha' => array(
				'name' => __( 'reCAPTCHA', 'wpforms' ),
				'form'   => true,
				'submit' => __( 'Save Settings', 'wpforms' ),
			),
			'validation' => array(
				'name' => __( 'Validation', 'wpforms' ),
				'form'   => true,
				'submit' => __( 'Save Settings', 'wpforms' ),
			),
			'integrations' => array(
				'name'   => __( 'Integrations', 'wpforms' ),
				'form'   => false,
				'submit' => false,
			),
		);
		return apply_filters( 'wpforms_settings_tabs', $tabs );
	}

	/**
	 * Output tab navigation area.
	 *
	 * @since 1.3.9
	 */
	public function tabs() {

		$tabs = $this->get_tabs();

		echo '<ul class="wpforms-admin-tabs">';
			foreach ( $tabs as $id => $tab ) {

				$active = $id === $this->view ? 'active' : '';
				$name   = esc_html( $tab['name'] );
				$link   = esc_url_raw( add_query_arg( 'view', $id, admin_url( 'admin.php?page=wpforms-settings' ) ) );

				echo '<li><a href="' . $link . '" class="' . $active . '">' . $name . '</a></li>';
			}
		echo '</ul>';
	}

	/**
	 * Return all the default registered settings fields.
	 *
	 * @since 1.3.9
	 * @param string $view
	 * @return array
	 */
	public function get_registered_settings( $view = '' ) {

		// reCAPTCAH heading description is long so we define it separately.
		$recaptcha_desc  = '<p>' . __( 'reCAPTCHA is a free anti-spam service from Google which helps to protect your website from spam and abuse while letting real people pass through with ease.', 'wpforms' ) . '</p>';
		$recaptcha_desc .= '<p>' . __( 'Google\'s original <a href="https://www.google.com/recaptcha/intro/" target="_blank" rel="noopener noreferrer">v2 reCAPTCHA</a> prompts users to check a box to prove they\'re human, whereas <a href="https://www.google.com/recaptcha/intro/invisible.html" target="_blank" rel="noopener noreferrer">Invisible reCAPTCHA</a> uses advanced technology to detect real users without requiring any input.', 'wpforms' ) . '</p>';
		$recaptcha_desc .= '<p>' . __( 'Sites already using v2 reCAPTCHA will need to create new site keys before switching to the Invisible reCAPTCHA.', 'wpforms' ) . '</p>';
		$recaptcha_desc .= '<p>' . sprintf( __( '<a href="%s" rel="noopener noreferrer" target="_blank">Read our walk through</a> to learn more and for step-by-step directions.', 'wpforms' ), 'https://wpforms.com/docs/setup-captcha-wpforms/' ) . '</p>';

		$defaults = array(
			// General Settings tab.
			'general' => array(
				'license-heading' => array(
					'id'       => 'license-heading',
					'content'  => '<h4>' . __( 'License', 'wpforms' ) . '</h4><p>' . __( 'Your license key provides access to updates and addons.', 'wpforms' ) . '</p>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => array( 'section-heading' ),
				),
				'license-key' => array(
					'id'      => 'license-key',
					'name'    => __( 'License Key', 'wpforms' ),
					'type'    => 'license',
				),
				'general-heading' => array(
					'id'       => 'general-heading',
					'content'  => '<h4>' . __( 'General', 'wpforms' ) . '</h4>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => array( 'section-heading', 'no-desc' ),
				),
				'disable-css' => array(
					'id'        => 'disable-css',
					'name'      => __( 'Include Form Styling', 'wpforms' ),
					'desc'      => __( 'Determines which CSS files to load for the site.', 'wpforms' ),
					'type'      => 'select',
					'choicesjs' => true,
					'default'   => 1,
					'options'   => array(
						1 => __( 'Base and form theme styling', 'wpforms' ),
						2 => __( 'Base styling only', 'wpforms' ),
						3 => __( 'No styling', 'wpforms' ),
					),
				),
				'global-assets' => array(
					'id'      => 'global-assets',
					'name'    => __( 'Load Assets Globally', 'wpforms' ),
					'desc'    => __( 'Check this if you would like to load WPForms assets site-wide. Only check if your site is having compatibility issues or instructed to by support.', 'wpforms' ),
					'type'    => 'checkbox',
				),
				'hide-announcements' => array(
					'id'      => 'hide-announcements',
					'name'    => __( 'Hide Announcements', 'wpforms' ),
					'desc'    => __( 'Check this if you would like to hide plugin announcements and update details.', 'wpforms' ),
					'type'    => 'checkbox',
				),
			),
			// Email settings tab.
			'email' => array(
				'email-heading' => array(
					'id'       => 'email-heading',
					'content'  => '<h4>' . __( 'Email', 'wpforms' ) . '</h4>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => array( 'section-heading', 'no-desc' ),
				),
				'email-template' => array(
					'id'        => 'email-template',
					'name'      => __( 'Template', 'wpforms' ),
					'desc'      => __( 'Determines how email notifications will be formatted. HTML Templates are the default.', 'wpforms' ),
					'type'      => 'radio',
					'default'   => 'default',
					'options'   => array(
						'default' => __( 'HTML Template', 'wpforms' ),
						'none'    => __( 'Plain text', 'wpforms' ),
					),
				),
				'email-header-image' => array(
					'id'        => 'email-header-image',
					'name'      => __( 'Header Image', 'wpforms' ),
					'desc'      => __( 'Upload or choose a logo to be displayed at the top of email notifications.<br>Recommended size is 300x100 or smaller for best support on all devices.', 'wpforms' ),
					'type'      => 'image',
				),
				'email-background-color' => array(
					'id'        => 'email-background-color',
					'name'      => __( 'Background Color', 'wpforms' ),
					'desc'      => __( 'Customize the background color of the HTML email template.', 'wpforms' ),
					'type'      => 'color',
					'default'   => '#e9eaec',
				),
				'email-carbon-copy' => array(
					'id'        => 'email-carbon-copy',
					'name'      => __( 'Carbon Copy', 'wpforms' ),
					'desc'      => __( 'Check this if you would like to enable the ability to CC: email addresses in the form notification settings.', 'wpforms' ),
					'type'      => 'checkbox',
				),
			),
			// Recaptcha settings tab.
			'recaptcha' => array(
				'recaptcha-heading' => array(
					'id'       => 'recaptcha-heading',
					'content'  => '<h4>' . __( 'reCAPTCHA', 'wpforms' ) . '</h4>' . $recaptcha_desc,
					'type'     => 'content',
					'no_label' => true,
					'class'    => array( 'section-heading' ),
				),
				'recaptcha-type' => array(
					'id'        => 'recaptcha-type',
					'name'      => __( 'Type', 'wpforms' ),
					'type'      => 'radio',
					'default'   => 'default',
					'options'   => array(
						'v2'        => __( 'v2 reCAPTCHA', 'wpforms' ),
						'invisible' => __( 'Invisible reCAPTCHA', 'wpforms' ),
					),
				),
				'recaptcha-site-key' => array(
					'id'        => 'recaptcha-site-key',
					'name'      => __( 'Site Key', 'wpforms' ),
					'type'      => 'text',
				),
				'recaptcha-secret-key' => array(
					'id'        => 'recaptcha-secret-key',
					'name'      => __( 'Secret Key', 'wpforms' ),
					'type'      => 'text',
				),
			),
			// Validation messages settings tab.
			'validation' => array(
				'validation-heading' => array(
					'id'       => 'validation-heading',
					'content'  => '<h4>' . __( 'Validation Messages', 'wpforms' ) . '</h4><p>' . __( 'These messages are displayed to the user as they fill out a form in real-time.', 'wpforms' ) . '</p>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => array( 'section-heading' ),
				),
				'validation-required' => array(
					'id'        => 'validation-required',
					'name'      => __( 'Required', 'wpforms' ),
					'type'      => 'text',
					'default'   => __( 'This field is required.', 'wpforms' ),
				),
				'validation-url' => array(
					'id'        => 'validation-url',
					'name'      => __( 'Website URL', 'wpforms' ),
					'type'      => 'text',
					'default'   => __( 'Please enter a valid URL.', 'wpforms' ),
				),
				'validation-email' => array(
					'id'        => 'validation-email',
					'name'      => __( 'Email', 'wpforms' ),
					'type'      => 'text',
					'default'   => __( 'Please enter a valid email address.', 'wpforms' ),
				),
				'validation-number' => array(
					'id'        => 'validation-number',
					'name'      => __( 'Number', 'wpforms' ),
					'type'      => 'text',
					'default'   => __( 'Please enter a valid number.', 'wpforms' ),
				),
				'validation-confirm' => array(
					'id'        => 'validation-confirm',
					'name'      => __( 'Confirm Value', 'wpforms' ),
					'type'      => 'text',
					'default'   => __( 'Field values do not match.', 'wpforms' ),
				),
			),
			// Provider integrations settings tab.
			'integrations' => array(
				'integrations-heading' => array(
					'id'       => 'integrations-heading',
					'content'  => '<h4>' . __( 'Integrations', 'wpforms' ) . '</h4><p>' . __( 'Manage integrations with popular providers such as Constant Contact, MailChimp, Zapier, and more.', 'wpforms' ) . '</p>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => array( 'section-heading' ),
				),
				'integrations-providers' => array(
					'id'       => 'integrations-providers',
					'content'  => '<h4>' . __( 'Integrations', 'wpforms' ) . '</h4><p>' . __( 'Manage integrations with popular providers such as Constant Contact, MailChimp, Zapier, and more.', 'wpforms' ) . '</p>',
					'type'     => 'providers',
					'wrap'     => 'none',
				),
			),
		);
		$defaults = apply_filters( 'wpforms_settings_defaults', $defaults );

		return empty( $view ) ? $defaults : $defaults[ $view ];
	}

	/**
	 * Return array containing markup for all the appropriate settings fields.
	 *
	 * @since 1.3.9
	 * @param string $view
	 * @return array
	 */
	public function get_settings_fields( $view = '' ) {

		$fields   = array();
		$settings = $this->get_registered_settings( $view );

		foreach ( $settings as $id => $args ) {

			$fields[ $id ] = wpforms_settings_output_field( $args );
		}

		return apply_filters( 'wpforms_settings_fields', $fields, $view );
	}

	/**
	 * Build the output for the plugin settings page.
	 *
	 * @since 1.0.0
	 */
	public function output() {

		$tabs   = $this->get_tabs();
		$fields = $this->get_settings_fields( $this->view );

		echo '<div id="wpforms-settings" class="wrap wpforms-admin-wrap">';

			$this->tabs();

			echo '<h1 class="wpforms-h1-placeholder"></h1>';

			if ( wpforms()->pro && class_exists( 'WPForms_License' ) ) {
				wpforms()->license->notices( true );
			}

			echo '<div class="wpforms-admin-content wpforms-admin-settings">';

			// Some tabs rely on AJAX and do not contain a form, such as Integrations.
			if ( ! empty( $tabs[ $this->view ]['form'] ) ) {
				echo '<form class="wpforms-admin-settings-form" method="post">';
				echo '<input type="hidden" name="action" value="update-settings">';
				echo '<input type="hidden" name="view" value="' . $this->view . '">';
				echo '<input type="hidden" name="nonce" value="' . wp_create_nonce( 'wpforms-settings-nonce' ) . '">';
			}

			do_action( 'wpforms_admin_settings_before', $this->view, $fields );

			foreach ( $fields as $field ) {
				echo $field;
			}

			if ( ! empty( $tabs[ $this->view ]['submit'] ) ) {
				echo '<p class="submit">';
					echo '<button type="submit" class="wpforms-btn wpforms-btn-md wpforms-btn-orange" name="wpforms-settings-submit">' . $tabs[ $this->view ]['submit'] . '</button>';
				echo '</p>';
			}

			do_action( 'wpforms_admin_settings_after', $this->view, $fields );

			if ( ! empty( $tabs[ $this->view ]['form'] ) ) {
				echo '</form>';
			}

			echo '</div>';

		echo '</div>';
	}

	/**
	 * Let Lite users know a Custom Captcha addon is available.
	 *
	 * @since 1.3.9
	 */
	public function captcha_addon_notice() {

		if ( 'recaptcha' !== $this->view ) {
			return;
		}

		// Only display to Lite users.
		if ( wpforms()->pro ) {
			return;
		}
		?>
		<div class="captcha-addon-notice wpforms-clear">
			<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/settings-captcha-addon.png">
			<h5><?php _e( 'Want to better protect your contact forms from spam?', 'wpforms' ); ?></h5>
			<p>
				<?php _e( 'WPForms custom captcha addon allows you to add custom questions captcha or math questions captcha to your WordPress forms. Since we know spam is a huge problem for contact forms, WPForms goes above and beyond to help you protect your forms.', 'wpforms' ); ?>
				<br><br>
				<a href="<?php echo wpforms_admin_upgrade_link(); ?>" class="wpforms-btn wpforms-btn-md wpforms-btn-orange wpforms-upgrade-modal" target="_blank" rel="noopener noreferrer"><?php _e( 'Click here to Upgrade', 'wpforms' ); ?></a>
			</p>
		</div>
		<?php
	}
}
new WPForms_Settings;
