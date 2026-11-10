<?php
/**
 * Helper functions.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * The capability needed to see the quick access panel and to manage the settings.
 *
 * @return string The capability.
 */
function swift_control_capability() {

	return 'delete_others_posts';

}

/**
 * Get the user roles of the site.
 *
 * @return array Role names, keyed by role.
 */
function swift_control_get_roles() {

	$roles = new WP_Roles();

	return $roles->role_names;

}

/**
 * Check if the quick access panel has active widgets.
 *
 * @return bool Whether the panel has active widgets.
 */
function swift_control_has_active_widgets() {

	return ! empty( swift_control_get_active_widgets() );

}

/**
 * Get the default active widget keys.
 *
 * @return array The default active widget keys.
 */
function swift_control_get_default_active_widgets() {

	return array_keys( require __DIR__ . '/widgets/active-widgets.php' );

}

/**
 * Get the default active widgets that are not active anymore.
 *
 * @return array The unused default active widget keys.
 */
function swift_control_get_unused_default_active_widgets() {

	return array_values( array_diff( swift_control_get_default_active_widgets(), swift_control_get_active_widgets() ) );

}

/**
 * Get the active widget keys.
 *
 * @return array The active widget keys.
 */
function swift_control_get_active_widgets() {

	$active_widgets = get_option( 'swift_control_active_widgets' );

	// Fresh install.
	if ( false === $active_widgets ) {
		return swift_control_get_default_active_widgets();
	}

	return is_array( $active_widgets ) ? $active_widgets : array();

}

/**
 * Get the available (inactive) widget keys.
 *
 * @return array The available widget keys.
 */
function swift_control_get_available_widgets() {

	$available_widgets = array_merge( swift_control_get_unused_default_active_widgets(), array_keys( require __DIR__ . '/widgets/available-widgets.php' ) );

	return array_values( array_diff( $available_widgets, swift_control_get_active_widgets() ) );

}

/**
 * Get the locked PRO widget keys.
 *
 * @return array The locked widget keys.
 */
function swift_control_get_locked_widgets() {

	return array_keys( swift_control_get_locked_widget_settings() );

}

/**
 * Get the default settings of the active & available widgets.
 *
 * @return array The default widget settings, keyed by widget.
 */
function swift_control_get_default_widget_settings() {

	static $defaults = null;

	// The active widgets come first, their settings win.
	if ( null === $defaults ) {
		$defaults = ( require __DIR__ . '/widgets/active-widgets.php' ) + ( require __DIR__ . '/widgets/available-widgets.php' );
	}

	return $defaults;

}

/**
 * Get the saved widget settings.
 *
 * @return array The saved settings, keyed by widget.
 */
function swift_control_get_saved_widget_settings() {

	$settings = get_option( 'swift_control_widget_settings', array() );

	return is_array( $settings ) ? $settings : array();

}

/**
 * Get the settings of the locked PRO widgets.
 *
 * @return array The locked widget settings, keyed by widget.
 */
function swift_control_get_locked_widget_settings() {

	return require __DIR__ . '/widgets/pro-widgets.php';

}

/**
 * Get the default color settings.
 *
 * @return array The default color settings.
 */
function swift_control_get_default_color_settings() {

	return array(
		'widget_bg_color'           => '#ffffff',
		'widget_bg_color_hover'     => '#f4f2f9',
		'widget_icon_color'         => '#2b2b33',
		'setting_button_bg_color'   => '#860ee6',
		'setting_button_icon_color' => '#ffffff',
	);

}

/**
 * Get the color settings.
 *
 * @return array The color settings.
 */
function swift_control_get_color_settings() {

	$saved_settings = get_option( 'swift_control_color_settings', array() );
	$saved_settings = is_array( $saved_settings ) ? $saved_settings : array();
	$color_settings = swift_control_get_default_color_settings();

	foreach ( $color_settings as $color_key => $default_color ) {
		$color = isset( $saved_settings[ $color_key ] ) ? swift_control_sanitize_color( $saved_settings[ $color_key ] ) : '';

		if ( '' !== $color ) {
			$color_settings[ $color_key ] = $color;
		}
	}

	return $color_settings;

}

/**
 * Get the miscellaneous settings.
 *
 * @return array The miscellaneous settings.
 */
function swift_control_get_misc_settings() {

	$saved_settings = get_option( 'swift_control_misc_settings', array() );

	return is_array( $saved_settings ) ? $saved_settings : array();

}

