<?php
/**
 * Setup: settings page, admin assets & the quick access panel preview.
 *
 * @package Better_Admin_Bar
 */

namespace SwiftControl;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Setup Better Admin Bar.
 */
class Setup {

	/**
	 * Whether the admin bar is removed for the current user (kept here, other code may read it).
	 *
	 * @var bool
	 */
	public static $remove_admin_bar = false;

	/**
	 * Hook in once all plugins are loaded.
	 */
	public static function init() {

		add_action( 'plugins_loaded', array( new self(), 'setup' ) );

	}

	/**
	 * Register the action & filter hooks.
	 */
	public function setup() {

		// Better Admin Bar PRO replaces the free version.
		if ( defined( 'SWIFT_CONTROL_PRO_PLUGIN_URL' ) ) {
			return;
		}

		add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 999 );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ), 20 );
		add_action( 'admin_footer', array( $this, 'quick_panel_admin_preview' ) );

		new Output();
		new Save_Settings();
		new Tools();

	}

	/**
	 * Check if we're on the settings page.
	 *
	 * @return bool
	 */
	private function is_settings_page() {

		$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		return $current_screen && 'settings_page_better-admin-bar' === $current_screen->id;

	}

	/**
	 * Add settings link to plugin list page.
	 *
	 * @param array  $actions     An array of plugin action links.
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 *
	 * @return array The modified plugin action links.
	 */
	public function add_settings_link( $actions, $plugin_file ) {

		if ( SWIFT_CONTROL_PLUGIN_BASENAME === $plugin_file && current_user_can( swift_control_capability() ) ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=better-admin-bar' ) ) . '">' . esc_html__( 'Settings', 'better-admin-bar' ) . '</a>';

			array_unshift( $actions, $settings_link );
		}

		return $actions;

	}

	/**
	 * Add submenu under "Settings" menu item.
	 */
	public function add_submenu_page() {

		add_options_page(
			__( 'Better Admin Bar', 'better-admin-bar' ),
			__( 'Better Admin Bar', 'better-admin-bar' ),
			swift_control_capability(),
			'better-admin-bar',
			array( $this, 'page_output' )
		);

	}

	/**
	 * Settings page output.
	 */
	public function page_output() {

		require SWIFT_CONTROL_PLUGIN_DIR . '/templates/admin-page.php';

	}

	/**
	 * Enqueue the settings page styles & scripts.
	 */
	public function admin_scripts() {

		if ( ! $this->is_settings_page() ) {
			return;
		}

		wp_enqueue_style( 'heatbox', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/heatbox.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );
		wp_enqueue_style( 'swift-control-icon-picker', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/icon-picker.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );
		wp_enqueue_style( 'swift-control-admin', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/swift-control-admin.css', array( 'heatbox' ), SWIFT_CONTROL_PLUGIN_VERSION );
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script( 'swift-control-icon-picker', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/icon-picker.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		// The picker loads the icons when it's opened the first time (they're big).
		wp_localize_script(
			'swift-control-icon-picker',
			'SwiftControlIcons',
			array(
				'iconsUrl' => add_query_arg( 'ver', SWIFT_CONTROL_PLUGIN_VERSION, SWIFT_CONTROL_PLUGIN_URL . '/assets/icons/lucide.json' ),
				'tagsUrl'  => add_query_arg( 'ver', SWIFT_CONTROL_PLUGIN_VERSION, SWIFT_CONTROL_PLUGIN_URL . '/assets/icons/lucide-tags.json' ),
				'labels'   => array(
					'search'  => __( 'Search icons', 'better-admin-bar' ),
					'loading' => __( 'Loading icons…', 'better-admin-bar' ),
					'error'   => __( 'The icons couldn\'t be loaded. Please try again.', 'better-admin-bar' ),
					'none'    => __( 'No icons found.', 'better-admin-bar' ),
				),
			)
		);

		wp_enqueue_script( 'wp-color-picker-alpha', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/wp-color-picker-alpha.js', array( 'wp-color-picker', 'wp-i18n' ), '2.1.3', true );
		wp_enqueue_script( 'swift-control-settings-page', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/settings-page.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );
		wp_enqueue_script( 'swift-control-widget-settings', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/widget-settings.js', array( 'jquery-ui-sortable', 'swift-control-icon-picker' ), SWIFT_CONTROL_PLUGIN_VERSION, true );
		wp_enqueue_script( 'swift-control-general-settings', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/general-settings.js', array( 'jquery', 'wp-color-picker-alpha' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		wp_localize_script(
			'swift-control-widget-settings',
			'SwiftControl',
			array(
				'nonces' => array(
					'changeWidgetsOrder'   => wp_create_nonce( 'change_widgets_order' ),
					'changeWidgetSettings' => wp_create_nonce( 'change_widget_settings' ),
					'saveGeneralSettings'  => wp_create_nonce( 'save_general_settings' ),
				),
				'labels' => array(
					'edit'  => __( 'Edit', 'better-admin-bar' ),
					'save'  => __( 'Save', 'better-admin-bar' ),
					'saved' => __( 'Settings saved', 'better-admin-bar' ),
					'error' => __( 'Something went wrong. Please try again.', 'better-admin-bar' ),
				),
			)
		);

		// The quick access panel preview.
		wp_enqueue_style( 'swift-control', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/swift-control.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );
		wp_enqueue_script( 'swift-control-preview', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/swift-control-preview.js', array( 'jquery' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

		Output::inline_styles();

	}

	/**
	 * Output the quick access panel preview on the settings page.
	 */
	public function quick_panel_admin_preview() {

		if ( $this->is_settings_page() ) {
			swift_control_quick_access_panel( true );
		}

	}

	/**
	 * Add the user & role classes to every admin page, and the heatbox classes to the settings page.
	 *
	 * @param string $classes The class names.
	 *
	 * @return string The class names.
	 */
	public function admin_body_class( $classes ) {

		$current_user = wp_get_current_user();

		// Not sanitize_html_class(): it strips the "%" of encoded non-Latin names and changes the class.
		$classes .= ' swift-control-user-' . esc_attr( $current_user->user_nicename );

		foreach ( (array) $current_user->roles as $role ) {
			$classes .= ' swift-control-role-' . sanitize_html_class( $role );
		}

		if ( $this->is_settings_page() ) {
			$classes .= ' heatbox-admin has-header';
		}

		return $classes;

	}

}
