<?php

class WP_CustomTemplates {

	private $wptcp_meta;
	private $post_ID;

	function __construct() {
		$this->wptcp_meta = 'wptcp_template_name';
		add_action( 'admin_menu', array($this, 'wptcp_admin_menu') );
		add_action( 'admin_init', array($this, 'wptcp_admin_init') );
		add_action( 'admin_init', array($this, 'wptcp_update_options') );
		add_action( 'save_post', array($this, 'wptcp_save_post') );
		add_filter( 'body_class', array($this, 'wptcp_body_class') );
		add_filter( 'single_template', array($this, 'wptcp_load_single_template') );
		register_deactivation_hook( __FILE__, array($this, 'wptcp_uninstall') );
	}

	function wptcp_admin_menu() {
		add_options_page( __( 'Post Type Template', 'elementor-templater' ), __( 'Post Type Template', 'elementor-templater' ), 'manage_options', 'wptcp-settings', array($this, 'wptcp_admin_page') );
	}

	function wptcp_admin_page() {
		require ET_PATH . 'inc/settings.php';
	}

	function wptcp_admin_init() {
		$tcp_options = get_option( 'wptcp_options' );

		if ( isset( $tcp_options['objects'] ) ) {
			$post_types = apply_filters( 'wptcp_post_types', $tcp_options['objects'] );
			foreach ( $post_types as $post_type ) {
				add_meta_box(
					'postparentdiv',
					__( 'Custom Template', 'elementor-templater' ),
					array( $this, 'wptcp_post_template'),
					$post_type,
					'side',
					'high'
				);
			}
		}
	}

	function wptcp_post_template( $post ) {
		$this->post_ID = $post->ID;

		$template_vars   = array();
		$templates       = $this->wptcp_get_post_templates();
		$custom_template = $this->wptcp_get_custom_post_template();

		if ( $templates ) { ?>
			<label class="hidden" for="page_template"><?php _e( 'Choose Template', 'elementor-templater' ); ?></label>
			<input type="hidden" name="tcp_current_template" value="1" />
			<select name="custom_post_template" id="custom_post_template">
				<option 
					value='default'
					<?php
					if ( ! $custom_template ) {
						echo "selected='selected'";
					}
					?>
					><?php _e( 'Default Template', 'elementor-templater' ); ?></option>
				<?php foreach ( $templates as $filename => $name ) { ?>
					<option 
						value='<?php echo $filename; ?>'
						<?php
						if ( $custom_template == $filename ) {
							echo "selected='selected'";
						}
						?>
						><?php echo $name; ?></option>
				<?php } ?>
			</select>
	<?php
		} else {
			echo '<p>No Custom Templates Found!</p>';
		}
	}

	function wptcp_get_post_templates() {
		$theme          = wp_get_theme();
		$post_templates = array();
		$files          = (array) $theme->get_files( 'php', 1 );
		foreach ( $files as $file => $full_path ) {
			$headers = @get_file_data( $full_path, array( 'Post Template Name' => 'Post Template Name' ) );
			if ( empty( $headers['Post Template Name'] ) ) {
				continue;
			}
			$post_templates[ $file ] = $headers['Post Template Name'];
		}
		return $post_templates;
	}

	function wptcp_get_custom_post_template() {
		$custom_template = get_post_meta( $this->post_ID, $this->wptcp_meta, true );
		return $custom_template;
	}

	function wptcp_set_custom_post_template( $template ) {
		delete_post_meta( $this->post_ID, $this->wptcp_meta );
		if ( ! $template || $template == 'default' ) {
			return;
		}
		add_post_meta( $this->post_ID, $this->wptcp_meta, $template );
	}

	function wptcp_save_post( $post_ID ) {
		$action_needed = (bool) @ $_POST['tcp_current_template'];
		if ( ! $action_needed ) {
			return;
		}

		$this->post_ID = $post_ID;
		$template      = (string) @ $_POST['custom_post_template'];
		$template      = stripslashes_deep( $template );
		$this->wptcp_set_custom_post_template( $template );
	}

	function wptcp_load_single_template( $template ) {
		global $wp_query;
		$this->post_ID = $wp_query->post->ID;
		$template_file = $this->wptcp_get_custom_post_template();

		if ( ! $template_file ) {
			return $template;
		}

		if ( file_exists( trailingslashit( STYLESHEETPATH ) . $template_file ) ) {
			return STYLESHEETPATH . DIRECTORY_SEPARATOR . $template_file;
		} elseif ( file_exists( TEMPLATEPATH . DIRECTORY_SEPARATOR . $template_file ) ) {
			return TEMPLATEPATH . DIRECTORY_SEPARATOR . $template_file;
		}

		return $template;
	}

	function wptcp_body_class( $classes ) {
		if ( ! is_single() ) {
			return $classes;
		}
		$post_template = get_post_meta( get_the_ID(), 'wptcp_template_name', true );
		if ( ! empty( $post_template ) ) {
			$classes[] = 'page-template';
			$classes[] = 'page-template-' . str_replace( '.php', '-php', $post_template );
		}
		return $classes;
	}

	function wptcp_update_options() {
		global $wpdb;
		if ( ! isset( $_POST['wptcp_submit'] ) ) {
			return false;
		}

		check_admin_referer( 'nonce_wptcp' );
		$input_options            = array();
		$input_options['objects'] = isset( $_POST['objects'] ) ? $_POST['objects'] : '';
		update_option( 'wptcp_options', $input_options );
		wp_redirect( 'options-general.php?page=wptcp-settings&msg=update' );
	}

	function wptcp_uninstall() {
		delete_option( 'wptcp_options' );
	}
}

/**
 * Instantiate the plugin
 *
 * @global
 */
$tcp_obj = new WP_CustomTemplates();
?>
