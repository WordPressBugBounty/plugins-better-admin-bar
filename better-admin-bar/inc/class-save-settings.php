<?php
/**
 * Save the settings via ajax.
 *
 * @package Better_Admin_Bar
 */

namespace SwiftControl;

defined( 'ABSPATH' ) || die( "Can't access directly" );

// phpcs:disable WordPress.Security.NonceVerification.Missing -- Every handler starts with verify().

/**
 * Ajax handlers of the settings page and the quick access panel.
 */
class Save_Settings {

	/**
	 * Register the ajax actions.
	 */
	public function __construct() {

		add_action( 'wp_ajax_swift_control_change_widgets_order', array( $this, 'change_widgets_order' ) );
		add_action( 'wp_ajax_swift_control_change_widget_settings', array( $this, 'change_widget_settings' ) );
		add_action( 'wp_ajax_swift_control_save_general_settings', array( $this, 'save_general_settings' ) );
		add_action( 'wp_ajax_swift_control_save_position', array( $this, 'save_position' ) );

	}

	/**
	 * Check the nonce and the capability, stop if one fails.
	 *
	 * @param string $action The nonce action.
	 */
	private function verify( $action ) {

		if ( ! check_ajax_referer( $action, 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid token', 'better-admin-bar' ), 401 );
		}

		if ( ! current_user_can( swift_control_capability() ) ) {
			wp_send_json_error( __( 'You are not allowed to do this', 'better-admin-bar' ), 403 );
		}

	}

	/**
	 * Save the active widgets & their order.
	 */
	public function change_widgets_order() {

		$this->verify( 'change_widgets_order' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below.
		$active_widgets = isset( $_POST['active_widgets'] ) ? wp_unslash( $_POST['active_widgets'] ) : array();

		update_option( 'swift_control_active_widgets', swift_control_sanitize_widget_keys( $active_widgets ) );

		wp_send_json_success( __( 'Widget order is changed', 'better-admin-bar' ) );

	}

	/**
	 * Save the settings of one widget.
	 */
	public function change_widget_settings() {

		$this->verify( 'change_widget_settings' );

		$widget_key = isset( $_POST['widget_key'] ) ? sanitize_text_field( wp_unslash( $_POST['widget_key'] ) ) : '';

		if ( ! swift_control_is_valid_widget_key( $widget_key ) ) {
			wp_send_json_error( __( 'Unknown widget', 'better-admin-bar' ) );
		}

		$settings = swift_control_sanitize_widget_setting( wp_unslash( $_POST ) );

		if ( empty( $settings['icon_class'] ) ) {
			wp_send_json_error( __( 'Icon class is empty', 'better-admin-bar' ) );
		}

		if ( ! isset( $settings['text'] ) || '' === $settings['text'] ) {
			wp_send_json_error( __( 'Text is empty', 'better-admin-bar' ) );
		}

		$widget_settings = swift_control_get_saved_widget_settings();

		$widget_settings[ $widget_key ] = array_merge(
			isset( $widget_settings[ $widget_key ] ) && is_array( $widget_settings[ $widget_key ] ) ? $widget_settings[ $widget_key ] : array(),
			$settings
		);

		update_option( 'swift_control_widget_settings', $widget_settings );

		wp_send_json_success(
			array(
				'widget_key' => $widget_key,
				'message'    => __( 'Widget setting is saved', 'better-admin-bar' ),
			)
		);

	}

	/**
	 * Save the color, display, misc & admin bar settings.
	 */
	public function save_general_settings() {

		$this->verify( 'save_general_settings' );

		$data = wp_unslash( $_POST );

		update_option( 'swift_control_color_settings', swift_control_sanitize_color_settings( $data ) );
		update_option( 'swift_control_display_settings', swift_control_sanitize_display_settings( $data ) );
		update_option( 'swift_control_misc_settings', swift_control_sanitize_flags( $data, swift_control_misc_fields() ) );

		// An empty role selection isn't sent at all.
		$admin_bar_settings = swift_control_sanitize_admin_bar_settings( $data + array( 'remove_by_roles' => array() ) );
		$saved_admin_bar    = (array) get_option( 'swift_control_admin_bar_settings', array() );

		// These have no field on the settings page (import only), keep them.
		foreach ( array( 'showing_intent', 'hiding_intent' ) as $field ) {
			unset( $admin_bar_settings[ $field ] );

			if ( isset( $saved_admin_bar[ $field ] ) ) {
				$admin_bar_settings[ $field ] = $saved_admin_bar[ $field ];
			}
		}

		update_option( 'swift_control_admin_bar_settings', $admin_bar_settings );

		wp_send_json_success( __( 'The settings are saved', 'better-admin-bar' ) );

	}

	/**
	 * Save the position of the quick access panel for the current user.
	 */
	public function save_position() {

		$this->verify( 'save_position' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Cast to numbers / compared below.
		$position = array(
			'x'            => isset( $_POST['x'] ) ? (float) wp_unslash( $_POST['x'] ) : 0,
			'x_direction'  => isset( $_POST['x_direction'] ) && 'right' === wp_unslash( $_POST['x_direction'] ) ? 'right' : 'left',
			'y'            => isset( $_POST['y'] ) ? (float) wp_unslash( $_POST['y'] ) : 0,
			'y_direction'  => isset( $_POST['y_direction'] ) && 'top' === wp_unslash( $_POST['y_direction'] ) ? 'top' : 'bottom',
			'y_percentage' => isset( $_POST['y_percentage'] ) ? (float) wp_unslash( $_POST['y_percentage'] ) : 0,
		);
		// phpcs:enable

		update_user_meta( get_current_user_id(), 'swift_control_position', $position );

		wp_send_json_success( __( 'Widget position is saved', 'better-admin-bar' ) );

	}

}
