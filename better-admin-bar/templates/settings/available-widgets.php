<?php
/**
 * Available widgets box: widgets that can be dragged into the quick access panel.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox available-widgets-box">
	<h2>
		<?php esc_html_e( 'Widgets', 'better-admin-bar' ); ?>
		<span class="heatbox-tooltip" tabindex="0">
			<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
			<span class="tooltip-content"><?php esc_html_e( 'Drag & drop widgets into the quick access panel to add them.', 'better-admin-bar' ); ?></span>
		</span>
	</h2>

	<?php // No whitespace inside the list, so it can be styled with :empty. ?>
	<ul id="available-items" class="widget-items available-items" data-empty="<?php esc_attr_e( 'All widgets are in use.', 'better-admin-bar' ); ?>"><?php array_map( 'swift_control_settings_output_widget_item', swift_control_get_available_widgets() ); ?></ul>
</div>
