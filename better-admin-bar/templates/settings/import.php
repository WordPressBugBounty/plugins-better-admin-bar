<?php
/**
 * Import box.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox import-widgets-box">
	<h2><?php esc_html_e( 'Import', 'better-admin-bar' ); ?></h2>
	<div class="heatbox-content">
		<form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=better-admin-bar#tools' ) ); ?>" enctype="multipart/form-data">
			<input type="hidden" name="swift_control_action" value="import">
			<?php wp_nonce_field( 'swift_control_import_widgets', 'swift_control_import_nonce' ); ?>
			<p class="description"><?php esc_html_e( 'Select a .json file exported from Better Admin Bar. It replaces your current settings.', 'better-admin-bar' ); ?></p>
			<p>
				<label class="screen-reader-text" for="swift_control_import_file"><?php esc_html_e( 'Select a file', 'better-admin-bar' ); ?></label>
				<input type="file" name="swift_control_import_file" id="swift_control_import_file" accept=".json,application/json">
			</p>
			<button type="submit" class="button button-primary">
				<span class="dashicons dashicons-upload" aria-hidden="true"></span>
				<?php esc_html_e( 'Import File', 'better-admin-bar' ); ?>
			</button>
		</form>
	</div>
</div>
