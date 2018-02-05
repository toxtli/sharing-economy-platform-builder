<?php

class EL_CustomTemplates {

	private $elcpt_meta;
	private $post_ID;

	function __construct() {
		$this->elcpt_meta = 'elcpt_template_name';
		add_action( 'admin_menu', array($this, 'elcpt_admin_menu') );
		add_action( 'admin_init', array($this, 'elcpt_admin_init') );
		add_action( 'admin_init', array($this, 'elcpt_update_options') );
		add_action( 'save_post', array($this, 'elcpt_save_post') );
		add_filter( 'body_class', array($this, 'elcpt_body_class') );
		add_filter( 'single_template', array($this, 'elcpt_load_single_template') );
		register_deactivation_hook( __FILE__, array($this, 'elcpt_uninstall') );
	}

	function elcpt_admin_menu() {
		add_options_page( __( 'Post Type Template', 'elementor-templater' ), __( 'Post Type Template', 'elementor-templater' ), 'manage_options', 'elcpt-settings', array($this, 'elcpt_admin_page') );
	}

	function elcpt_admin_page() {
		require ET_PATH . 'inc/settings.php';
	}

	function elcpt_admin_init() {
		$cpt_options = get_option( 'elcpt_options' );

		if ( isset( $cpt_options['objects'] ) ) {
			$post_types = apply_filters( 'elcpt_post_types', $cpt_options['objects'] );
			foreach ( $post_types as $post_type ) {
				add_meta_box(
					'postparentdiv',
					__( 'Custom Template', 'elementor-templater' ),
					array( $this, 'elcpt_post_template'),
					$post_type,
					'side',
					'high'
				);
			}
		}
	}

	function elcpt_post_template( $post ) {
		$this->post_ID = $post->ID;

		$template_vars   = array();
		$templates       = $this->elcpt_get_post_templates();
		$custom_template = $this->elcpt_get_custom_post_template();

		if ( $templates ) { ?>
			<label class="hidden" for="page_template"><?php _e( 'Choose Template', 'elementor-templater' ); ?></label>
			<input type="hidden" name="cpt_current_template" value="1" />
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

	function elcpt_get_post_templates() {
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

	function elcpt_get_custom_post_template() {
		$custom_template = get_post_meta( $this->post_ID, $this->elcpt_meta, true );
		return $custom_template;
	}

	function elcpt_set_custom_post_template( $template ) {
		delete_post_meta( $this->post_ID, $this->elcpt_meta );
		if ( ! $template || $template == 'default' ) {
			return;
		}
		add_post_meta( $this->post_ID, $this->elcpt_meta, $template );
	}

	function elcpt_save_post( $post_ID ) {
		$action_needed = (bool) @ $_POST['cpt_current_template'];
		if ( ! $action_needed ) {
			return;
		}

		$this->post_ID = $post_ID;
		$template      = (string) @ $_POST['custom_post_template'];
		$template      = stripslashes_deep( $template );
		$this->elcpt_set_custom_post_template( $template );
	}

	function elcpt_load_single_template( $template ) {
		global $wp_query;
		$this->post_ID = $wp_query->post->ID;
		$template_file = $this->elcpt_get_custom_post_template();

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

	function elcpt_body_class( $classes ) {
		if ( ! is_single() ) {
			return $classes;
		}
		$post_template = get_post_meta( get_the_ID(), 'elcpt_template_name', true );
		if ( ! empty( $post_template ) ) {
			$classes[] = 'page-template';
			$classes[] = 'page-template-' . str_replace( '.php', '-php', $post_template );
		}
		return $classes;
	}

	function elcpt_update_options() {
		global $wpdb;
		if ( ! isset( $_POST['elcpt_submit'] ) ) {
			return false;
		}

		check_admin_referer( 'nonce_elcpt' );
		$input_options            = array();
		$input_options['objects'] = isset( $_POST['objects'] ) ? $_POST['objects'] : '';
		update_option( 'elcpt_options', $input_options );
		wp_redirect( 'options-general.php?page=elcpt-settings&msg=update' );
	}

	function elcpt_uninstall() {
		delete_option( 'elcpt_options' );
	}
}

/**
 * Instantiate the plugin
 *
 * @global
 */
$cpt_obj = new EL_CustomTemplates();
?>
