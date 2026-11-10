<?php
/**
 * Runs when the plugin is deleted. Removes the data only if "Remove data on uninstall" is checked.
 *
 * @package Better_Admin_Bar
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || die( "Can't access directly" );

/**
 * Delete the options of the current site, if the site asked for it.
 *
 * @return bool Whether the data was deleted.
 */
function swift_control_uninstall_site() {

	$misc_settings = get_option( 'swift_control_misc_settings', array() );

	if ( empty( $misc_settings['delete_on_uninstall'] ) ) {
		return false;
	}

	$options = array(
		'swift_control_active_widgets',
		'swift_control_widget_settings',
		'swift_control_display_settings',
		'swift_control_color_settings',
		'swift_control_admin_bar_settings',
		'swift_control_misc_settings',
		'swift_control_discontinue_message',
		'swift_control_compat_migrate_options',
		'swift_control_compat_panel_colors',
	);

	foreach ( $options as $option ) {
		delete_option( $option );
	}

	return true;

}

// Better Admin Bar PRO uses the same data, keep it while PRO is installed (active or not).
if ( defined( 'SWIFT_CONTROL_PRO_PLUGIN_URL' ) ) {
	return;
}

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

foreach ( array_keys( get_plugins() ) as $swift_control_plugin_file ) {
	if ( 'swift-control-pro.php' === basename( $swift_control_plugin_file ) ) {
		return;
	}
}

$swift_control_deleted = false;

if ( is_multisite() && function_exists( 'get_sites' ) ) {
	foreach ( get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	) as $swift_control_site_id ) {
		switch_to_blog( $swift_control_site_id );
		$swift_control_deleted = swift_control_uninstall_site() || $swift_control_deleted;
		restore_current_blog();
	}
} else {
	$swift_control_deleted = swift_control_uninstall_site();
}

// The saved panel positions of the users.
if ( $swift_control_deleted ) {
	delete_metadata( 'user', 0, 'swift_control_position', '', true );
}