/**
 * Get the admin bar settings.
 *
 * Empty number fields stay '' (empty string), they fall back to the defaults on output.
 *
 * @return array The admin bar settings.
 */
function swift_control_get_admin_bar_settings() {

	$saved_settings = get_option( 'swift_control_admin_bar_settings', array() );
	$saved_settings = is_array( $saved_settings ) ? $saved_settings : array();
	$settings       = array(
		'remove_by_roles' => isset( $saved_settings['remove_by_roles'] ) ? $saved_settings['remove_by_roles'] : array(),
	);

	foreach ( swift_control_admin_bar_flag_fields() as $field ) {
		$settings[ $field ] = isset( $saved_settings[ $field ] ) ? absint( $saved_settings[ $field ] ) : 0;
	}

	foreach ( swift_control_admin_bar_number_fields() as $field ) {
		$settings[ $field ] = isset( $saved_settings[ $field ] ) && '' !== $saved_settings[ $field ] ? absint( $saved_settings[ $field ] ) : '';
	}

	// Old values were 0 / 1.
	if ( is_numeric( $settings['remove_by_roles'] ) ) {
		$settings['remove_by_roles'] = absint( $settings['remove_by_roles'] ) ? array( 'all' ) : array();
	}

	if ( ! is_array( $settings['remove_by_roles'] ) ) {
		$settings['remove_by_roles'] = array();
	}

	return $settings;

}

/**
 * Get the display settings.
 *
 * @return array The display settings.
 */
function swift_control_get_display_settings() {

	$saved_settings = get_option( 'swift_control_display_settings', array() );
	$saved_settings = is_array( $saved_settings ) ? $saved_settings : array();
	$settings       = array();

	foreach ( swift_control_display_fields() as $field ) {
		$settings[ $field ] = isset( $saved_settings[ $field ] ) ? absint( $saved_settings[ $field ] ) : 0;
	}

	return $settings;

}

/**
 * The on/off fields of the display settings.
 *
 * @return array The field names.
 */
function swift_control_display_fields() {

	return array( 'disable_swift_control', 'remove_indicator', 'expanded' );

}

/**
 * The icon size of the quick access panel: default, smallest & biggest (in pixels).
 *
 * @return array The sizes, keyed "default", "min" & "max".
 */
function swift_control_icon_size_limits() {

	return array(
		'default' => 20,
		'min'     => 12,
		'max'     => 32,
	);

}

/**
 * Sanitize an icon size. Empty or invalid means the default.
 *
 * @param mixed $size The icon size in pixels.
 *
 * @return int|string The icon size within the limits, or '' for the default.
 */
function swift_control_sanitize_icon_size( $size ) {

	if ( ! is_scalar( $size ) || '' === trim( (string) $size ) || ! is_numeric( $size ) ) {
		return '';
	}

	$limits = swift_control_icon_size_limits();

	return min( $limits['max'], max( $limits['min'], absint( $size ) ) );

}

/**
 * Get the icon size of the quick access panel.
 *
 * @return int The icon size in pixels.
 */
function swift_control_get_icon_size() {

	$saved_settings = get_option( 'swift_control_display_settings', array() );
	$size           = is_array( $saved_settings ) && isset( $saved_settings['icon_size'] ) ? swift_control_sanitize_icon_size( $saved_settings['icon_size'] ) : '';

	return '' !== $size ? $size : swift_control_icon_size_limits()['default'];

}

/**
 * Sanitize the display settings: the on/off fields & the icon size.
 *
 * @param mixed $settings The display settings.
 *
 * @return array The sanitized display settings.
 */
function swift_control_sanitize_display_settings( $settings ) {

	$sanitized = swift_control_sanitize_flags( $settings, swift_control_display_fields() );

	if ( is_array( $settings ) && isset( $settings['icon_size'] ) ) {
		$size = swift_control_sanitize_icon_size( $settings['icon_size'] );

		if ( '' !== $size ) {
			$sanitized['icon_size'] = $size;
		}
	}

	return $sanitized;

}

/**
 * The on/off fields of the miscellaneous settings.
 *
 * @return array The field names.
 */
function swift_control_misc_fields() {

	return array( 'delete_on_uninstall' );

}

/**
 * The on/off fields of the admin bar settings.
 *
 * @return array The field names.
 */
function swift_control_admin_bar_flag_fields() {

	return array( 'remove_top_gap', 'fix_menu_item_overflow', 'auto_hide' );

}

