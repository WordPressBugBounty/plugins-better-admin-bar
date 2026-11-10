<?php
/**
 * Active widgets box: the widgets of the quick access panel.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox active-widgets-box">
	<h2>
		<?php esc_html_e( 'Quick Access Panel', 'better-admin-bar' ); ?>
		<span class="heatbox-tooltip has-image" tabindex="0">
			<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
			<span class="tooltip-content">
				<img src="<?php echo esc_url( SWIFT_CONTROL_PLUGIN_URL . '/assets/img/quick-access-panel.gif' ); ?>" alt="">
				<span><?php esc_html_e( 'The quick access panel is a better way to navigate WordPress. Quickly access all key areas of your website from a convenient navigation panel.', 'better-admin-bar' ); ?></span>
			</span>
		</span>
		<span class="heatbox-status-indicator" role="status"></span>
	</h2>

	<?php // No whitespace inside the list, so it can be styled with :empty. ?>
	<ul id="active-items" class="widget-items active-items" data-empty="<?php esc_attr_e( 'Drag widgets here to add them to the panel.', 'better-admin-bar' ); ?>"><?php array_map( 'swift_control_settings_output_widget_item', swift_control_get_active_widgets() ); ?></ul>

	<div class="heatbox-content swift-control-preview">
		<label class="toggle-switch">
			<input type="checkbox" id="swift_control_preview_toggle" value="1">
			<span class="switch-track"><span class="switch-thumb"></span></span>
		</label>
		<label for="swift_control_preview_toggle"><?php esc_html_e( 'Show a preview of the panel on this page', 'better-admin-bar' ); ?></label>
	</div>
</div>
