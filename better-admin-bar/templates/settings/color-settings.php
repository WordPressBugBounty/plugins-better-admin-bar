<?php
/**
 * Color settings box of the quick access panel.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$swift_control_default_colors = swift_control_get_default_color_settings();
$swift_control_colors         = swift_control_get_color_settings();
$swift_control_color_fields   = array(
	'setting_button_bg_color'   => __( 'Accent color', 'better-admin-bar' ),
	'setting_button_icon_color' => __( 'Accent icon color', 'better-admin-bar' ),
	'widget_bg_color'           => __( 'Background color', 'better-admin-bar' ),
	'widget_bg_color_hover'     => __( 'Background color (hover)', 'better-admin-bar' ),
	'widget_icon_color'         => __( 'Icon color', 'better-admin-bar' ),
);
?>

<div class="heatbox color-settings-box">
	<h2><?php esc_html_e( 'Color Settings', 'better-admin-bar' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php foreach ( $swift_control_color_fields as $swift_control_field => $swift_control_label ) : ?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $swift_control_field ); ?>"><?php echo esc_html( $swift_control_label ); ?></label>
					<?php if ( 'setting_button_bg_color' === $swift_control_field ) : ?>
						<p class="description"><?php esc_html_e( 'The button that opens the panel.', 'better-admin-bar' ); ?></p>
					<?php elseif ( 'widget_bg_color' === $swift_control_field ) : ?>
						<p class="description"><?php esc_html_e( 'The widgets in the panel.', 'better-admin-bar' ); ?></p>
					<?php endif; ?>
				</th>
				<td>
					<input type="text" name="<?php echo esc_attr( $swift_control_field ); ?>" id="<?php echo esc_attr( $swift_control_field ); ?>" value="<?php echo esc_attr( $swift_control_colors[ $swift_control_field ] ); ?>" class="color-picker-field general-setting-field has-instant-preview" data-alpha="true" data-default-color="<?php echo esc_attr( $swift_control_default_colors[ $swift_control_field ] ); ?>">
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
