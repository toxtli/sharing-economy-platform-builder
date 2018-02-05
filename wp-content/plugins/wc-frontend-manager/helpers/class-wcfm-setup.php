<?php
/**
 * WCFM Dashboard Setup Class
 * 
 * @since 3.1.3
 * @package wcfm/helpers
 * @author WC Lovers
 */
if (!defined('ABSPATH')) {
    exit;
}

class WCFM_Dashboard_Setup {

	/** @var string Currenct Step */
	private $step = '';

	/** @var array Steps for the setup wizard */
	private $steps = array();

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wcfm_admin_menus' ) );
		add_action( 'admin_init', array( $this, 'wcfm_dashboard_setup' ) );
	}

	/**
	 * Add admin menus/screens.
	 */
	public function wcfm_admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'wcfm-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function wcfm_dashboard_setup() {
		global $WCFM;
		if ( filter_input(INPUT_GET, 'page') != 'wcfm-setup') {
			return;
		}

		if (!WCFM_Dependencies::woocommerce_plugin_active_check()) {
			if (isset($_POST['submit'])) {
				$this->install_woocommerce();
			}
			$this->install_woocommerce_view();
			exit();
		}
		$default_steps = array(
				'introduction' => array(
					'name' => __('Introduction', 'wc-frontend-manager' ),
					'view' => array($this, 'wcfm_setup_introduction'),
					'handler' => '',
				),
				'dashboard' => array(
					'name' => __('Dashboard Setup', 'wc-frontend-manager'),
					'view' => array($this, 'wcfm_setup_dashboard'),
					'handler' => array($this, 'wcfm_setup_dashboard_save')
				),
				'style' => array(
					'name' => __('Style', 'wc-frontend-manager'),
					'view' => array($this, 'wcfm_setup_style'),
					'handler' => array($this, 'wcfm_setup_style_save')
				),
				'capability' => array(
					'name' => __('Capability', 'wc-frontend-manager'),
					'view' => array($this, 'wcfm_setup_capability'),
					'handler' => array($this, 'wcfm_setup_capability_save')
				),
				'next_steps' => array(
					'name' => __('Ready!', 'wc-frontend-manager'),
					'view' => array($this, 'wcfm_setup_ready'),
					'handler' => '',
				),
		);
		$is_marketplace = wcfm_is_marketplace();
		if( !$is_marketplace ) unset( $default_steps['capability'] );
		$this->steps = apply_filters('wcfm_dashboard_setup_steps', $default_steps);
		$current_step = filter_input(INPUT_GET, 'step');
		$this->step = $current_step ? sanitize_key($current_step) : current(array_keys($this->steps));
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script('jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array('jquery'), '2.70', true);
		wp_register_script('select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array('jquery'), '4.0.3');
		wp_register_script('wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array('jquery', 'select2'), WC_VERSION);
		wp_localize_script('wc-enhanced-select', 'wc_enhanced_select_params', array(
				'i18n_no_matches' => _x('No matches found', 'enhanced select', 'wc-frontend-manager'),
				'i18n_ajax_error' => _x('Loading failed', 'enhanced select', 'wc-frontend-manager'),
				'i18n_input_too_short_1' => _x('Please enter 1 or more characters', 'enhanced select', 'wc-frontend-manager'),
				'i18n_input_too_short_n' => _x('Please enter %qty% or more characters', 'enhanced select', 'wc-frontend-manager'),
				'i18n_input_too_long_1' => _x('Please delete 1 character', 'enhanced select', 'wc-frontend-manager'),
				'i18n_input_too_long_n' => _x('Please delete %qty% characters', 'enhanced select', 'wc-frontend-manager'),
				'i18n_selection_too_long_1' => _x('You can only select 1 item', 'enhanced select', 'wc-frontend-manager'),
				'i18n_selection_too_long_n' => _x('You can only select %qty% items', 'enhanced select', 'wc-frontend-manager'),
				'i18n_load_more' => _x('Loading more results&hellip;', 'enhanced select', 'wc-frontend-manager'),
				'i18n_searching' => _x('Searching&hellip;', 'enhanced select', 'wc-frontend-manager'),
				'ajax_url' => admin_url('admin-ajax.php'),
				'search_products_nonce' => wp_create_nonce('search-products'),
				'search_customers_nonce' => wp_create_nonce('search-customers'),
		));

		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION);
		wp_enqueue_style( 'wc-setup', WC()->plugin_url() . '/assets/css/wc-setup.css', array('dashicons', 'install'), WC_VERSION);
		wp_enqueue_style( 'wcfm-setup', $WCFM->plugin_url . '/assets/css/setup/wcfm-style-dashboard-setup.css', array('wc-setup'), $WCFM->version );
		wp_register_script('wc-setup', WC()->plugin_url() . '/assets/js/admin/wc-setup' . $suffix . '.js', array('jquery', 'wc-enhanced-select', 'jquery-blockui'), WC_VERSION);
		wp_localize_script('wc-setup', 'wc_setup_params', array(
				'locale_info' => json_encode(include( WC()->plugin_path() . '/i18n/locale-info.php' )),
		));
		
		// Color Picker
		wp_enqueue_style( 'wp-color-picker' );
    wp_register_script( 'colorpicker_init', $WCFM->plugin_url . 'includes/libs/colorpicker/colorpicker.js', array( 'jquery', 'wp-color-picker' ), $WCFM->version );
		wp_register_script( 'iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch') );
		wp_register_script( 'wp-color-picker', admin_url('js/color-picker.min.js'), array('iris') );
		
		// Checkbox OFF-ON
		$WCFM->library->load_checkbox_offon_lib();
		
		$colorpicker_l10n = array('clear' => __('Clear'), 'defaultString' => __('Default'), 'pick' => __('Select Color'));
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
		
		if (!empty($_POST['save_step']) && isset($this->steps[$this->step]['handler'])) {
				call_user_func($this->steps[$this->step]['handler'], $this);
		}

		ob_start();
		$this->dashboard_setup_header();
		$this->dashboard_setup_steps();
		$this->dashboard_setup_content();
		$this->dashboard_setup_footer();
		exit();
	}

	/**
	 * Content for install woocommerce view
	 */
	public function install_woocommerce_view() {
		global $WCFM;
			?>
			<!DOCTYPE html>
			<html <?php language_attributes(); ?>>
					<head>
							<meta name="viewport" content="width=device-width" />
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
							<title><?php esc_html_e('WCFM &rsaquo; Setup Wizard', 'wc-frontend-manager'); ?></title>
							<?php do_action('admin_print_styles'); ?>
							<?php do_action('admin_head'); ?>
							<style type="text/css">
									body {
											margin: 100px auto 24px;
											box-shadow: none;
											background: #f1f1f1;
											padding: 0;
											max-width: 700px;
									}
									#wc-logo {
											border: 0;
											margin: 0 0 24px;
											padding: 0;
											text-align: center;
									}
									#wc-logo a {
										color: #00897b;
										text-decoration: none;
									}
									
									#wc-logo a span {
										padding-left: 10px;
										padding-top: 23px;
										display: inline-block;
										vertical-align: top;
										font-weight: 700;
									}
									.wcfm-install-woocommerce {
											box-shadow: 0 1px 3px rgba(0,0,0,.13);
											padding: 24px 24px 0;
											margin: 0 0 20px;
											background: #fff;
											overflow: hidden;
											zoom: 1;
									}
									.wcfm-install-woocommerce .button-primary{
											font-size: 1.25em;
											padding: .5em 1em;
											line-height: 1em;
											margin-right: .5em;
											margin-bottom: 2px;
											height: auto;
									}
									.wcfm-install-woocommerce{
											font-family: sans-serif;
											text-align: center;    
									}
									.wcfm-install-woocommerce form .button-primary{
											color: #fff;
											background-color: #00897b;
											font-size: 16px;
											border: 1px solid #00897b;
											width: 230px;
											padding: 10px;
											margin: 25px 0 20px;
											cursor: pointer;
									}
									.wcfm-install-woocommerce form .button-primary:hover{
											background-color: #000000;
									}
									.wcfm-install-woocommerce p{
											line-height: 1.6;
									}

							</style>
					</head>
					<body class="wcfm-setup wp-core-ui">
							<h1 id="wc-logo"><a href="http://wclovers.com/"><img src="<?php echo $WCFM->plugin_url; ?>assets/images/wcfm-transparent.png" alt="WCFM" /><span>WC Frontend Manager</span></a></h1>
							<div class="wcfm-install-woocommerce">
									<p><?php _e('WCFM requires WooCommerce plugin to be active!', 'wc-frontend-manager'); ?></p>
									<form method="post" action="" name="wcfm_install_woocommerce">
											<?php submit_button(__('Install WooCommerce', 'primary', 'wcfm_install_woocommerce')); ?>
											<?php wp_nonce_field('wcfm-install-woocommerce'); ?>
									</form>
							</div>
					</body>
			</html>
			<?php
	}

	/**
	 * Install woocommerce if not exist
	 * @throws Exception
	 */
	public function install_woocommerce() {
			check_admin_referer('wcfm-install-woocommerce');
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
			require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			WP_Filesystem();
			$skin = new Automatic_Upgrader_Skin;
			$upgrader = new WP_Upgrader($skin);
			$installed_plugins = array_map(array(__CLASS__, 'format_plugin_slug'), array_keys(get_plugins()));
			$plugin_slug = 'woocommerce';
			$plugin = $plugin_slug . '/' . $plugin_slug . '.php';
			$installed = false;
			$activate = false;
			// See if the plugin is installed already
			if (in_array($plugin_slug, $installed_plugins)) {
					$installed = true;
					$activate = !is_plugin_active($plugin);
			}
			// Install this thing!
			if (!$installed) {
					// Suppress feedback
					ob_start();

					try {
							$plugin_information = plugins_api('plugin_information', array(
									'slug' => $plugin_slug,
									'fields' => array(
											'short_description' => false,
											'sections' => false,
											'requires' => false,
											'rating' => false,
											'ratings' => false,
											'downloaded' => false,
											'last_updated' => false,
											'added' => false,
											'tags' => false,
											'homepage' => false,
											'donate_link' => false,
											'author_profile' => false,
											'author' => false,
									),
							));

							if (is_wp_error($plugin_information)) {
									throw new Exception($plugin_information->get_error_message());
							}

							$package = $plugin_information->download_link;
							$download = $upgrader->download_package($package);

							if (is_wp_error($download)) {
									throw new Exception($download->get_error_message());
							}

							$working_dir = $upgrader->unpack_package($download, true);

							if (is_wp_error($working_dir)) {
									throw new Exception($working_dir->get_error_message());
							}

							$result = $upgrader->install_package(array(
									'source' => $working_dir,
									'destination' => WP_PLUGIN_DIR,
									'clear_destination' => false,
									'abort_if_destination_exists' => false,
									'clear_working' => true,
									'hook_extra' => array(
											'type' => 'plugin',
											'action' => 'install',
									),
							));

							if (is_wp_error($result)) {
									throw new Exception($result->get_error_message());
							}

							$activate = true;
					} catch (Exception $e) {
							printf(
											__('%1$s could not be installed (%2$s). <a href="%3$s">Please install it manually by clicking here.</a>', 'wc-frontend-manager'), 'WooCommerce', $e->getMessage(), esc_url(admin_url('plugin-install.php?tab=search&s=woocommerce'))
							);
							exit();
					}

					// Discard feedback
					ob_end_clean();
			}

			wp_clean_plugins_cache();
			// Activate this thing
			if ($activate) {
					try {
							$result = activate_plugin($plugin);

							if (is_wp_error($result)) {
									throw new Exception($result->get_error_message());
							}
					} catch (Exception $e) {
							printf(
											__('%1$s was installed but could not be activated. <a href="%2$s">Please activate it manually by clicking here.</a>', 'wc-frontend-manager'), 'WooCommerce', admin_url('plugins.php')
							);
							exit();
					}
			}
			wp_safe_redirect(admin_url('index.php?page=wcfm-setup'));
	}

	/**
	 * Get slug from path
	 * @param  string $key
	 * @return string
	 */
	private static function format_plugin_slug($key) {
			$slug = explode('/', $key);
			$slug = explode('.', end($slug));
			return $slug[0];
	}

	/**
	 * Get the URL for the next step's screen.
	 * @param string step   slug (default: current step)
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 * @since 2.7.7
	 */
	public function get_next_step_link($step = '') {
			if (!$step) {
					$step = $this->step;
			}

			$keys = array_keys($this->steps);
			if (end($keys) === $step) {
					return admin_url();
			}

			$step_index = array_search($step, $keys);
			if (false === $step_index) {
					return '';
			}

			return add_query_arg('step', $keys[$step_index + 1]);
	}

	/**
	 * Setup Wizard Header.
	 */
	public function dashboard_setup_header() {
			global $WCFM;
			?>
			<!DOCTYPE html>
			<html <?php language_attributes(); ?>>
					<head>
							<meta name="viewport" content="width=device-width" />
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
							<title><?php esc_html_e('WCFM &rsaquo; Setup Wizard', 'wc-frontend-manager'); ?></title>
							<?php wp_print_scripts('wc-setup'); ?>
							<?php wp_print_scripts('wcfm-setup'); ?>
							<?php do_action('admin_print_styles'); ?>
							<?php do_action('admin_head'); ?>
							<style type="text/css">
									.wc-setup-steps {
											justify-content: center;
									}
							</style>
					</head>
					<body class="wc-setup wp-core-ui">
							<h1 id="wc-logo"><a href="http://wclovers.com/"><img src="<?php echo $WCFM->plugin_url; ?>assets/images/wcfm-transparent.png" alt="WCFM" /><span>WC Frontend Manager</span></a></h1>
							<?php
					}

	/**
	 * Output the steps.
	 */
	public function dashboard_setup_steps() {
			$ouput_steps = $this->steps;
			array_shift($ouput_steps);
			?>
			<ol class="wc-setup-steps">
					<?php foreach ($ouput_steps as $step_key => $step) : ?>
							<li class="<?php
							if ($step_key === $this->step) {
									echo 'active';
							} elseif (array_search($this->step, array_keys($this->steps)) > array_search($step_key, array_keys($this->steps))) {
									echo 'done';
							}
							?>"><?php echo esc_html($step['name']); ?></li>
			<?php endforeach; ?>
			</ol>
			<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function dashboard_setup_content() {
			echo '<div class="wc-setup-content">';
			call_user_func($this->steps[$this->step]['view'], $this);
			echo '</div>';
	}

	/**
	 * Introduction step.
	 */
	public function wcfm_setup_introduction() {
		?>
		<h1><?php esc_html_e("Let's experience the best ever WC Frontend Dashboard!!", 'wc-frontend-manager'); ?></h1>
		<p><?php _e('Thank you for choosing WCFM! This quick setup wizard will help you to configure the basic settings and you will have your dashboard ready in no time. <strong>It’s completely optional as WCFM already auto-setup.</strong>', 'wc-frontend-manager'); ?></p>
		<p><?php esc_html_e("If you don't want to go through the wizard right now, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!", 'wc-frontend-manager'); ?></p>
		<p class="wc-setup-actions step">
			<a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button-primary button button-large button-next"><?php esc_html_e("Let's go!", 'wc-frontend-manager'); ?></a>
			<a href="<?php echo esc_url(admin_url()); ?>" class="button button-large"><?php esc_html_e('Not right now', 'wc-frontend-manager'); ?></a>
		</p>
		<?php
	}

	/**
	 * Dashboard setup content
	 */
	public function wcfm_setup_dashboard() {
		global $WCFM;
		$wcfm_options = (array) get_option( 'wcfm_options' );
		$is_dashboard_full_view_disabled = isset( $wcfm_options['dashboard_full_view_disabled'] ) ? $wcfm_options['dashboard_full_view_disabled'] : 'no';
		$is_dashboard_theme_header_disabled = isset( $wcfm_options['dashboard_theme_header_disabled'] ) ? $wcfm_options['dashboard_theme_header_disabled'] : 'no';
		$is_slick_menu_disabled = isset( $wcfm_options['slick_menu_disabled'] ) ? $wcfm_options['slick_menu_disabled'] : 'no';
		$is_headpanel_disabled = isset( $wcfm_options['headpanel_disabled'] ) ? $wcfm_options['headpanel_disabled'] : 'no';
		$is_welcome_box_disabled = isset( $wcfm_options['welcome_box_disabled'] ) ? $wcfm_options['welcome_box_disabled'] : 'no';
		$is_checklist_view_disabled = isset( $wcfm_options['checklist_view_disabled'] ) ? $wcfm_options['checklist_view_disabled'] : 'no';
		?>
		<h1><?php esc_html_e('Dashboard setup', 'wc-frontend-manager'); ?></h1>
		<form method="post">
			<table class="form-table">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_style', array(
																																												"dashboard_full_view_disabled" => array('label' => __('WCFM Full View', 'wc-frontend-manager') , 'name' => 'dashboard_full_view_disabled','type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox input-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_dashboard_full_view_disabled),
																																												"dashboard_theme_header_disabled" => array('label' => __('Theme Header', 'wc-frontend-manager') , 'name' => 'dashboard_theme_header_disabled','type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox input-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_dashboard_theme_header_disabled),
																																												"slick_menu_disabled" => array('label' => __('WCFM Slick Menu', 'wc-frontend-manager') , 'name' => 'slick_menu_disabled','type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox input-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_slick_menu_disabled),
																																												"headpanel_disabled" => array('label' => __('WCFM Header Panel', 'wc-frontend-manager') , 'name' => 'headpanel_disabled','type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox input-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_headpanel_disabled),
																																												"welcome_box_disabled" => array('label' => __('Welcome Box', 'wc-frontend-manager') , 'name' => 'welcome_box_disabled','type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox input-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_welcome_box_disabled),
																																												"checklist_view_disabled" => array('label' => __('Category Checklist View', 'wc-frontend-manager') , 'name' => 'checklist_view_disabled','type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox input-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_checklist_view_disabled, 'hints' => __( 'Disable this to have Product Manager Category/Custom Taxonomy Selector - Flat View.', 'wc-frontend-manager' ) ),
																																												) ) );
				?>
			</table>
			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e('Continue', 'wc-frontend-manager'); ?>" name="save_step" />
				<a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-large button-next"><?php esc_html_e('Skip this step', 'wc-frontend-manager'); ?></a>
				<?php wp_nonce_field('wcfm-setup'); ?>
			</p>
		</form>
		<?php
	}
	
	/**
	 * Style setup content
	 */
	public function wcfm_setup_style() {
		global $WCFM;
		wp_print_scripts('wp-color-picker');
		wp_print_scripts('colorpicker_init');
		wp_print_scripts('iris');
		$wcfm_options = (array) get_option( 'wcfm_options' );
	  $color_options = $WCFM->wcfm_color_setting_options();
		?>
		<h1><?php esc_html_e('Dashboard Style', 'wc-frontend-manager'); ?></h1>
		<form method="post">
			<table class="form-table">
				<?php
					$color_options_array = array();
					foreach( $color_options as $color_option_key => $color_option ) {
						$color_options_array[$color_option['name']] = array( 'label' => $color_option['label'] , 'type' => 'colorpicker', 'in_table' => 'yes', 'class' => 'wcfm-text wcfm_ele colorpicker', 'label_class' => 'wcfm_title wcfm_ele', 'value' => ( isset($wcfm_options[$color_option['name']]) ) ? $wcfm_options[$color_option['name']] : $color_option['default'] );
					}
					$WCFM->wcfm_fields->wcfm_generate_form_field( $color_options_array );
				?>
			</table>
			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e('Continue', 'wc-frontend-manager'); ?>" name="save_step" />
				<a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-large button-next"><?php esc_html_e('Skip this step', 'wc-frontend-manager'); ?></a>
				<?php wp_nonce_field('wcfm-setup'); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * capability setup content
	 */
	public function wcfm_setup_capability() {
		global $WCFM;
		$wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );
		
		$submit_products = ( isset( $wcfm_capability_options['submit_products'] ) ) ? $wcfm_capability_options['submit_products'] : 'no';
		$publish_products = ( isset( $wcfm_capability_options['publish_products'] ) ) ? $wcfm_capability_options['publish_products'] : 'no';
		$edit_live_products = ( isset( $wcfm_capability_options['edit_live_products'] ) ) ? $wcfm_capability_options['edit_live_products'] : 'no';
		$delete_products = ( isset( $wcfm_capability_options['delete_products'] ) ) ? $wcfm_capability_options['delete_products'] : 'no';
		
		// Miscellaneous Capabilities
		$manage_booking = ( isset( $wcfm_capability_options['manage_booking'] ) ) ? $wcfm_capability_options['manage_booking'] : 'no';
		$manage_subscription = ( isset( $wcfm_capability_options['manage_subscription'] ) ) ? $wcfm_capability_options['manage_subscription'] : 'no';
		$associate_listings = ( isset( $wcfm_capability_options['associate_listings'] ) ) ? $wcfm_capability_options['associate_listings'] : 'no';
		
		$view_orders  = ( isset( $wcfm_capability_options['view_orders'] ) ) ? $wcfm_capability_options['view_orders'] : 'no';
		$order_status_update  = ( isset( $wcfm_capability_options['order_status_update'] ) ) ? $wcfm_capability_options['order_status_update'] : 'no';
		$view_reports  = ( isset( $wcfm_capability_options['view_reports'] ) ) ? $wcfm_capability_options['view_reports'] : 'no';
		?>
		<h1><?php esc_html_e('Capability', 'wc-frontend-manager'); ?></h1>
		<form method="post">
			<table class="form-table">
			  <?php
			  $WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_products', array("submit_products" => array('label' => __('Submit Products', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $submit_products),
																																																									 "publish_products" => array('label' => __('Publish Products', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $publish_products),
																																																									 "edit_live_products" => array('label' => __('Edit Live Products', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $edit_live_products),
																																																									 "delete_products" => array('label' => __('Delete Products', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $delete_products)
																													) ) );
				
				if( wcfm_is_booking() ) {
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_booking', array(  "manage_booking" => array('label' => __('Manage Bookings', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_booking),
																											) ) );
				}
				
				if( wcfm_is_subscription() ) {
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_subscription', array(  "manage_subscription" => array('label' => __('Manage Subscriptions', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_subscription),
																											) ) );
				}
				
				if( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_listings', array(  "associate_listings" => array('label' => __('Listings', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'desc' => __( 'by WP Job Manager.', 'wc-frontend-manager' ), 'dfvalue' => $associate_listings),
																											) ) );
				}
				
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_orders', array(  "view_orders" => array('label' => __('View Orders', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_orders),
																																																									 "order_status_update" => array('label' => __('Status Update', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $order_status_update),
																										) ) );
				
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_reports', array("view_reports" => array('label' => __('View Reports', 'wc-frontend-manager') , 'type' => 'checkboxoffon', 'in_table' => 'yes', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_reports),
																										 ) ) );
			  ?>
			</table>
			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e('Continue', 'wc-frontend-manager'); ?>" name="save_step" />
				<a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="button button-large button-next"><?php esc_html_e('Skip this step', 'wc-frontend-manager'); ?></a>
				<?php wp_nonce_field('wcfm-setup'); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Ready to go content
	 */
	public function wcfm_setup_ready() {
		global $WCFM;
		?>
		<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo site_url(); ?>" data-text="Hey Guys! Our new e-commerce store is now live and ready to be ransacked! Check it out at" data-via="wcfmlovers" data-size="large">Tweet</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		<h1><?php esc_html_e('We are done!', 'wc-frontend-manager'); ?></h1>
		<div class="woocommerce-message woocommerce-tracker">
				<p><?php esc_html_e("Your front-end dashboard is ready. It's time to experience the things more Easily and Peacefully. Also you will be a bit more relax than ever before, have fun!!", 'wc-frontend-manager') ?></p>
		</div>
		<div class="wc-setup-next-steps">
				<div class="wc-setup-next-steps-first">
						<h2><?php esc_html_e( 'Next steps', 'wc-frontend-manager' ); ?></h2>
						<ul>
								<li class="setup-product"><a class="button button-primary button-large" href="<?php echo esc_url( get_wcfm_url() ); ?>"><?php esc_html_e( "Let's go to Dashboard", 'wc-frontend-manager' ); ?></a></li>
						</ul>
				</div>
				<div class="wc-setup-next-steps-last">
						<h2><?php _e( 'Learn more', 'wc-frontend-manager' ); ?></h2>
						<ul>
								<li class="video-walkthrough"><a target="_blank" href="https://www.youtube.com/channel/UCJ0c60fv3l1K9mBbHdmR-5Q"><?php esc_html_e( 'Watch the tutorial videos', 'wc-frontend-manager' ); ?></a></li>
								<li class="knowledgebase"><a target="_blank" href="https://wclovers.com/blog/woocommerce-frontend-manager/"><?php esc_html_e( 'WCFM - What & Why?', 'wc-frontend-manager' ); ?></a></li>
								<li class="learn-more"><a target="_blank" href="http://wclovers.com/blog/choose-best-woocommerce-multi-vendor-marketplace-plugin/"><?php esc_html_e( 'Choose your multi-vendor plugin', 'wc-frontend-manager' ); ?></a></li>
						</ul>
				</div>
		</div>
		<?php
	}

	/**
	 * Save dashboard settings
	 */
	public function wcfm_setup_dashboard_save() {
		global $WCFM;
		check_admin_referer('wcfm-setup');
		
		$options = get_option( 'wcfm_options' );
		
		$dashboard_full_view_disabled = filter_input(INPUT_POST, 'dashboard_full_view_disabled');
		$dashboard_theme_header_disabled = filter_input(INPUT_POST, 'dashboard_theme_header_disabled');
		$slick_menu_disabled = filter_input(INPUT_POST, 'slick_menu_disabled');
		$headpanel_disabled = filter_input(INPUT_POST, 'headpanel_disabled');
		$checklist_view_disabled = filter_input(INPUT_POST, 'checklist_view_disabled');
		
		// Menu Disabled
		if( !$dashboard_full_view_disabled ) $options['dashboard_full_view_disabled'] = 'no';
		else $options['dashboard_full_view_disabled'] = 'yes';
		
		// Theme Header Disabled
		if( !$dashboard_theme_header_disabled ) $options['dashboard_theme_header_disabled'] = 'no';
		else $options['dashboard_theme_header_disabled'] = 'yes';
		
		// Slick Menu Disabled
		if( !$slick_menu_disabled ) $options['slick_menu_disabled'] = 'no';
		else $options['slick_menu_disabled'] = 'yes';
		
		// Header Panel Disabled
		if( !$headpanel_disabled ) $options['headpanel_disabled'] = 'no';
		else $options['headpanel_disabled'] = 'yes';
		
		// Taxonomy Checklist vew Disabled
		if( !$checklist_view_disabled ) $options['checklist_view_disabled'] = 'no';
		else $options['checklist_view_disabled'] = 'yes';
		
		update_option( 'wcfm_options', $options );
		
		wp_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}
	
	/**
	 * Save dashboard style settings
	 */
	public function wcfm_setup_style_save() {
		global $WCFM;
		check_admin_referer('wcfm-setup');
		
		$options = get_option( 'wcfm_options' );
		
		$dashboard_full_view_disabled = filter_input(INPUT_POST, 'dashboard_full_view_disabled');
		$slick_menu_disabled = filter_input(INPUT_POST, 'slick_menu_disabled');
		$headpanel_disabled = filter_input(INPUT_POST, 'headpanel_disabled');
		$checklist_view_disabled = filter_input(INPUT_POST, 'checklist_view_disabled');
		
		$color_options = $WCFM->wcfm_color_setting_options();
		foreach( $color_options as $color_option_key => $color_option ) {
			$color_value = filter_input( INPUT_POST, $color_option['name'] );
			if( $color_value ) { $options[$color_option['name']] = $color_value; } else { $options[$color_option['name']] = $color_option['default']; }
		}
		
		update_option( 'wcfm_options', $options );
		
		// Init WCFM Custom CSS file
		$wcfm_style_custom = $WCFM->wcfm_create_custom_css();
		
		wp_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}

	/**
	 * save capability settings
	 * @global object $WCFM
	 */
	public function wcfm_setup_capability_save() {
			global $WCFM;
			check_admin_referer('wcfm-setup');

			$wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );
			
			$submit_products = filter_input(INPUT_POST, 'submit_products');
			$publish_products = filter_input(INPUT_POST, 'publish_products');
			$edit_live_products = filter_input(INPUT_POST, 'edit_live_products');
			$delete_products = filter_input(INPUT_POST, 'delete_products');
			
			$manage_booking = filter_input(INPUT_POST, 'manage_booking');
			$manage_subscription = filter_input(INPUT_POST, 'manage_subscription');
			$associate_listings = filter_input(INPUT_POST, 'associate_listings');
			
			$view_orders = filter_input(INPUT_POST, 'view_orders');
			$view_reports = filter_input(INPUT_POST, 'view_reports');
			
			if( !$submit_products ) $wcfm_capability_options['submit_products'] = 'no';
			else $wcfm_capability_options['submit_products'] = 'yes';
			
			if( !$publish_products ) $wcfm_capability_options['publish_products'] = 'no';
			else $wcfm_capability_options['publish_products'] = 'yes';
			
			if( !$edit_live_products ) $wcfm_capability_options['edit_live_products'] = 'no';
			else $wcfm_capability_options['edit_live_products'] = 'yes';
			
			if( !$delete_products ) $wcfm_capability_options['delete_products'] = 'no';
			else $wcfm_capability_options['delete_products'] = 'yes';
			
			if( !$manage_booking ) $wcfm_capability_options['manage_booking'] = 'no';
			else $wcfm_capability_options['manage_booking'] = 'yes';
			
			if( !$manage_subscription ) $wcfm_capability_options['manage_subscription'] = 'no';
			else $wcfm_capability_options['manage_subscription'] = 'yes';
			
			if( !$associate_listings ) $wcfm_capability_options['associate_listings'] = 'no';
			else $wcfm_capability_options['associate_listings'] = 'yes';
			
			if( !$view_orders ) $wcfm_capability_options['view_orders'] = 'no';
			else $wcfm_capability_options['view_orders'] = 'yes';
			
			if( !$view_reports ) $wcfm_capability_options['view_reports'] = 'no';
			else $wcfm_capability_options['view_reports'] = 'yes';
			
			update_option( 'wcfm_capability_options', $wcfm_capability_options );
			
			$WCFM->wcfm_vendor_support->vendors_capability_option_updates();

			wp_redirect(esc_url_raw($this->get_next_step_link()));
			exit;
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function dashboard_setup_footer() {
			if ('next_steps' === $this->step) :
					?>
					<a class="wc-return-to-dashboard" href="<?php echo esc_url(admin_url()); ?>"><?php esc_html_e('Return to the WordPress Dashboard', 'wc-frontend-manager'); ?></a>
	<?php endif; ?>
			</body>
	</html>
	<?php
	}
}

new WCFM_Dashboard_Setup();
