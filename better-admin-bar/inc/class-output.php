<?php
/**
 * Frontend output: the quick access panel and the admin bar settings.
 *
 * @package Better_Admin_Bar
 */

namespace SwiftControl;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Frontend output.
 */
class Output {

	/**
	 * Register the action & filter hooks.
	 */
	public function __construct() {

		add_action( 'wp', array( $this, 'check_page' ) );
		add_action( 'wp', array( $this, 'remove_admin_bar' ) );
		add_action( 'wp_head', array( $this, 'admin_bar_wp_head' ), PHP_INT_MAX );
		add_action( 'wp_footer', array( $this, 'admin_bar_output' ), 15 );

	}

	/**
	 * Check if the admin bar settings apply on this page.
	 *
	 * Not when the admin bar is removed, turned off in the user's profile or in the customizer preview.
	 *
	 * @return bool
	 */
	private function admin_bar_is_styled() {

		return ! is_customize_preview() && is_admin_bar_showing();

	}

	/**
	 * Show the quick access panel to logged-in editors & admins, if it has active widgets.
	 */
	public function check_page() {

		if ( ! is_user_logged_in() || ! current_user_can( swift_control_capability() ) ) {
			return;
		}

		if ( is_customize_preview() || swift_control_is_inside_page_builder() ) {
			return;
		}

		// Nothing to show: don't load the panel's files.
		if ( ! swift_control_has_active_widgets() || swift_control_get_display_settings()['disable_swift_control'] ) {
			return;
		}

		if ( ! apply_filters( 'swift_control_frontend_display', true ) ) {
			return;
		}

		add_filter( 'body_class', array( $this, 'add_body_class' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_styles' ) );
		add_action( 'wp_footer', array( $this, 'quick_panel_output' ), 5 );

	}

	/**
	 * Add a class to the body.
	 *
	 * @param array $classes The body classes.
	 *
	 * @return array The body classes.
	 */
	public function add_body_class( $classes ) {

		$classes[] = 'has-swift-control';

		return $classes;

	}

	/**
	 * Remove the admin bar on the frontend for the selected roles.
	 */
	public function remove_admin_bar() {

		$selected_roles = swift_control_get_admin_bar_settings()['remove_by_roles'];

		if ( empty( $selected_roles ) ) {
			return;
		}

		if ( ! in_array( 'all', $selected_roles, true ) && ! array_intersect( (array) wp_get_current_user()->roles, $selected_roles ) ) {
			return;
		}

		Setup::$remove_admin_bar = true;

		add_filter( 'show_admin_bar', '__return_false' );

	}

	/**
	 * Print the admin bar styles.
	 *
	 * Some checks need to compare with '' (empty string) on purpose, an empty field means "use the default".
	 * Don't change them to empty() checks.
	 *
	 * Test any change to these rules on desktop and small screens, with "Hide Admin Bar on screens smaller than"
	 * below and above 782px (the WordPress breakpoint), and with both combined.
	 */
	public function admin_bar_wp_head() {

		if ( ! $this->admin_bar_is_styled() ) {
			return;
		}

		$settings            = swift_control_get_admin_bar_settings();
		$inactive_opacity    = '' !== $settings['inactive_opacity'] ? $settings['inactive_opacity'] : 100;
		$active_opacity      = '' !== $settings['active_opacity'] ? $settings['active_opacity'] : 100;
		$transition_duration = '' !== $settings['transition_duration'] ? $settings['transition_duration'] : 500;

		ob_start();

		// The fix for overflowing items doesn't conflict with the other rules.
		if ( $settings['fix_menu_item_overflow'] ) {
			echo file_get_contents( SWIFT_CONTROL_PLUGIN_DIR . '/assets/css/fix-items-overflow.css' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Our own CSS file.
		}
		?>
		html #wpadminbar {
			-webkit-transition: all <?php echo esc_attr( $transition_duration / 1000 ); ?>s ease;
			transition: all <?php echo esc_attr( $transition_duration / 1000 ); ?>s ease;
		}

		body {
			-webkit-transition: all <?php echo esc_attr( $transition_duration / 1000 ); ?>s ease;
			transition: all <?php echo esc_attr( $transition_duration / 1000 ); ?>s ease;
		}
		<?php
		if ( $settings['remove_top_gap'] ) {
			?>
			html,
			html body,
			* html body {
				margin-top: -32px;
			}

			@media screen and (max-width: 782px) {
				html,
				html body,
				* html body {
					margin-top: -46px;
				}
			}
			<?php
		}

		// The hover opacity works without the normal opacity.
		if ( '' !== $settings['active_opacity'] ) {
			$this->print_active_opacity( $active_opacity );
		}

		if ( '' !== $settings['inactive_opacity'] ) {
			?>
			html:not(.auto-hide-admin-bar) #wpadminbar {
				opacity: <?php echo esc_attr( $inactive_opacity / 100 ); ?>;
			}

			html.auto-hide-admin-bar #wpadminbar {
				opacity: <?php echo esc_attr( $inactive_opacity / 100 ); ?>;
			}
			<?php
			// No hover opacity: keep the normal opacity on hover.
			if ( '' === $settings['active_opacity'] ) {
				$this->print_active_opacity( $inactive_opacity );
			}
		}

		if ( $settings['auto_hide'] ) {
			?>
			html,
			html body,
			* html body {
				margin-top: -32px;
			}

			html #wpadminbar {
				top: -32px;
			}

			html.show-wpadminbar,
			html.show-wpadminbar body,
			* html.show-wpadminbar body {
				margin-top: 0;
			}

			html.show-wpadminbar #wpadminbar {
				top: 0;
			}

