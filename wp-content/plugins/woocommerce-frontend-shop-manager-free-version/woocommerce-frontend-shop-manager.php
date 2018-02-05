<?php
/*
Plugin Name: WooCommerce Frontend Shop Manager - Free Version
Plugin URI: http://www.mihajlovicnenad.com/woocommerce-frontend-shop-manager
Description:  WooCommerce Frontend Shop Manager! The ultimate tool for managing WooCommerce shops, right at the frontend, featuring live product editing and vendor support! For WooCommerce Frontend Shop Manager Premuim visit mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 1.0.3
Author URI: http://www.mihajlovicnenad.com
*/

add_action( 'plugins_loaded', array( 'WC_Frontnend_Shop_Manager_Free', 'init' ));

class WC_Frontnend_Shop_Manager_Free {

	public static $path;
	public static $url_path;
	public static $settings;

	public static function init() {
		$class = __CLASS__;
		new $class;
	}

	function __construct() {

		if ( !class_exists('Woocommerce') || !current_user_can('edit_products') ) {
			return;
		}

		self::$path = plugin_dir_path( __FILE__ );
		self::$url_path = plugins_url( __FILE__ );

		self::$settings['user'] = wp_get_current_user();
		self::$settings['woocommerce']['decimal_sep'] = get_option( 'woocommerce_price_decimal_sep' );

		if ( self::$settings['user']->has_cap( 'administrator' ) || self::$settings['user']->has_cap( 'manage_woocommerce' ) ) {
			self::$settings['admin_mode'] = true;
		}

		add_action( 'init', array(&$this, 'wfsm_textdomain') );
		add_action( 'wp_enqueue_scripts', array(&$this, 'wfsm_scripts') );

		add_action( 'woocommerce_before_shop_loop_item', array(&$this, 'wfsm_content') );
		add_action( 'woocommerce_before_single_product_summary', array(&$this, 'wfsm_content'), 5 );
		add_action( 'wp_ajax_wfsm_respond', array(&$this, 'wfsm_respond') );
		add_action( 'wp_ajax_wfsm_save', array(&$this, 'wfsm_save') );

	}

	public static function wfsm_get_path() {
		return plugin_dir_path( __FILE__ );
	}

	function wfsm_textdomain() {
		$dir = trailingslashit( WP_LANG_DIR );
		load_plugin_textdomain( 'wfsm', false, $dir . 'plugins' );
	}

	function wfsm_scripts() {
		wp_enqueue_style( 'wfsm-selectize', plugins_url( 'assets/css/selectize.default.css', __FILE__) );
		wp_enqueue_style( 'wfsm-style', plugins_url( 'assets/css/styles.css', __FILE__) );

		wp_register_script( 'wfsm-selectize', plugins_url( 'assets/js/selectize.min.js', __FILE__), array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'wfsm-scripts', plugins_url( 'assets/js/scripts.js', __FILE__), array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'wfsm-init', plugins_url( 'assets/js/scripts-init.js', __FILE__), array( 'jquery' ), '1.0.0', false );
		wp_enqueue_media();
		wp_enqueue_script( array( 'wfsm-init', 'jquery-ui-datepicker', 'wfsm-selectize', 'wfsm-scripts' ) );
		$curr_args = array(
			'ajax' => admin_url( 'admin-ajax.php' )
		);

		wp_localize_script( 'wfsm-scripts', 'wfsm', $curr_args );
	}


