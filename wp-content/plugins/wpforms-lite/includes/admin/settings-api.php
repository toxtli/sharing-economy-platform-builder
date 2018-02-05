<?php
/**
 * Settings API.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.3.7
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2017, WPForms LLC
 */

/**
 * Settings output wrapper.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_output_field( $args ) {

	// Define default callback for this field type.
	$callback = ! empty( $args['type'] ) && function_exists( 'wpforms_settings_' . $args['type'] . '_callback' ) ? 'wpforms_settings_' . $args['type'] . '_callback' : 'wpforms_settings_missing_callback';

	// Allow custom callback to be provided via arg.
	if ( ! empty( $args['callback'] ) && function_exists( $args['callback'] ) ) {
		$callback = $args['callback'];
	}

	// Store returned markup from callback.
	$field = call_user_func( $callback, $args );

	// Allow arg to bypass standard field wrap for custom display.
	if ( ! empty( $args['wrap'] ) ) {
		return $field;
	}

	// Custom row classes.
	$class = ! empty( $args['class'] ) ? wpforms_sanitize_classes( (array) $args['class'], true ) : '';

	// Build standard field markup and return.
	$output  = '<div class="wpforms-setting-row wpforms-setting-row-' . sanitize_html_class( $args['type'] ) . ' wpforms-clear ' . $class . '" id="wpforms-setting-row-' . wpforms_sanitize_key( $args['id'] ) . '">';

		if ( ! empty( $args['name'] ) && empty( $args['no_label'] ) ) {
			$output .= '<span class="wpforms-setting-label">';
				$output .= '<label for="wpforms-setting-' . wpforms_sanitize_key( $args['id'] ) . '">' . esc_html( $args['name'] ) . '</label>';
			$output .= '</span>';
		}

		$output .= '<span class="wpforms-setting-field">';
			$output .= $field;
		$output .= '</span>';

	$output .= '</div>';

	return $output;
}

/**
 * Missing Callback.
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.3.9
 * @param array $args Arguments passed by the setting.
 */
function wpforms_settings_missing_callback( $args ) {

	return sprintf(
		__( 'The callback function used for the %s setting is missing.', 'wpforms' ),
		'<strong>' . wpforms_sanitize_key( $args['id'] ) . '</strong>'
	);
}

