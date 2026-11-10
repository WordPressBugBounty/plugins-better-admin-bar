<?php
/**
 * Backwards compatibility.
 *
 * @package Better_Admin_Bar
 */

namespace SwiftControl;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class that handles backwards compatibility.
 */
class Backwards_Compatibility {

	/**
	 * Init the class setup.
	 */
	public static function init() {
		$instance = new self();
		$instance->setup();
	}

	/**
	 * Setup the class.
	 */
	public function setup() {

		add_action( 'admin_init', array( $this, 'options_compatibility' ) );

	}

	/**
	 * Run compatibility checking on admin_init hook.
	 */
	public function options_compatibility() {

		// Don't run checking on heartbeat request.
		if ( isset( $_POST['action'] ) && 'heartbeat' === $_POST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Only compared.
			return;
		}

		$this->migrate_options();
		$this->migrate_panel_colors();

		do_action( 'swift_control_options_compatibility' );

	}

	/**
	 * Migrate settings / options.
	 *
	 * - Move "remove_admin_bar" sub-option key.
	 *   It will be moved from "swift_control_misc_settings" option name to "swift_control_admin_bar_settings" option name.
	 * - Move old admin bar settings (before getting bought by David) to the new settings.
	 *   Migrate it from "admin_bar_settings" option name to "swift_control_admin_bar_settings" option name.
	 */
	public function migrate_options() {

		// Make sure we don't check again.
		if ( get_option( 'swift_control_compat_migrate_options' ) ) {
			return;
		}

		$old_option_name  = 'admin_bar_settings'; // From the old "Better Admin Bar" plugin.
		$new_option_name  = 'swift_control_admin_bar_settings';
		$misc_option_name = 'swift_control_misc_settings';

		$old_options  = get_option( $old_option_name, array() );
		$new_options  = get_option( $new_option_name, array() );
		$misc_options = get_option( $misc_option_name, array() );

		$is_old_bab_user = false;
		$should_update   = false;

		if ( get_option( 'admin_bar_settings_backwards_compat' ) ) {
			$is_old_bab_user = true;
			$should_update   = true;

			$new_options['remove_top_gap'] = 1;
			delete_option( 'admin_bar_settings_backwards_compat' );
		}

		// Move "remove_admin_bar" sub-option key.
		if ( isset( $misc_options['remove_admin_bar'] ) ) {
			$new_options['remove_by_roles'] = $misc_options['remove_admin_bar'];

			$should_update = true;

			unset( $misc_options['remove_admin_bar'] );
		}

		if ( isset( $old_options['hide_admin_bar'] ) ) {
			$new_options['remove_by_roles'] = array( 'all' );

			$should_update = true;

			unset( $old_options['hide_admin_bar'] );
		}

		if ( isset( $old_options['show_admin'] ) ) {
			$new_options['remove_by_roles'] = array_values( array_diff( array_keys( swift_control_get_roles() ), array( 'administrator' ) ) );

			$should_update = true;

			unset( $old_options['show_admin'] );
		}

		if ( isset( $old_options['fix_multiline'] ) ) {
			$new_options['fix_menu_item_overflow'] = 1;

			$should_update = true;

			unset( $old_options['fix_multiline'] );
		}

		if ( isset( $old_options['hide_below'] ) ) {
			$new_options['hide_below_screen_width'] = $old_options['hide_below'];

			$should_update = true;

			unset( $old_options['hide_below'] );
		}

		if ( isset( $old_options['inactive_opacity'] ) ) {
			$new_options['inactive_opacity'] = $old_options['inactive_opacity'];

			$should_update = true;

			unset( $old_options['inactive_opacity'] );
		} elseif ( $is_old_bab_user ) {
			// Old "Better Admin Bar" plugin implemented 30 (%) as default inactive opacity.
			$new_options['inactive_opacity'] = 30;

			$should_update = true;
		}

		if ( isset( $old_options['active_opacity'] ) ) {
			$new_options['active_opacity'] = $old_options['active_opacity'];

			$should_update = true;

			unset( $old_options['active_opacity'] );
		} elseif ( $is_old_bab_user ) {
			// Old "Better Admin Bar" plugin implemented 100 (%) as default active opacity.
			$new_options['active_opacity'] = 100;

			$should_update = true;
		}

		if ( isset( $old_options['autohide'] ) ) {
			$new_options['auto_hide'] = 1;

			$should_update = true;

			unset( $old_options['autohide'] );
		}

		if ( isset( $old_options['hover_area'] ) ) {
			$new_options['hover_area_height'] = $old_options['hover_area'];

			$should_update = true;

			unset( $old_options['hover_area'] );
		}

		if ( isset( $old_options['hover_delay'] ) ) {
			$new_options['hiding_transition_delay'] = $old_options['hover_delay'];

			$should_update = true;

			unset( $old_options['hover_delay'] );
		}

		// Update the options it should.
		if ( $should_update ) {
			update_option( $new_option_name, $new_options );
			update_option( $misc_option_name, $misc_options );

			delete_option( $old_option_name );
		}

		do_action( 'swift_control_compat_migrate_options' );

		// Make sure we don't check again.
		update_option( 'swift_control_compat_migrate_options', 1 );

	}

	/**
	 * Give the panel the new light look (5.0) on sites that saved the old default colors.
	 *
	 * The settings page saves every color, also the ones nobody changed. Colors that still have the old
	 * default are removed, so the new defaults apply. Colors someone picked stay.
	 */
	public function migrate_panel_colors() {

		if ( get_option( 'swift_control_compat_panel_colors' ) ) {
			return;
		}

		$old_defaults = array(
			'widget_bg_color'       => '#f5f5f7',
			'widget_bg_color_hover' => '#ededf0',
			'widget_icon_color'     => '#616666',
		);
		$colors       = get_option( 'swift_control_color_settings', array() );

		if ( is_array( $colors ) ) {
			$changed = false;

			foreach ( $old_defaults as $key => $old_default ) {
				if ( isset( $colors[ $key ] ) && is_string( $colors[ $key ] ) && strtolower( trim( $colors[ $key ] ) ) === $old_default ) {
					unset( $colors[ $key ] );
					$changed = true;
				}
			}

			if ( $changed ) {
				update_option( 'swift_control_color_settings', $colors );
			}
		}

		update_option( 'swift_control_compat_panel_colors', 1 );

	}

}
