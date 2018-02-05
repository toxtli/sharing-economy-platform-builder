<?php
namespace ElementorMenus;

use Elementor\Utils;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; } // Exit if accessed directly

/**
 * Main class plugin
 */
class Plugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_modules_manager;

	/**
	 * @var array
	 */
	private $_localize_settings = [];

	/**
	 * @return string
	 */
	public function get_version() {
		return ELEMENTOR_MENUS_VERSION;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'navmenu-addon-for-elementor' ), '1.0.5' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'navmenu-addon-for-elementor' ), '1.0.5' );
	}

	/**
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function _includes() {
		require ELEMENTOR_MENUS_PATH . 'includes/modules-manager.php';

		// if ( is_admin() ) {
			// require ELEMENTOR_MENUS_PATH . 'includes/admin.php';
		// }
	}

	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class
			)
		);
		$filename = ELEMENTOR_MENUS_PATH . $filename . '.php';

		if ( is_readable( $filename ) ) {
			include( $filename );
		}
	}

	public function get_localize_settings() {
		return $this->_localize_settings;
	}

	public function add_localize_settings( $setting_key, $setting_value = null ) {
		if ( is_array( $setting_key ) ) {
			$this->_localize_settings = array_replace_recursive( $this->_localize_settings, $setting_key );

			return;
		}

		if ( ! is_array( $setting_value ) || ! isset( $this->_localize_settings[ $setting_key ] ) || ! is_array( $this->_localize_settings[ $setting_key ] ) ) {
			$this->_localize_settings[ $setting_key ] = $setting_value;

			return;
		}

		$this->_localize_settings[ $setting_key ] = array_replace_recursive( $this->_localize_settings[ $setting_key ], $setting_value );
	}

	public function enqueue_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$direction_suffix = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'elementor-menus',
			ELEMENTOR_MENUS_URL . 'assets/css/frontend' . $direction_suffix . $suffix . '.css',
			[],
			ELEMENTOR_MENUS_VERSION
		);

		if ( is_admin() ) {
			wp_enqueue_style(
				'elementor-menus-admin',
				ELEMENTOR_MENUS_URL . 'assets/css/admin' . $direction_suffix . $suffix . '.css',
				[],
				ELEMENTOR_MENUS_VERSION
			);
		}
	}

	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'elementor-menus-modernizer',
			ELEMENTOR_MENUS_URL . 'assets/js/modernizr.custom.js',
			[
				'jquery',
			],
			ELEMENTOR_MENUS_VERSION,
			false
		);

		wp_enqueue_script(
			'elementor-menus-frontend',
			ELEMENTOR_MENUS_URL . 'assets/js/frontend' . $suffix . '.js',
			[
				'jquery',
			],
			ELEMENTOR_MENUS_VERSION,
			true
		);

		wp_localize_script(
			'elementor-menus-frontend',
			'ElementorMenusFrontendConfig',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'elementor-menus-frontend' ),
			]
		);
	}

	public function enqueue_panel_scripts() {
		// $suffix = Utils::is_script_debug() ? '' : '.min';
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'elementor-menus-modernizer',
			ELEMENTOR_MENUS_URL . 'assets/js/modernizr.custom.js',
			[
				// 'jquery',
			],
			ELEMENTOR_MENUS_VERSION,
			false
		);

		wp_enqueue_script(
			'elementor-menus',
			ELEMENTOR_MENUS_URL . 'assets/js/editor' . $suffix . '.js',
			[
				'jquery',
				'backbone-marionette',
			],
			ELEMENTOR_MENUS_VERSION,
			true
		);

		wp_localize_script(
			'elementor-menus',
			'ElementorMenusConfig',
			apply_filters( 'elementor_menus/editor/localize_settings', [] ),
			[
				'expand'   => __( 'expand child menu', 'navmenu-addon-for-elementor' ),
				'collapse' => __( 'collapse child menu', 'navmenu-addon-for-elementor' ),
			]
		);
	}

	public function enqueue_panel_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style(
			'elementor-menus-editor',
			ELEMENTOR_MENUS_URL . 'assets/css/editor' . $suffix . '.css',
			[
				'elementor-editor',
			],
			ELEMENTOR_MENUS_VERSION
		);
	}

	public function enqueue_default_scripts() {
		wp_localize_script(
			'elementor-menus-frontend', 'elementorScreenReaderText', array(
				'expand'   => __( 'expand child menu', 'navmenu-addon-for-elementor' ),
				'collapse' => __( 'collapse child menu', 'navmenu-addon-for-elementor' ),
			)
		);
	}

	public function enqueue_default_secondary_scripts() {
		wp_localize_script(
			'elementor-menus-frontend', 'elementorSecondaryScreenReaderText', array(
				'expand'   => __( 'expand child menu', 'navmenu-addon-for-elementor' ),
				'collapse' => __( 'collapse child menu', 'navmenu-addon-for-elementor' ),
			)
		);
	}

	public function enqueue_panel_default_scripts() {
		wp_localize_script(
			'elementor-menus', 'elementorScreenReaderText', array(
				'expand'   => __( 'expand child menu', 'navmenu-addon-for-elementor' ),
				'collapse' => __( 'collapse child menu', 'navmenu-addon-for-elementor' ),
			)
		);
	}

	public function elementor_init() {
		$this->_modules_manager = new Manager();

		// Add element category in panel
		\Elementor\Plugin::instance()->elements_manager->add_category(
			'branding-elements',
			[
				'title' => __( 'Header Elements', 'navmenu-addon-for-elementor' ),
				'icon'  => 'font',
			],
			1
		);
	}

	protected function add_actions() {
		add_action( 'elementor/init', [ $this, 'elementor_init' ] );

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_panel_styles' ], 997 );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_panel_scripts' ], 997 );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_panel_default_scripts' ], 997 );

		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_scripts' ], 999 );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_default_scripts' ], 999 );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_default_secondary_scripts' ], 999 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 998 );
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->_includes();

		$this->add_actions();

		if ( is_admin() ) {
			// new Admin();
			// new License\Admin();
		}
	}
}

if ( ! defined( 'ELEMENTOR_MENUS_TESTS' ) ) {
	// In tests we run the instance manually.
	Plugin::instance();
}
