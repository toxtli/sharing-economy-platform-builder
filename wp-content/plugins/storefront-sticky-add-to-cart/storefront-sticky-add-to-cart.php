<?php
/**
 * Plugin Name:			Storefront Sticky Add to Cart
 * Plugin URI:			https://wordpress.org/plugins/storefront-sticky-add-to-cart/
 * Description:			Adds a sticky add-to-cart bar in single product pages that is revealed as the user scrolls down the page.
 * Version:				1.1.8
 * Author:				WooThemes
 * Author URI:			http://woothemes.com/
 * Requires at least:	4.0.0
 * Tested up to:		4.8.2
 *
 * Text Domain: storefront-sticky-add-to-cart
 * Domain Path: /languages/
 *
 * @package Storefront_Sticky_Add_to_Cart
 * @category Core
 * @author James Koster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Returns the main instance of Storefront_Sticky_Add_to_Cart to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Sticky_Add_to_Cart
 */
function storefront_sticky_add_to_cart() {
	return Storefront_Sticky_Add_to_Cart::instance();
} // End Storefront_Sticky_Add_to_Cart()

storefront_sticky_add_to_cart();

/**
 * Main Storefront_Sticky_Add_to_Cart Class
 *
 * @class Storefront_Sticky_Add_to_Cart
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Sticky_Add_to_Cart
 */
final class Storefront_Sticky_Add_to_Cart {
	/**
	 * Storefront_Sticky_Add_to_Cart The single instance of Storefront_Sticky_Add_to_Cart.
	 *
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The admin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'storefront-sticky-add-to-cart';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.1.8';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'ssatc_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'ssatc_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'ssatc_plugin_links' ) );
	}

	/**
	 * Main Storefront_Sticky_Add_to_Cart Instance
	 *
	 * Ensures only one instance of Storefront_Sticky_Add_to_Cart is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Sticky_Add_to_Cart()
	 * @return Main Storefront_Sticky_Add_to_Cart instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function ssatc_load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-sticky-add-to-cart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links
	 *
	 * @param array $links the plugin action links.
	 * @since  1.0.0
	 */
	public function ssatc_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="https://wordpress.org/support/plugin/storefront-sticky-add-to-cart">' . esc_attr__( 'Support', 'storefront-sticky-add-to-cart' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 *
	 * @return void
	 */
	public function ssatc_setup() {
		add_action( 'wp_enqueue_scripts', array( $this, 'ssatc_script' ), 999 );
		add_action( 'wp', array( $this, 'ssatc_load_add_to_cart_bar' ), 999 );
	}

	/**
	 * Enqueue CSS and custom styles.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ssatc_script() {
		$theme = wp_get_theme();
		wp_enqueue_style( 'ssatc-styles', plugins_url( '/assets/css/style.css', __FILE__ ), '', get_option( 'storefront-sticky-add-to-cart-version' ) );
		wp_register_script( 'waypoints', plugins_url( '/assets/js/jquery.waypoints.min.js', __FILE__ ), array( 'jquery' ), '4.0.0' );
		wp_register_script( 'waypoints-init', plugins_url( '/assets/js/waypoints.init.min.js', __FILE__ ), array( 'jquery' ) );
		wp_register_script( 'ssatc-variable', plugins_url( '/assets/js/variable.min.js', __FILE__ ), array( 'jquery' ) );

		// If Storefront is the active parent theme, add some styles.
		if ( 'Storefront' === $theme->name || 'storefront' === $theme->template ) {

			$bg_color     = storefront_get_content_background_color();
			$accent_color = get_theme_mod( 'storefront_accent_color' );
			$text_color   = get_theme_mod( 'storefront_text_color' );

			$ssatc_style = '
			.ssatc-sticky-add-to-cart {
				background-color: ' . $bg_color . ';
				color: ' . $text_color . ';
			}

			.ssatc-sticky-add-to-cart a:not(.button) {
				color: ' . $accent_color . ';
			}';

			wp_add_inline_style( 'ssatc-styles', $ssatc_style );
		}
	}

	/**
	 * Hooks the add to cart bar into DOM
	 */
	public function ssatc_load_add_to_cart_bar() {
		add_action( 'storefront_after_footer', array( $this, 'ssatc_add_to_cart_bar' ), 999 );
	}

	/**
	 * Display the current product image
	 *
	 * @return void
	 */
	public function ssatc_product_image() {
		global $post;

		if ( has_post_thumbnail() ) {

			$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_catalog' ), array(
				'title'	=> $image_title,
				'alt'	=> $image_title,
			) );

			echo wp_kses_post( apply_filters( 'woocommerce_single_product_image_html', sprintf( '%s', $image ), $post->ID ) );

		} else {

			echo wp_kses_post( apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'storefront-sticky-add-to-cart' ) ), $post->ID ) );

		}
	}

	/**
	 * Display the add to cart bar
	 *
	 * @return void
	 */
	function ssatc_add_to_cart_bar() {
		global $product;

		// Only execute if WooCommerce is installed.
		if ( class_exists( 'WooCommerce' ) ) {

			// And if we're on a product page.
			if ( is_product() ) {
				$availability      = $product->get_availability();
				$ssatc             = new Storefront_Sticky_Add_to_Cart();
				$availability_html = empty( $availability['availability'] ) ? '' : '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . wp_kses_post( $availability['availability'] ) . '</span>';

				// And if the product isn't variable or grouped.
				wp_enqueue_script( 'waypoints' );
				wp_enqueue_script( 'waypoints-init' );

					?>
						<section class="ssatc-sticky-add-to-cart animated">
							<div class="col-full">
								<?php
								if ( version_compare( WC_VERSION, '2.7.0', '<' ) ) {
									$rating_html = $product->get_rating_html();
								} else {
									$rating_html = wc_get_rating_html( $product->get_average_rating() );
								}

								$ssatc->ssatc_product_image();
								echo '<div class="ssatc-content">';
								echo esc_attr__( 'You\'re viewing:', 'storefront-sticky-add-to-cart' ) . ' <strong>' . get_the_title() . '</strong><br />';
								echo '<span class="price">' . wp_kses_post( $product->get_price_html() ) . '</span> ' . wp_kses_post( $rating_html );
								echo wp_kses_post( apply_filters( 'woocommerce_stock_html', $availability_html, array_key_exists( 'availability', $availability ) ? $availability['availability'] : '', $product ) );

								ob_start();

								if ( $product->is_type( 'simple' ) ) {
									echo '<a href="' . esc_url( $product->add_to_cart_url() ) . '" class="button alt">' . esc_attr( $product->single_add_to_cart_text() ) . '</a>';
								} else {
									echo '<a class="button alt variable">' . esc_attr__( 'Select options', 'storefront-sticky-add-to-cart' ) . '</a>';
									wp_enqueue_script( 'ssatc-variable' );
								}

								if ( class_exists( 'WC_Catalog_Restrictions' ) ) {
									if ( ! WC_Catalog_Restrictions_Filters::instance()->user_can_purchase( $product ) ) {
										ob_end_clean();
										global $wc_cvo;
										$html = apply_filters( 'catalog_visibility_alternate_add_to_cart_button', do_shortcode( wpautop( wptexturize( $wc_cvo->setting( 'wc_cvo_s_price_text' ) ) ) ) );

										echo $html;
									}
								}

								ob_end_flush();

								echo '</div>';
								?>
							</div>
						</section>
					<?php
			}
		}
	}
} // End Class
