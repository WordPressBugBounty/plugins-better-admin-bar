<?php
/**
 * Save button below the settings.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox-form-footer">
	<div>
		<button type="submit" class="button button-primary button-larger save-general-settings">
			<?php esc_html_e( 'Save Changes', 'better-admin-bar' ); ?>
		</button>
		<span class="heatbox-notice swift-control-notice" role="status"></span>
	</div>
</div>
