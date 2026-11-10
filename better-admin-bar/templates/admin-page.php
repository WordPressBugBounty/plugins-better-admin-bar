<?php
/**
 * Settings page.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$swift_control_tabs = array(
	'settings'  => __( 'Quick Access Panel', 'better-admin-bar' ),
	'admin-bar' => __( 'Admin Bar', 'better-admin-bar' ),
	'tools'     => __( 'Tools', 'better-admin-bar' ),
);

$swift_control_products = array(
	array(
		'url'         => 'https://wordpress.org/plugins/ultimate-dashboard/',
		'image'       => 'ultimate-dashboard.jpg',
		'title'       => __( 'Ultimate Dashboard', 'better-admin-bar' ),
		'subheadline' => __( 'Fully customize your WordPress dashboard.', 'better-admin-bar' ),
		'text'        => __( 'Ultimate Dashboard is the #1 plugin to create a custom WordPress dashboard for you and your clients. It also comes with multisite support which makes it the perfect plugin for your WaaS network.', 'better-admin-bar' ),
	),
	array(
		'url'         => 'https://wordpress.org/themes/page-builder-framework/',
		'image'       => 'page-builder-framework.jpg',
		'title'       => __( 'Page Builder Framework', 'better-admin-bar' ),
		'subheadline' => __( 'The only theme you\'ll ever need.', 'better-admin-bar' ),
		'text'        => __( 'With its minimalistic design the Page Builder Framework theme is the perfect foundation for your next project. Build blazing fast websites with a theme that is easy to use, lightweight & highly customizable.', 'better-admin-bar' ),
	),
	array(
		'url'         => 'https://wordpress.org/plugins/responsive-youtube-vimeo-popup/',
		'image'       => 'wp-video-popup.jpg',
		'title'       => __( 'WP Video Popup', 'better-admin-bar' ),
		'subheadline' => __( 'The #1 video popup plugin for WordPress.', 'better-admin-bar' ),
		'text'        => __( 'Add a YouTube, Vimeo or Rumble video popup to WordPress. Lightweight, privacy-friendly lightbox that works with any page builder.', 'better-admin-bar' ),
	),
);
?>

<div class="wrap heatbox-wrap swift-control-settings">

	<div class="heatbox-header heatbox-has-tab-nav heatbox-margin-bottom">

		<div class="heatbox-container heatbox-container-center">

			<div class="logo-container">

				<div>
					<h1 class="title">
						<?php echo esc_html( get_admin_page_title() ); ?>
						<span class="version"><?php echo esc_html( SWIFT_CONTROL_PLUGIN_VERSION ); ?></span>
					</h1>
					<p class="subtitle"><?php esc_html_e( 'The WordPress admin bar reimagined.', 'better-admin-bar' ); ?></p>
				</div>

				<div>
					<img src="<?php echo esc_url( SWIFT_CONTROL_PLUGIN_URL . '/assets/img/logo.png' ); ?>" alt="">
				</div>

			</div>

			<nav aria-label="<?php esc_attr_e( 'Settings sections', 'better-admin-bar' ); ?>">
				<ul class="heatbox-tab-nav">
					<?php foreach ( $swift_control_tabs as $swift_control_tab => $swift_control_tab_title ) : ?>
						<li class="heatbox-tab-nav-item" data-panel="<?php echo esc_attr( $swift_control_tab ); ?>">
							<a href="#<?php echo esc_attr( $swift_control_tab ); ?>"><?php echo esc_html( $swift_control_tab_title ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

		</div>

	</div>

	<div class="heatbox-container heatbox-container-center">
		<!-- WordPress places admin notices after this marker -->
		<hr class="wp-header-end">
	</div>

	<?php // The browser's checks would silently block saving for a field on a hidden tab; the values are cleaned on save. ?>
	<form method="post" class="swift-control-settings-form general-settings-area" novalidate>

		<div class="heatbox-admin-panel" data-panel="settings">
			<div class="heatbox-container heatbox-container-center heatbox-column-container">

				<div class="heatbox-main">
					<?php
					require __DIR__ . '/settings/active-widgets.php';
					require __DIR__ . '/settings/display-settings.php';
					require __DIR__ . '/settings/color-settings.php';
					require __DIR__ . '/settings/misc-settings.php';
					require __DIR__ . '/settings/form-footer.php';
					?>
				</div>

				<div class="heatbox-sidebar">
					<?php
					require __DIR__ . '/settings/available-widgets.php';
					require __DIR__ . '/settings/pro-widgets.php';
					require __DIR__ . '/settings/review.php';
					?>
				</div>

			</div>
		</div>

		<div class="heatbox-admin-panel" data-panel="admin-bar">
			<div class="heatbox-container heatbox-container-center">
				<?php
				require __DIR__ . '/settings/admin-bar-settings.php';
				require __DIR__ . '/settings/form-footer.php';
				?>
			</div>
		</div>

	</form>

	<div class="heatbox-admin-panel" data-panel="tools">
		<div class="heatbox-container heatbox-container-center">
			<div class="heatbox-columns">
				<?php
				require __DIR__ . '/settings/export.php';
				require __DIR__ . '/settings/import.php';
				?>
			</div>
		</div>
	</div>

	<div class="heatbox-container heatbox-container-center">
		<div class="heatbox-divider"></div>
	</div>

	<div class="heatbox-container heatbox-container-wide heatbox-container-center heatbox-products">

		<h2><?php esc_html_e( 'Check out our other free WordPress products!', 'better-admin-bar' ); ?></h2>

		<ul class="products">
			<?php foreach ( $swift_control_products as $swift_control_product ) : ?>
				<li class="heatbox">
					<a href="<?php echo esc_url( $swift_control_product['url'] ); ?>" target="_blank" aria-hidden="true" tabindex="-1">
						<img src="<?php echo esc_url( SWIFT_CONTROL_PLUGIN_URL . '/assets/img/' . $swift_control_product['image'] ); ?>" alt="">
					</a>
					<div class="heatbox-content">
						<h3><?php echo esc_html( $swift_control_product['title'] ); ?></h3>
						<p class="subheadline"><?php echo esc_html( $swift_control_product['subheadline'] ); ?></p>
						<p><?php echo esc_html( $swift_control_product['text'] ); ?></p>
						<a href="<?php echo esc_url( $swift_control_product['url'] ); ?>" target="_blank" class="button"><?php esc_html_e( 'Learn more', 'better-admin-bar' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'better-admin-bar' ); ?></span></a>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<p class="credit"><?php esc_html_e( 'Made with ❤ in Torsby, Sweden', 'better-admin-bar' ); ?></p>

	</div>

</div>
