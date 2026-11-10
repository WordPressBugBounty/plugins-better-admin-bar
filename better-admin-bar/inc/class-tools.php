<?php
/**
 * Tools: export & import the settings.
 *
 * @package Better_Admin_Bar
 */

namespace SwiftControl;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Export & import.
 */
class Tools {

	/**
	 * Register the action hooks.
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'process_export' ) );
		add_action( 'admin_init', array( $this, 'process_import' ) );

	}

	/**
	 * The exported options and their sanitize callbacks.
	 *
	 * @return array Sanitize callbacks, keyed by option name.
	 */
	private function options() {

		return array(
			'swift_control_active_widgets'     => 'swift_control_sanitize_widget_keys',
			'swift_control_widget_settings'    => 'swift_control_sanitize_widget_settings',
			'swift_control_display_settings'   => 'swift_control_sanitize_display_settings',
			'swift_control_color_settings'     => 'swift_control_sanitize_color_settings',
			'swift_control_admin_bar_settings' => 'swift_control_sanitize_admin_bar_settings',
			'swift_control_misc_settings'      => function ( $value ) {
				return swift_control_sanitize_flags( $value, swift_control_misc_fields() );
			},
		);

	}

	/**
	 * Check the nonce of a tools form and the capability.
	 *
	 * @param string $action The form action ("export" or "import").
	 *
	 * @return bool Whether the form was sent and may be processed.
	 */
	private function verify( $action ) {

		if ( ! isset( $_POST['swift_control_action'] ) || $action !== $_POST['swift_control_action'] ) {
			return false;
		}

		$nonce = isset( $_POST[ 'swift_control_' . $action . '_nonce' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'swift_control_' . $action . '_nonce' ] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'swift_control_' . $action . '_widgets' ) ) {
			return false;
		}

		return current_user_can( swift_control_capability() );

	}

	/**
	 * Download the settings as a .json file.
	 */
	public function process_export() {

		if ( ! $this->verify( 'export' ) ) {
			return;
		}

		$data = array();

		foreach ( array_keys( $this->options() ) as $option_name ) {
			$data[ $option_name ] = get_option( $option_name, array() );
		}

		// Never saved yet: export the default widgets, not an empty panel.
		$data['swift_control_active_widgets'] = swift_control_get_active_widgets();

		nocache_headers();
		header( 'Content-Disposition: attachment; filename=better-admin-bar-export-' . gmdate( 'Y-m-d-H.i.s' ) . '.json' );
		header( 'Content-Type: application/json; charset=utf-8' );

		echo wp_json_encode( $data );

		exit;

	}

	/**
	 * Import the settings from an uploaded .json file.
	 */
	public function process_import() {

		if ( ! $this->verify( 'import' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Nonce checked in verify(), only the name and the temporary path are used.
		$file = isset( $_FILES['swift_control_import_file'] ) ? $_FILES['swift_control_import_file'] : array();

		if ( empty( $file['tmp_name'] ) || ! empty( $file['error'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			$this->notice( 'swift-control-import-not-exist', __( 'Please upload a file to import', 'better-admin-bar' ) );
			return;
		}

		if ( 'json' !== strtolower( pathinfo( sanitize_file_name( $file['name'] ), PATHINFO_EXTENSION ) ) ) {
			$this->notice( 'swift-control-invalid-import', __( 'Please upload a valid .json file', 'better-admin-bar' ) );
			return;
		}

		$imports = json_decode( (string) file_get_contents( $file['tmp_name'] ), true );
		$options = $this->options();

		if ( ! is_array( $imports ) || ( $imports && ! array_intersect_key( $imports, $options ) ) ) {
			$this->notice( 'swift-control-invalid-import', __( 'This file isn\'t a valid Better Admin Bar export.', 'better-admin-bar' ) );
			return;
		}

		if ( ! array_intersect_key( $imports, $options ) ) {
			$this->notice( 'swift-control-empty-import', __( 'Your import is empty', 'better-admin-bar' ) );
			return;
		}

		foreach ( $options as $option_name => $sanitize_callback ) {
			if ( isset( $imports[ $option_name ] ) ) {
				update_option( $option_name, call_user_func( $sanitize_callback, $imports[ $option_name ] ) );
			}
		}

		$this->notice( 'swift-control-widgets-imported', __( 'Data imported', 'better-admin-bar' ), 'success' );

	}

	/**
	 * Add a notice to the settings page.
	 *
	 * @param string $code    The notice code.
	 * @param string $message The message.
	 * @param string $type    The notice type.
	 */
	private function notice( $code, $message, $type = 'error' ) {

		add_settings_error( 'swift_control_import', $code, $message, $type );

	}

}