			@media screen and (max-width: 782px) {
				html,
				html body,
				* html body {
					margin-top: -46px;
				}

				html #wpadminbar {
					top: -46px;
				}

				html.show-wpadminbar,
				html.show-wpadminbar body,
				* html.show-wpadminbar body {
					margin-top: 0;
				}

				html.show-wpadminbar #wpadminbar {
					top: 0;
				}
			}
			<?php
			if ( $settings['remove_top_gap'] ) {
				?>
				html.show-wpadminbar,
				html.show-wpadminbar body,
				* html.show-wpadminbar body {
					margin-top: -32px;
				}

				@media screen and (max-width: 782px) {
					html.show-wpadminbar,
					html.show-wpadminbar body,
					* html.show-wpadminbar body {
						margin-top: -46px;
					}
				}
				<?php
			}
		}

		// Hide the admin bar on screens smaller than "hide_below_screen_width".
		if ( '' !== $settings['hide_below_screen_width'] ) {
			$screen_max_width = $settings['hide_below_screen_width'] - 1;
			?>
			@media (max-width: <?php echo esc_attr( $screen_max_width ); ?>px) {
				html #wpadminbar {
					padding: 0;
					height: 0;
					display: none !important;
				}
			}
			<?php
			if ( $screen_max_width <= 782 ) {
				?>
				@media (max-width: <?php echo esc_attr( $screen_max_width ); ?>px) {
					html body,
					html.show-wpadminbar body,
					* html.show-wpadminbar body {
						margin-top: -46px;
					}
				}
				<?php
			} else {
				?>
				@media (max-width: <?php echo esc_attr( $screen_max_width ); ?>px) {
					html body,
					html.show-wpadminbar body,
					* html.show-wpadminbar body {
						margin-top: -32px;
					}
				}

				@media (max-width: 782px) {
					html body,
					html.show-wpadminbar body,
					* html.show-wpadminbar body {
						margin-top: -46px;
					}
				}
				<?php
			}
		}

		echo '<style type="text/css">' . ob_get_clean() . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.

	}

	/**
	 * Print the admin bar opacity on hover.
	 *
	 * @param int $opacity The opacity in percent.
	 */
	private function print_active_opacity( $opacity ) {
		?>
		html:not(.auto-hide-admin-bar) #wpadminbar:hover {
			opacity: <?php echo esc_attr( $opacity / 100 ); ?>;
		}

		html.auto-hide-admin-bar.show-wpadminbar #wpadminbar {
			opacity: <?php echo esc_attr( $opacity / 100 ); ?>;
		}
		<?php
	}

	/**
	 * Enqueue the frontend assets.
	 */
	public function frontend_scripts() {

		wp_enqueue_style( 'swift-control', SWIFT_CONTROL_PLUGIN_URL . '/assets/css/swift-control.css', array(), SWIFT_CONTROL_PLUGIN_VERSION );

		wp_enqueue_script( 'interact', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/interact.min.js', array(), SWIFT_CONTROL_PLUGIN_VERSION, true );
		wp_enqueue_script( 'swift-control', SWIFT_CONTROL_PLUGIN_URL . '/assets/js/swift-control.js', array( 'jquery', 'interact' ), SWIFT_CONTROL_PLUGIN_VERSION, true );

	}

	/**
	 * Output the quick access panel.
	 */
	public function quick_panel_output() {

		swift_control_quick_access_panel();

	}

	/**
	 * Output the auto-hide script of the admin bar.
	 */
	public function admin_bar_output() {

		if ( ! $this->admin_bar_is_styled() ) {
			return;
		}

		$settings = swift_control_get_admin_bar_settings();

		if ( ! $settings['auto_hide'] ) {
			return;
		}

		$options = array(
			'showingIntent'   => $settings['showing_intent'] ? $settings['showing_intent'] : 500,
			'hidingIntent'    => $settings['hiding_intent'] ? $settings['hiding_intent'] : 1250,
			'transitionDelay' => $settings['hiding_transition_delay'] ? $settings['hiding_transition_delay'] : 1500,
		);
		?>
		<script>
		var swiftControlAdminBarOpts = <?php echo wp_json_encode( $options ); ?>;
		<?php echo file_get_contents( SWIFT_CONTROL_PLUGIN_DIR . '/assets/js/admin-bar.js' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Our own script file. ?>
		</script>
		<?php

	}

	/**
	 * Add the color settings as inline styles of the quick access panel.
	 */
	public static function inline_styles() {

		$colors   = swift_control_get_color_settings();
		$defaults = swift_control_get_default_color_settings();
		$rules    = array(
			'setting_button_bg_color'   => array(
				'.swift-control-widgets .swift-control-widget-setting .swift-control-widget-link, .swift-control-widgets .swift-control-widget-setting .swift-control-widget-link:hover' => 'background-color',
				'.swift-control-widgets .swift-control-widget-setting::after' => 'color',
			),
			'setting_button_icon_color' => array(
				'.swift-control-widgets .swift-control-widget-setting a, .swift-control-widgets .swift-control-widget-setting a:hover' => 'color',
			),
			'widget_bg_color_hover'     => array(
				'.swift-control-widgets .swift-control-widget-link:hover' => 'background-color',
			),
			'widget_bg_color'           => array(
				'.swift-control-widgets .swift-control-widget-link' => 'background-color',
				'.swift-control-widgets .is-disabled .swift-control-widget-link, .swift-control-widgets .is-disabled .swift-control-widget-link:hover' => 'background-color',
			),
			'widget_icon_color'         => array(
				'.swift-control-widgets .swift-control-widget-link, .swift-control-widgets .is-disabled .swift-control-widget-link:hover' => 'color',
			),
		);
		$css      = '';

		foreach ( $rules as $color_key => $selectors ) {
			if ( $colors[ $color_key ] === $defaults[ $color_key ] ) {
				continue;
			}

			foreach ( $selectors as $selector => $property ) {
				$css .= $selector . ' {' . $property . ': ' . $colors[ $color_key ] . ';}';
			}
		}

		$icon_size = swift_control_get_icon_size();

		if ( swift_control_icon_size_limits()['default'] !== $icon_size ) {
			$css .= '.swift-control-widgets {--swift-control-icon-size: ' . absint( $icon_size ) . 'px;}';
		}

		wp_add_inline_style( 'swift-control', $css );

	}

}
