<?php
/**
 * Better Admin Bar PRO box.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<div class="heatbox pro-widgets-box">
	<h2><?php esc_html_e( 'Better Admin Bar', 'better-admin-bar' ); ?> <span class="heatbox-badge">PRO</span></h2>
	<div class="heatbox-content">
		<p class="description"><?php esc_html_e( 'Get Better Admin Bar PRO & provide your clients with the user experience they deserve.', 'better-admin-bar' ); ?></p>
		<ul class="heatbox-features">
			<?php foreach ( swift_control_get_locked_widget_settings() as $swift_control_pro_widget ) : ?>
				<li>
					<span class="dashicons dashicons-yes" aria-hidden="true"></span>
					<div>
						<h3><?php echo esc_html( $swift_control_pro_widget['text'] ); ?></h3>
						<p class="description"><?php echo esc_html( $swift_control_pro_widget['description'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="heatbox-margin-top">
			<a href="https://betteradminbar.com/pricing/?utm_source=bab&amp;utm_medium=settings&amp;utm_campaign=bab" target="_blank" class="button button-primary button-full">
				<?php esc_html_e( 'Learn more', 'better-admin-bar' ); ?>
				<span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'better-admin-bar' ); ?></span>
			</a>
		</div>
	</div>
</div>
