<?php
/**
 * Export box.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox export-widgets-box">
	<h2><?php esc_html_e( 'Export', 'better-admin-bar' ); ?></h2>
	<div class="heatbox-content">
		<form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=better-admin-bar#tools' ) ); ?>">
			<input type="hidden" name="swift_control_action" value="export">
			<?php wp_nonce_field( 'swift_control_export_widgets', 'swift_control_export_nonce' ); ?>
			<p class="description"><?php esc_html_e( 'Download your settings as a .json file to import them on another WordPress site.', 'better-admin-bar' ); ?></p>
			<button type="submit" class="button button-primary">
				<span class="dashicons dashicons-download" aria-hidden="true"></span>
				<?php esc_html_e( 'Export File', 'better-admin-bar' ); ?>
			</button>
		</form>
	</div>
</div>