/**
 * The number fields of the admin bar settings (pixels, percent or milliseconds).
 *
 * "showing_intent" and "hiding_intent" have no field on the settings page, they can only be imported.
 *
 * @return array The field names.
 */
function swift_control_admin_bar_number_fields() {

	return array( 'hide_below_screen_width', 'inactive_opacity', 'active_opacity', 'showing_intent', 'hiding_intent', 'hiding_transition_delay', 'transition_duration' );

}

/**
 * Sanitize a color: a hex color or an rgb() / rgba() color.
 *
 * @param mixed $color The color.
 *
 * @return string The sanitized color, or an empty string if it isn't a valid color.
 */
function swift_control_sanitize_color( $color ) {

	if ( ! is_string( $color ) ) {
		return '';
	}

	$color = trim( $color );

	if ( preg_match( '/^#([a-f0-9]{3}|[a-f0-9]{4}|[a-f0-9]{6}|[a-f0-9]{8})$/i', $color ) ) {
		return $color;
	}

	if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(\d*\.?\d+|\d+\.)\s*)?\)$/i', $color ) ) {
		return $color;
	}

	if ( 'transparent' === strtolower( $color ) ) {
		return 'transparent';
	}

	return '';

}

/**
 * Keep only "all" or existing role keys.
 *
 * @param mixed $roles The submitted roles.
 *
 * @return array The sanitized roles.
 */
function swift_control_sanitize_roles( $roles ) {

	if ( ! is_array( $roles ) ) {
		return array();
	}

	$roles = array_map( 'sanitize_text_field', array_filter( $roles, 'is_string' ) );

	if ( in_array( 'all', $roles, true ) ) {
		return array( 'all' );
	}

	return array_values( array_intersect( $roles, array_keys( swift_control_get_roles() ) ) );

}

/**
 * Check if a widget key is valid.
 *
 * Better Admin Bar PRO uses the same options, so PRO widgets (e.g. custom ones) stay valid here.
 *
 * @param mixed $widget_key The widget key.
 *
 * @return bool Whether the widget key is valid.
 */
function swift_control_is_valid_widget_key( $widget_key ) {

	return is_string( $widget_key ) && (bool) preg_match( '/^[a-zA-Z0-9_-]+$/', $widget_key );

}

/**
 * Sanitize a list of widget keys.
 *
 * @param mixed $widget_keys The widget keys.
 *
 * @return array The sanitized widget keys.
 */
function swift_control_sanitize_widget_keys( $widget_keys ) {

	if ( ! is_array( $widget_keys ) ) {
		return array();
	}

	return array_values( array_unique( array_filter( $widget_keys, 'swift_control_is_valid_widget_key' ) ) );

}

/**
 * Sanitize the settings of one widget.
 *
 * @param mixed $settings The widget settings (icon_class, text, url, new_tab, redirect_url).
 *
 * @return array The sanitized settings. Missing settings stay missing.
 */
function swift_control_sanitize_widget_setting( $settings ) {

	if ( ! is_array( $settings ) ) {
		return array();
	}

	$sanitized = array();

	if ( isset( $settings['icon_class'] ) && is_string( $settings['icon_class'] ) ) {
		$sanitized['icon_class'] = trim( preg_replace( '/[^a-z0-9 _-]/i', '', $settings['icon_class'] ) );
	}

	if ( isset( $settings['text'] ) && is_scalar( $settings['text'] ) ) {
		$sanitized['text'] = sanitize_text_field( $settings['text'] );
	}

	foreach ( array( 'url', 'redirect_url' ) as $url_field ) {
		if ( isset( $settings[ $url_field ] ) && is_string( $settings[ $url_field ] ) ) {
			// Keep "auto" (the edit post widget), it isn't a url.
			$sanitized[ $url_field ] = 'auto' === $settings[ $url_field ] ? 'auto' : esc_url_raw( $settings[ $url_field ] );
		}
	}

	if ( isset( $settings['new_tab'] ) ) {
		$sanitized['new_tab'] = absint( $settings['new_tab'] ) ? 1 : 0;
	}

	return $sanitized;

}

/**
 * Sanitize the saved settings of all widgets.
 *
 * @param mixed $widget_settings The widget settings, keyed by widget.
 *
 * @return array The sanitized widget settings.
 */
