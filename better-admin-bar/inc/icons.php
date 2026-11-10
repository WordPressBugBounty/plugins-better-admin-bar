<?php
/**
 * Icons: the Lucide icons of the quick access panel, drawn as inline SVG.
 *
 * Widgets save the name of a Lucide icon ("gauge"). Settings from before 5.0 have Font Awesome 5 classes
 * ("fas fa-tachometer-alt"), they are shown as the closest Lucide icon or the fallback icon. Saved settings
 * aren't changed: Better Admin Bar PRO shares them.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * The icon for widgets whose icon has no match (Font Awesome brand icons, unknown names).
 *
 * @return string The Lucide icon name.
 */
function swift_control_fallback_icon() {

	return 'link';

}

/**
 * Get all Lucide icons.
 *
 * @return array The inner SVG markup of each icon, keyed by icon name.
 */
function swift_control_get_icons() {

	static $icons = null;

	if ( null === $icons ) {
		$icons = json_decode( (string) file_get_contents( SWIFT_CONTROL_PLUGIN_DIR . '/assets/icons/lucide.json' ), true );
		$icons = is_array( $icons ) ? $icons : array();
	}

	return $icons;

}

/**
 * Get the Lucide icon to show for a saved icon.
 *
 * @param string $icon The saved icon: a Lucide name or an old Font Awesome class.
 *
 * @return string The Lucide icon name.
 */
function swift_control_resolve_icon( $icon ) {

	$icon  = is_string( $icon ) ? strtolower( trim( $icon ) ) : '';
	$icons = swift_control_get_icons();

	// Font Awesome class from before 5.0, like "fas fa-tachometer-alt". Brand icons ("fab") have no match.
	if ( preg_match( '/^(fas|far|fa)\s+fa-([a-z0-9-]+)$/', $icon, $matches ) ) {
		static $font_awesome_map = null;

		if ( null === $font_awesome_map ) {
			$font_awesome_map = require __DIR__ . '/font-awesome-map.php';
		}

		$icon = isset( $font_awesome_map[ $matches[2] ] ) ? $font_awesome_map[ $matches[2] ] : '';
	}

	return isset( $icons[ $icon ] ) ? $icon : swift_control_fallback_icon();

}

/**
 * Get the SVG markup of an icon.
 *
 * @param string $icon The saved icon: a Lucide name or an old Font Awesome class.
 *
 * @return string The SVG markup, safe to print.
 */
function swift_control_icon_svg( $icon ) {

	$icon  = swift_control_resolve_icon( $icon );
	$icons = swift_control_get_icons();
	$inner = isset( $icons[ $icon ] ) ? $icons[ $icon ] : '';

	// The inner markup comes from our own icon file (paths, circles, rects… with plain attributes).
	return '<svg class="swift-control-icon" data-icon="' . esc_attr( $icon ) . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $inner . '</svg>';

}