/**
 * Settings content field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_content_callback( $args ) {

	return ! empty( $args['content'] ) ? $args['content'] : '';
}

/**
 * Settings license field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_license_callback( $args ) {

	// Lite users don't need to worry about license keys.
	if ( ! wpforms()->pro || ! class_exists( 'WPForms_License' ) ) {
		$output  = '<p>' . __( 'You\'re using WPForms Lite - no license needed. Enjoy!', 'wpforms') . ' 🙂</p>';
		$output .= '<p>' . sprintf( __( 'To unlock more features consider <a href="%s" target="_blank" rel="noopener noreferrer" class="wpforms-upgrade-modal">upgrading to Pro</a>.</p>', 'wpforms' ), wpforms_admin_upgrade_link() );
		return $output;
	}

	$key  = wpforms_setting( 'key', '', 'wpforms_license' );
	$type = wpforms_setting( 'type', '', 'wpforms_license' );

	$output  = '<input type="password" id="wpforms-setting-license-key" value="' . esc_attr( $key ) . '" />';
	$output .= '<button id="wpforms-setting-license-key-verify" class="wpforms-btn wpforms-btn-md wpforms-btn-orange">' . __( 'Verify Key', 'wpforms' ) . '</button>';

	// Offer option to deactivate the key.
	$class = empty( $key ) ? 'wpforms-hide' : '';
	$output .= '<button id="wpforms-setting-license-key-deactivate" class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey ' . $class . '">' . __( 'Deactivate Key', 'wpforms' ) . '</button>';

	// If we have previously looked up the license type, display it.
	$class = empty( $type ) ? 'wpforms-hide' : '';
	$output .= '<p class="type ' . $class . '">' . sprintf( __( 'Your license key type is %s.', 'wpforms' ), '<strong>' . esc_html( $type ) . '</strong>' ) .'</p>';
	$output .= '<p class="desc ' . $class . '">' . __( 'If your license has been upgraded or is incorrect, <a href="#" id="wpforms-setting-license-key-refresh">click here to force a refresh</a>.', 'wpforms' ) . '</p>';

	return $output;
}

/**
 * Settings text input field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_text_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wpforms_setting( $args['id'], $default );
	$id      = wpforms_sanitize_key( $args['id'] );

	$output = '<input type="text" id="wpforms-setting-' . $id . '" name="' . $id . '" value="' . esc_attr( $value ) . '">';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings select field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_select_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wpforms_setting( $args['id'], $default );
	$id      = wpforms_sanitize_key( $args['id'] );
	$class   = ! empty( $args['choicesjs'] ) ? 'choicesjs-select' : '';
	$choices = ! empty( $args['choicesjs'] ) ? true : false;
	$data    = '';

	if ( $choices && !empty( $args['search'] ) ) {
		$data = ' data-search="true"';
	}

	$output  = $choices ? '<span class="choicesjs-select-wrap">' : '';
	$output .= '<select id="wpforms-setting-' . $id . '" name="' . $id . '" class="' . $class . '"' . $data . '>';

		foreach ( $args['options'] as $option => $name ) {
			$selected = selected( $value, $option, false );
			$output .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		}

	$output .= '</select>';
	$output .= $choices ? '</span>' : '';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings checkbox field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_checkbox_callback( $args ) {

	$value   = wpforms_setting( $args['id'] );
	$id      = wpforms_sanitize_key( $args['id'] );
	$checked = ! empty( $value ) ? checked( 1, $value, false ) : '';

	$output = '<input type="checkbox" id="wpforms-setting-' . $id . '" name="' . $id . '" ' . $checked . '>';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings radio field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_radio_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wpforms_setting( $args['id'], $default );
	$id      = wpforms_sanitize_key( $args['id'] );
	$output  = '';
	$x       = 1;

	foreach ( $args['options'] as $option => $name ) {

		$checked  = checked( $value, $option, false );
		$output  .= '<label for="wpforms-setting-' . $id . '[' . $x . ']" class="option-' . sanitize_html_class( $option ) . '">';
			$output .= '<input type="radio" id="wpforms-setting-' . $id . '[' . $x . ']" name="' . $id . '" value="' . esc_attr( $option ) . '" ' . $checked . '>';
			$output .= esc_html( $name );
		$output .= '</label>';
		$x++;
	}

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings image upload field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_image_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wpforms_setting( $args['id'], $default );
	$id      = wpforms_sanitize_key( $args['id'] );
	$output  = '';

	if ( ! empty( $value ) ) {
		$output .= '<img src="' . esc_url_raw( $value ) . '">';
	}

	$output .= '<input type="text" id="wpforms-setting-' . $id . '" name="' . $id . '" value="' . esc_url_raw( $value ) . '">';
	$output .= '<button class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey">' . __( 'Upload Image', 'wpforms' ) . '</button>';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings color picker field callback.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_color_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wpforms_setting( $args['id'], $default );
	$id      = wpforms_sanitize_key( $args['id'] );

	$output  = '<input type="text" id="wpforms-setting-' . $id . '" class="wpforms-color-picker" name="' . $id . '" value="' . esc_attr( $value ) . '">';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings providers field callback - this is for the Integrations tab.
 *
 * @since 1.3.9
 * @param array $args
 * @return string
 */
function wpforms_settings_providers_callback( $args ) {

	$providers = get_option( 'wpforms_providers', false );
	$active    = apply_filters( 'wpforms_providers_available', array() );

	$output = '<div id="wpforms-settings-providers">';

		ob_start();
		do_action( 'wpforms_settings_providers', $active, $providers );
		$output .= ob_get_clean();

	$output .= '</div>';

	return $output;
}
