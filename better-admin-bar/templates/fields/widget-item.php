<?php
/**
 * A widget on the settings page (active or available).
 *
 * Included from swift_control_settings_output_widget_item().
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$swift_control_field_id = 'swift_control_' . $widget_key;
?>

<li class="widget-item" data-widget-key="<?php echo esc_attr( $widget_key ); ?>">
	<span class="drag-handle" aria-hidden="true"></span>
	<div class="widget-icon dblclick-trigger">
		<?php echo swift_control_icon_svg( $parsed_settings['icon_class'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Our own SVG markup. ?>
		<button type="button" class="icon-picker blur-trigger" aria-label="<?php esc_attr_e( 'Change icon', 'better-admin-bar' ); ?>"></button>
	</div>
	<input type="text" name="<?php echo esc_attr( $swift_control_field_id ); ?>_text" class="text-field widget-text-field dblclick-trigger" value="<?php echo esc_attr( $parsed_settings['text'] ); ?>" aria-label="<?php esc_attr_e( 'Name', 'better-admin-bar' ); ?>" readonly>
	<?php if ( $has_redirect || $has_new_tab ) : ?>
		<div class="widget-extra-settings edit-mode-control">
			<?php if ( $has_redirect ) : ?>
				<input type="url" id="<?php echo esc_attr( $swift_control_field_id ); ?>_redirect_url" class="text-field redirect-url-field" value="<?php echo esc_attr( $parsed_settings['redirect_url'] ); ?>" placeholder="<?php esc_attr_e( 'Redirect URL', 'better-admin-bar' ); ?>" aria-label="<?php esc_attr_e( 'Redirect URL', 'better-admin-bar' ); ?>">
			<?php endif; ?>
			<?php if ( $has_new_tab ) : ?>
				<label class="label checkbox-label blur-trigger">
					<?php esc_html_e( 'New tab', 'better-admin-bar' ); ?>
					<input type="checkbox" id="<?php echo esc_attr( $swift_control_field_id ); ?>_new_tab" value="1" class="new-tab-field" <?php checked( $parsed_settings['new_tab'], 1 ); ?>>
					<div class="indicator"></div>
				</label>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<button type="button" class="button edit-button"><?php esc_html_e( 'Edit', 'better-admin-bar' ); ?></button>
</li>