	function wfsm_content() {

		global $post, $woocommerce_loop;

		$curr_id = $post->ID;

		if ( !isset( self::$settings['admin_mode'] ) && absint( $post->post_author ) !== self::$settings['user']->ID ) {
			return;
		}

		$add_loop = ( ( isset( $woocommerce_loop['columns'] ) && empty( $woocommerce_loop['columns'] ) ) || !isset( $woocommerce_loop['columns'] ) || !isset( $woocommerce_loop['loop'] ) ? 'single' : $woocommerce_loop['loop'] . '|' . $woocommerce_loop['columns'] );

	?>
		<div class="wfsm-buttons" data-id="<?php echo $curr_id; ?>" data-loop="<?php echo $add_loop; ?>">
			<a href="#" class="wfsm-button wfsm-activate" title="<?php _e( 'Quick edit product', 'wfsm' ); ?>"><i class="wfsmico-activate"></i></a>
			<a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $curr_id ) . '&action=edit' ) ); ?>" class="wfsm-button wfsm-edit" title="<?php _e( 'Edit product in the backend', 'wfsm' ); ?>"><i class="wfsmico-edit"></i></a>
			<a href="#" class="wfsm-button wfsm-save" title="<?php _e( 'Save changes', 'wfsm' ); ?>"><i class="wfsmico-save"></i></a>
			<a href="#" class="wfsm-button wfsm-discard" title="<?php _e( 'Discard changes', 'wfsm' ); ?>"><i class="wfsmico-discard"></i></a>
			<span class="wfsm-editing">
				<img width="64" height="64" src="<?php echo plugins_url( 'assets/images/editing.png', __FILE__ ); ?>" />
				<small>
					<?php _e( 'Currently Editing', 'wfsm' ) ; ?><br/>
					<?php _e( 'Tap to Save', 'wfsm' ) ; ?>
				</small>
			</span>
		</div>
	<?php

	}

	function wfsm_respond() {

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) === false ) {
			die( 'Error!' );
			exit;
		}

		if ( isset($_POST) && isset( $_POST['wfsm_id'] ) ) {
			$curr_post_id = absint( stripslashes( $_POST['wfsm_id'] ) );
			if ( get_post_status( $curr_post_id ) === false ) {
				die( 'Error!' );
				exit;
			}
		}
		else {
			die( 'Error!' );
			exit;
		}

		$curr_post_author = self::wfsm_check_premissions( $curr_post_id );

		if ( $curr_post_author === false ) {
			die( 'Error!' );
			exit;
		}

		$product = wc_get_product( $curr_post_id );

		if ( $product->is_type( 'simple' ) ) {
			$product_type_class = ' wfsm-simple-product';
			$product_information = __( 'Simple Product', 'wfsm' ) . '<br/> #ID ' . $curr_post_id;
		}
		else if ( $product->is_type( 'variable' ) ) {
			$product_type_class = ' wfsm-variable-product';
			$product_information = __( 'Variable Product', 'wfsm' ) . '<br/> #ID ' . $curr_post_id;
		}
		else if ( $product->is_type( 'external' ) ) {
			$product_type_class = ' wfsm-external-product';
			$product_information = __( 'External Product', 'wfsm' ) . '<br/> #ID ' . $curr_post_id;
		}
		else if ( $product->is_type( 'grouped' ) ) {
			$product_type_class = ' wfsm-grouped-product';
			$product_information = __( 'Grouped Product', 'wfsm' ) . '<br/> #ID ' . $curr_post_id;
		}
		else {
			$product_type_class = ' wfsm-' . $product->product_type() . '-product';
			$product_information = __( 'Product', 'wfsm' ) . ' #ID ' . $curr_post_id;
		}

		ob_start();
	?>
		<div class="wfsm-quick-editor">
			<div class="wfsm-screen<?php echo $product_type_class; ?>">
				<div class="wfsm-controls">
					<div class="wfsm-about">
						<img width="49" height="28" src="<?php echo plugins_url( 'assets/images/about.png', __FILE__ ); ?>" />
						<em>WFSM</em>
						<small><?php echo __( 'Editing', 'wfsm' ) . ': ' . $product_information; ?></small>
					</div>
					<span class="wfsm-expand"><i class="wfsmico-expand"></i></span>
					<span class="wfsm-contract"><i class="wfsmico-contract"></i></span>
					<span class="wfsm-side-edit"><i class="wfsmico-edit"></i></span>
					<span class="wfsm-side-save"><i class="wfsmico-save"></i></span>
					<span class="wfsm-side-discard"><i class="wfsmico-discard"></i></span>
					<div class="wfsm-clear"></div>
				</div>
				<span class="wfsm-headline"><?php _e( 'Product Data', 'wfsm' ); ?></span>
				<div class="wfsm-group-general">
					<label for="wfsm-featured-image" class="wfsm-featured-image">
						<a href="#" class="wfsm-featured-image-trigger">
						<?php
							if ( has_post_thumbnail( $curr_post_id ) ) {
								$curr_image = wp_get_attachment_image_src( $curr_image_id = get_post_thumbnail_id( $curr_post_id ), 'thumbnail' );
							?>
								<img width="64" height="64" src="<?php echo $curr_image[0]; ?>" />
							<?php
							}
							else {
								$curr_image_id = 0;
							?>
								<img width="64" height="64" src="<?php echo plugins_url( 'assets/images/placeholder.gif', __FILE__ ); ?>" />
						<?php
							}
						?>
						</a>
						<input id="wfsm-featured-image" name="wfsm-featured-image" class="wfsm-collect-data" type="hidden" value="<?php echo $curr_image_id; ?>" />
					</label>
					<div class="wfsm-featured-image-controls">
						<a href="#" class="wfsm-editor-button wfsm-change-image"><?php _e( 'Change Image', 'wfsm' ); ?></a>
						<a href="#" class="wfsm-editor-button wfsm-remove-image"><?php _e( 'Discard Image', 'wfsm' ); ?></a>
					</div>
					<div class="wfsm-clear"></div>
					<label for="wfsm-product-name">
						<span><?php _e( 'Product Name', 'wfsm' ); ?></span>
						<input id="wfsm-product-name" name="wfsm-product-name" class="wfsm-reset-this wfsm-collect-data" type="text" value="<?php echo get_the_title($curr_post_id); ?>"/>
					</label>
				<?php
					if ( !$product->is_type( 'variable' ) && !$product->is_type( 'grouped' ) ) {
				?>
					<label for="wfsm-regular-price" class="wfsm-label-half wfsm-label-first">
						<span><?php _e( 'Regular Price', 'wfsm' ); ?></span>
						<input id="wfsm-regular-price" name="wfsm-regular-price" class="wfsm-reset-this wfsm-collect-data" type="text" value="<?php echo $product->get_regular_price(); ?>"/>
					</label>
					<label for="wfsm-sale-price" class="wfsm-label-half">
						<span><?php _e( 'Sale Price', 'wfsm' ); ?></span>
						<input id="wfsm-sale-price" name="wfsm-sale-price" class="wfsm-reset-this wfsm-collect-data" type="text" value="<?php echo $product->get_sale_price(); ?>"/>
					</label>
					<div class="wfsm-clear"></div>
				<?php
					}
					?>
				</div>
				<span class="wfsm-headline wfsm-headline-taxonomies"><?php _e( 'Product Taxnonomies and Terms', 'wfsm' ); ?></span>
				<div class="wfsm-group-taxonomies">
					<label for="wfsm-select-product_cat" class="wfsm-selectize">
						<span><?php _e( 'Product Categories', 'wfsm' ); ?></span>
						<select id="wfsm-select-product_cat" name="wfsm-select-product_cat" class="wfsm-collect-data" multiple="multiple">
						<?php
							$product_cats = wp_get_post_terms($curr_post_id, 'product_cat', array( 'fields' => 'slugs' ) );
							foreach( get_terms('product_cat','parent=0&hide_empty=0') as $term ) {
								$wfsm_selected = in_array( $term->slug , $product_cats ) ? 'added' : 'notadded' ;
							?>
								<option <?php echo ( $wfsm_selected == 'added' ? ' selected="selected"' : '' ); ?> value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
							<?php
							}
						?>
						</select>
					</label>
				</div>
				<div class="wfsm-clear"></div>
				<script type="text/javascript">
					(function($){
						"use strict";

						$(document).on('click', '.wfsm-quick-editor label.wfsm-featured-image > a.wfsm-featured-image-trigger, .wfsm-change-image', function () {

							if ( $(this).hasClass('wfsm-change-image') ) {
								var el = $(this).parent().prev().find('.wfsm-featured-image-trigger');
							}
							else {
								var el = $(this);
							}

							var curr = el.parent();

							if ( $.isEmptyObject(window.wfsm_frame) == false ) {

								window.wfsm_frame.off('select');

								window.wfsm_frame.on( 'select', function() {

									var attachment = window.wfsm_frame.state().get('selection').first();
									window.wfsm_frame.close();

									curr.find('input:hidden').val(attachment.id);
									if ( attachment.attributes.type == 'image' ) {
										el.html('<img width="64" height="64" src="'+attachment.attributes.sizes.thumbnail.url+'" />');
									}

								});

								window.wfsm_frame.open();

								return false;
							}


							window.wfsm_frame = wp.media({
								title: '<?php echo esc_attr( __('Set Featured Image','wfsm') ); ?>',
								button: {
									text: el.data("update"),
									close: false
								},
								multiple: false,
								default_tab: 'upload',
								tabs: 'upload, library',
								returned_image_size: 'thumbnail'
							});

							window.wfsm_frame.off('select');

							window.wfsm_frame.on( 'select', function() {

								var attachment = window.wfsm_frame.state().get('selection').first();
								window.wfsm_frame.close();

								curr.find('input:hidden').val(attachment.id);
								if ( attachment.attributes.type == 'image' ) {
									el.html('<img width="64" height="64" src="'+attachment.attributes.sizes.thumbnail.url+'" />');
								}

							});

							window.wfsm_frame.open();

							return false;

						});

						$('.wfsm-group-taxonomies .wfsm-selectize select').each( function() {
							var curr = $(this);

							curr.selectize({
								plugins: ['remove_button'],
								delimiter: ',',
								persist: false,
								create: function(input) {
									return {
										value: input,
										text: input
									}
								}
							});
						});
					})(jQuery);
				</script>
				<small><?php echo 'WooCommerce Frontend Shop Manager<br/>Free Version 1.0.0<br/>by <a href="http://mihajlovicnenad.com">mihajlovicnenad.com</a><br/><a href="http://codecanyon.net/user/dzeriho/portfolio?ref=dzeriho">Get more premium plugins at this link!</a><br/><a href="http://codecanyon.net/item/woocommerce-frontend-shop-manager/10694235?ref=dzeriho">Get WooCommerce Frontend Shop Manager <strong>Premium</strong> at this link!!</a>'; ?></small>
			</div>
		</div>
	<?php
		$out = ob_get_clean();

		die($out);
		exit;

	}

	function product_exist( $sku ) {
		global $wpdb;
		$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value= %s LIMIT 1", $sku ) );
		return $product_id;
	}

	function wfsm_save() {

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) === false ) {
			die( 'Error!' );
			exit;
		}

		if ( isset($_POST) && isset( $_POST['wfsm_id'] ) ) {
			$curr_post_id = absint( stripslashes( $_POST['wfsm_id'] ) );
			if ( get_post_status( $curr_post_id ) === false ) {
				die( 'Error!' );
				exit;
			}
		}
		else {
			die( 'Error!' );
			exit;
		}

		$curr_post_author = self::wfsm_check_premissions( $curr_post_id );

		if ( $curr_post_author === false ) {
			die( 'Error!' );
			exit;
		}

		$curr_data = array();
		$curr_data = json_decode( stripslashes( $_POST['wfsm_save'] ), true );

		$curr_post = array(
			'ID' => $curr_post_id,
			'post_title' => $curr_data['wfsm-product-name'],
		);

		wp_update_post( $curr_post );

		if ( isset($curr_data['wfsm-featured-image']) ) {
			$curr_val = intval( stripslashes( $curr_data['wfsm-featured-image'] ) );
			if ( wp_attachment_is_image( $curr_val ) ) {
				update_post_meta( $curr_post_id, '_thumbnail_id', $curr_val );
			}
		}

		if ( isset($curr_data['wfsm-regular-price']) && $curr_data['wfsm-regular-price'] !== '' ) {
			update_post_meta( $curr_post_id, '_regular_price', intval( $curr_data['wfsm-regular-price'] ) );
			if ( intval( $curr_data['wfsm-sale-price'] ) == '' ) {
				update_post_meta( $curr_post_id, '_price', intval( $curr_data['wfsm-regular-price'] ) );
			}
		}
		else if ( isset($curr_data['wfsm-regular-price']) && $curr_data['wfsm-regular-price'] == '' ) {
			update_post_meta( $curr_post_id, '_regular_price', '' );
		}

		if ( isset($curr_data['wfsm-sale-price']) && $curr_data['wfsm-sale-price'] !== '' ) {
			update_post_meta( $curr_post_id, '_sale_price', intval( $curr_data['wfsm-sale-price'] ) );
			update_post_meta( $curr_post_id, '_price', intval( $curr_data['wfsm-sale-price'] ) );
		}
		else if ( isset($curr_data['wfsm-sale-price']) && $curr_data['wfsm-sale-price'] == '' ) {
			update_post_meta( $curr_post_id, '_sale_price', '' );
		}

		if ( isset($curr_data['wfsm-select-product_cat']) && $curr_data['wfsm-select-product_cat'] !== null && is_array( $curr_data['wfsm-select-product_cat'] ) ) {

			$add_terms = array();
			$ready_array = array_map( 'sanitize_text_field', $curr_data['wfsm-select-product_cat'] );

			foreach ( $ready_array as $curr_tax ) {
				$curr_slug = sanitize_title( $curr_tax );
				if ( !get_term_by( 'slug', $curr_slug, 'product_cat' ) ) {
					wp_insert_term( $curr_tax, 'product_cat', array( 'slug' => $curr_tax ) );
				}
				$add_terms[] = $curr_slug;
			}
			wp_set_object_terms( $curr_post_id, $add_terms, 'product_cat' );

		}
		else {
			wp_set_object_terms( $curr_post_id, array(), 'product_cat' );
		}


		$curr_loop = isset( $_POST['wfsm_loop'] ) ? sanitize_text_field( $_POST['wfsm_loop'] ) : 'single';

		if ( $curr_loop !== 'single' ) {

			$wfsm_settings = strpos( $curr_loop, '|' ) ? explode( '|', $curr_loop ) : 'single';
			
			if ( is_array( $wfsm_settings ) ) {

				global $woocommerce_loop;

				$woocommerce_loop = array(
					'loop' => intval( $wfsm_settings[0] ) - 1,
					'columns' => intval( $wfsm_settings[1] )
				);

				$curr_products = new WP_Query( array( 'post_type' => 'product', 'post__in' => array( $curr_post_id ) ) );

				ob_start();

				if ( $curr_products->have_posts() ) {

					while ( $curr_products->have_posts() ) : $curr_products->the_post();

						wc_get_template_part( 'content', 'product' );

					endwhile;

				}

				$out = ob_get_clean();

			}
			else {
				$out = 'single';
			}
		}
		else {
			$out = 'single';
		}

		die($out);
		exit;
	}

	public static function wfsm_check_premissions( $curr_post_id ) {

		$curr_post_author = absint( get_post_field( 'post_author', $curr_post_id ) );

		$curr_logged_user = get_current_user_id();

		$curr_user = get_user_by( 'id', $curr_logged_user );

		if ( $curr_user->has_cap( 'administrator' ) || $curr_user->has_cap( 'manage_woocommerce' ) ) {
			$curr_admin = true;
		}

		if ( !isset( $curr_admin ) && absint( $curr_post_author ) !== $curr_logged_user ) {
			return false;
		}
		else {
			return $curr_post_author;
		}

	}

}

?>