function swift_control_sanitize_widget_settings( $widget_settings ) {

	if ( ! is_array( $widget_settings ) ) {
		return array();
	}

	$sanitized = array();

	foreach ( swift_control_sanitize_widget_keys( array_keys( $widget_settings ) ) as $widget_key ) {
		$settings = is_array( $widget_settings[ $widget_key ] ) ? $widget_settings[ $widget_key ] : array();
		$known    = swift_control_sanitize_widget_setting( $settings );

		// Keep the settings only Better Admin Bar PRO uses (as plain text), PRO shares this option.
		$extra = map_deep( array_diff_key( $settings, array_flip( array( 'icon_class', 'text', 'url', 'new_tab', 'redirect_url' ) ) ), 'sanitize_text_field' );

		$sanitized[ $widget_key ] = $known + $extra;
	}

	return $sanitized;

}

/**
 * Sanitize the color settings. Invalid colors are left out (they fall back to the defaults).
 *
 * @param mixed $settings The color settings.
 *
 * @return array The sanitized color settings.
 */
function swift_control_sanitize_color_settings( $settings ) {

	$sanitized = array();

	if ( ! is_array( $settings ) ) {
		return $sanitized;
	}

	foreach ( array_keys( swift_control_get_default_color_settings() ) as $field ) {
		$color = isset( $settings[ $field ] ) ? swift_control_sanitize_color( $settings[ $field ] ) : '';

		if ( '' !== $color ) {
			$sanitized[ $field ] = $color;
		}
	}

	return $sanitized;

}

/**
 * Sanitize on/off settings.
 *
 * @param mixed $settings The settings.
 * @param array $fields   The allowed field names.
 *
 * @return array The sanitized settings (1 or 0).
 */
function swift_control_sanitize_flags( $settings, $fields ) {

	$sanitized = array();

	if ( ! is_array( $settings ) ) {
		return $sanitized;
	}

	foreach ( $fields as $field ) {
		if ( isset( $settings[ $field ] ) ) {
			$sanitized[ $field ] = absint( $settings[ $field ] ) ? 1 : 0;
		}
	}

	return $sanitized;

}

/**
 * Sanitize the admin bar settings.
 *
 * @param mixed $settings The admin bar settings.
 *
 * @return array The sanitized admin bar settings.
 */
function swift_control_sanitize_admin_bar_settings( $settings ) {

	if ( ! is_array( $settings ) ) {
		return array();
	}

	$sanitized = swift_control_sanitize_flags( $settings, swift_control_admin_bar_flag_fields() );

	if ( isset( $settings['remove_by_roles'] ) ) {
		$roles = $settings['remove_by_roles'];

		// Old values were 0 / 1 (none / all), older exports still have them.
		if ( is_numeric( $roles ) ) {
			$roles = absint( $roles ) ? array( 'all' ) : array();
		}

		$sanitized['remove_by_roles'] = swift_control_sanitize_roles( $roles );
	}

	foreach ( swift_control_admin_bar_number_fields() as $field ) {
		if ( ! isset( $settings[ $field ] ) || ! is_scalar( $settings[ $field ] ) ) {
			continue;
		}

		$value = trim( (string) $settings[ $field ] );

		// Anything that isn't a number means "use the default".
		$sanitized[ $field ] = is_numeric( $value ) ? absint( $value ) : '';
	}

	// Opacity is a percentage.
	foreach ( array( 'inactive_opacity', 'active_opacity' ) as $field ) {
		if ( isset( $sanitized[ $field ] ) && '' !== $sanitized[ $field ] ) {
			$sanitized[ $field ] = min( 100, $sanitized[ $field ] );
		}
	}

	return $sanitized;

}

/**
 * Parse the widget settings: saved settings first, then the defaults.
 *
 * @param string $widget_key      The widget key.
 * @param array  $widget_settings The saved widget settings.
 *
 * @return array The parsed widget settings.
 */
