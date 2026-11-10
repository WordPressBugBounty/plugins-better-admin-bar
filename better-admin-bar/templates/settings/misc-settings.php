<?php
/**
 * Miscellaneous settings box.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$swift_control_misc = swift_control_get_misc_settings();
?>

<div class="heatbox misc-settings-box">
	<h2><?php esc_html_e( 'Misc', 'better-admin-bar' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'Uninstall', 'better-admin-bar' ); ?></th>
			<td>
				<label class="label checkbox-label">
					<?php esc_html_e( 'Remove all data when the plugin is deleted', 'better-admin-bar' ); ?>
					<input type="checkbox" name="delete_on_uninstall" id="delete_on_uninstall" value="1" class="general-setting-field" <?php checked( ! empty( $swift_control_misc['delete_on_uninstall'] ) ); ?>>
					<div class="indicator"></div>
				</label>
			</td>
		</tr>
	</table>
</div>
