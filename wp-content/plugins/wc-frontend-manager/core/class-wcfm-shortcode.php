<?php
/**
 * WCFM plugin core
 *
 * Plugin shortcode
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM_Shortcode {

	public $list_product;

	public function __construct() {
		// WC Frontend Manager Shortcode
		add_shortcode('wc_frontend_manager', array(&$this, 'wc_frontend_manager'));
		
		// WC Frontend Manager Endpoint as Shortcode
		add_shortcode('wcfm', array(&$this, 'wcfm_endpoint_shortcode'));
		
		// WC Frontend Manager Header Panel Notifications as Shortcode
		add_shortcode('wcfm_notifications', array(&$this, 'wcfm_notifications_shortcode'));
	}

	public function wc_frontend_manager($attr) {
		global $WCFM;
		$this->load_class('wc-frontend-manager');
		return $this->shortcode_wrapper(array('WCFM_Frontend_Manager_Shortcode', 'output'));
	}
	
	/**
	 * WCFM End point as Short Code
	 */
	public function wcfm_endpoint_shortcode( $attr ) {
		global $WCFM, $wp, $WCFM_Query;
		$WCFM->nocache();
		
		echo '<div id="wcfm-main-contentainer"> <div id="wcfm-content">';
		
		$menu = true;
		if ( isset( $attr['menu'] ) && !empty( $attr['menu'] ) && ( 'false' == $attr['menu'] ) ) { $menu = false; } 
		
		if ( !isset( $attr['endpoint'] ) || ( isset( $attr['endpoint'] ) && empty( $attr['endpoint'] ) ) ) {
			
			// Load Scripts
			$WCFM->library->load_scripts( 'wcfm-dashboard' );
			
			// Load Styles
			$WCFM->library->load_styles( 'wcfm-dashboard' );
			
			// Load View
			$WCFM->library->load_views( 'wcfm-dashboard', $menu );
		} else {
			$wcfm_endpoints = $WCFM_Query->get_query_vars();
			
			foreach ( $wcfm_endpoints as $key => $value ) {
				if ( isset( $attr['endpoint'] ) && !empty( $attr['endpoint'] ) && ( $key == $attr['endpoint'] ) ) {
					// Load Scripts
					$WCFM->library->load_scripts( $key );
					
					// Load Styles
					$WCFM->library->load_styles( $key );
					
					// Load View
					$WCFM->library->load_views( $key, $menu );
				}
			}
		}
		
		echo '</div></div>';
	}
	
	/**
	 * WC Frontend Manager Header Panel Notifications as Shortcode
	 */
	function wcfm_notifications_shortcode( $attr ) {
		global $WCFM, $wp, $WCFM_Query;
		
		if( !wcfm_is_allow_wcfm() ) return;
		
		$message = true;
		if ( isset( $attr['message'] ) && !empty( $attr['message'] ) && ( 'false' == $attr['message'] ) ) { $message = false; }
		
		$enquiry = true;
		if ( isset( $attr['enquiry'] ) && !empty( $attr['enquiry'] ) && ( 'false' == $attr['enquiry'] ) ) { $enquiry = false; }
		
		$notice = true;
		if ( isset( $attr['notice'] ) && !empty( $attr['notice'] ) && ( 'false' == $attr['notice'] ) ) { $notice = false; }
		
		$unread_notice = $WCFM->frontend->wcfm_direct_message_count( 'notice' );
		$unread_message = $WCFM->frontend->wcfm_direct_message_count( 'message' ); 
		$unread_enquiry = $WCFM->frontend->wcfm_direct_message_count( 'enquiry' );
		?>
		<div class="wcfm_sc_notifications">
			<?php if( $message && apply_filters( 'wcfm_is_pref_direct_message', true ) && apply_filters( 'wcfm_is_allow_notifications', true ) && apply_filters( 'wcfm_is_allow_sc_notifications', true ) ) { ?>
				<a href="<?php echo get_wcfm_messages_url( ); ?>" class="fa fa-bell-o text_tip" data-tip="<?php _e( 'Notification Board', 'wc-frontend-manager' ); ?>"><span class="unread_notification_count message_count"><?php echo $unread_message; ?></span></a>
			<?php } ?>
			
			<?php if( $enquiry && apply_filters( 'wcfm_is_pref_enquiry', true ) && apply_filters( 'wcfm_is_allow_enquiry', true ) && apply_filters( 'wcfm_is_allow_sc_enquiry_notifications', true ) ) { ?>
				<a href="<?php echo get_wcfm_enquiry_url(); ?>" class="fa fa-question-circle-o text_tip" data-tip="<?php _e( 'Enquiry Board', 'wc-frontend-manager' ); ?>"><span class="unread_notification_count enquiry_count"><?php echo $unread_enquiry; ?></span></a>
			<?php } ?>
			
			<?php if( $notice && apply_filters( 'wcfm_is_pref_notice', true ) && apply_filters( 'wcfm_is_allow_notice', true ) && apply_filters( 'wcfm_is_allow_sc_notice_notifications', true ) ) { ?>
				<a href="<?php echo get_wcfm_notices_url( ); ?>" class="fa fa-bullhorn text_tip" data-tip="<?php _e( 'Notice Board', 'wc-frontend-manager' ); ?>"><?php if( wcfm_is_vendor() ) { ?><span class="unread_notification_count notice_count"><?php echo $unread_notice; ?></span><?php } ?></a>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	public function load_class($class_name = '') {
		global $WCFM;
		if ('' != $class_name && '' != $WCFM->token) {
			require_once ( $WCFM->plugin_path . 'includes/shortcodes/class-' . esc_attr($WCFM->token) . '-shortcode-' . esc_attr($class_name) . '.php' );
		}
	}

}
?>