function swift_control_parse_widget_settings( $widget_key, $widget_settings ) {

	$default_settings = swift_control_get_default_widget_settings();
	$widget_settings  = is_array( $widget_settings ) ? $widget_settings : array();
	$parsed_defaults  = wp_parse_args(
		isset( $default_settings[ $widget_key ] ) ? $default_settings[ $widget_key ] : array(),
		array(
			'icon_class'   => '',
			'text'         => '',
			'url'          => '',
			'new_tab'      => '',
			'redirect_url' => '',
		)
	);

	return array(
		'icon_class'   => isset( $widget_settings['icon_class'] ) ? $widget_settings['icon_class'] : $parsed_defaults['icon_class'],
		'text'         => isset( $widget_settings['text'] ) ? $widget_settings['text'] : $parsed_defaults['text'],
		'url'          => isset( $widget_settings['url'] ) ? $widget_settings['url'] : $parsed_defaults['url'],
		'new_tab'      => isset( $widget_settings['new_tab'] ) ? absint( $widget_settings['new_tab'] ) : $parsed_defaults['new_tab'],
		'redirect_url' => isset( $widget_settings['redirect_url'] ) ? $widget_settings['redirect_url'] : $parsed_defaults['redirect_url'],
	);

}

/**
 * Get the edit url of the current post.
 *
 * @return string The edit post url.
 */
function swift_control_get_edit_post_url() {

	return (string) get_edit_post_link();

}

/**
 * Check if the current page is opened in the editor of a page builder.
 *
 * @return bool
 */
function swift_control_is_inside_page_builder() {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checks if the parameters exist.
	return isset( $_GET['elementor-preview'] ) || isset( $_GET['fl_builder'] ) || isset( $_GET['brizy-edit-iframe'] ) || isset( $_GET['ct_builder'] );

}

/**
 * Parse the widget name.
 *
 * @param string $widget_name The widget name.
 * @param string $widget_key  The widget key.
 *
 * @return string The parsed widget name.
 */
function swift_control_parse_widget_name( $widget_name, $widget_key ) {

	if ( is_singular() ) {
		$post_type_object = get_post_type_object( get_post_type() );

		if ( $post_type_object && false !== stripos( $widget_name, '{post_type}' ) ) {
			$widget_name = str_ireplace( '{post_type}', $post_type_object->labels->singular_name, $widget_name );
		}
	} elseif ( 'edit_post_type' === $widget_key ) {
		$widget_name = __( 'Disabled', 'better-admin-bar' );
	}

	return ucwords( $widget_name );

}

/**
 * Parse the widget url.
 *
 * @param string $widget_url The widget url.
 * @param string $widget_key The widget key.
 *
 * @return string The parsed widget url.
 */
function swift_control_parse_widget_url( $widget_url, $widget_key ) {

	global $wp;

	switch ( $widget_key ) {
		case 'theme_customizer':
			$widget_url = add_query_arg( 'url', rawurlencode( home_url( $wp->request ) ), $widget_url );
			break;
		case 'edit_post_type':
			$widget_url = swift_control_get_edit_post_url();
			break;
	}

	return $widget_url;

}

/**
 * Parse the widget class.
 *
 * @param string $widget_class The widget class names.
 * @param string $widget_key   The widget key.
 *
 * @return string The parsed widget class.
 */
function swift_control_parse_widget_class( $widget_class, $widget_key ) {

	if ( 'edit_post_type' === $widget_key && ! is_singular() ) {
		$widget_class = trim( $widget_class . ' is-disabled' );
	}

	return $widget_class;

}

/**
 * Output a widget item on the settings page.
 *
 * @param string $widget_key The widget key.
 */
function swift_control_settings_output_widget_item( $widget_key ) {

	$saved_settings  = swift_control_get_saved_widget_settings();
	$default_widgets = swift_control_get_default_widget_settings();
	$default_setting = isset( $default_widgets[ $widget_key ] ) ? $default_widgets[ $widget_key ] : array();
	$widget_settings = ! empty( $saved_settings[ $widget_key ] ) ? $saved_settings[ $widget_key ] : array();
	$parsed_settings = swift_control_parse_widget_settings( $widget_key, $widget_settings );
	$has_new_tab     = isset( $default_setting['new_tab'] ) || isset( $widget_settings['new_tab'] );
	$has_redirect    = isset( $default_setting['redirect_url'] ) || isset( $widget_settings['redirect_url'] );

	require SWIFT_CONTROL_PLUGIN_DIR . '/templates/fields/widget-item.php';

}

/**
 * Output the quick access panel.
 *
 * @param bool $is_preview Whether the panel is the preview inside the admin area.
 */
function swift_control_quick_access_panel( $is_preview = false ) {

	$display_settings = swift_control_get_display_settings();

	if ( ! $is_preview && $display_settings['disable_swift_control'] ) {
		return;
	}

	require SWIFT_CONTROL_PLUGIN_DIR . '/templates/quick-access-panel.php';

}
