<?php
/**
 * Quick access panel.
 *
 * Included from swift_control_quick_access_panel(), $is_preview tells if it's the preview on the settings page.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$swift_control_widgets        = swift_control_get_active_widgets();
$swift_control_saved_settings = swift_control_get_saved_widget_settings();
$swift_control_transition     = 75;
$swift_control_showing_delay  = 0;
$swift_control_hiding_delay   = ( count( $swift_control_widgets ) - 1 ) * $swift_control_transition;
$swift_control_items          = array();
$swift_control_styles         = '';

foreach ( $swift_control_widgets as $swift_control_widget_key ) {
	$swift_control_parsed = swift_control_parse_widget_settings(
		$swift_control_widget_key,
		! empty( $swift_control_saved_settings[ $swift_control_widget_key ] ) ? $swift_control_saved_settings[ $swift_control_widget_key ] : array()
	);

	$swift_control_items[] = array(
		'key'     => $swift_control_widget_key,
		'class'   => swift_control_parse_widget_class( '', $swift_control_widget_key ),
		'icon'    => $swift_control_parsed['icon_class'],
		'name'    => swift_control_parse_widget_name( $swift_control_parsed['text'], $swift_control_widget_key ),
		'url'     => swift_control_parse_widget_url( $swift_control_parsed['url'], $swift_control_widget_key ) ?: '#',
		'new_tab' => $swift_control_parsed['new_tab'] && 'logout' !== $swift_control_widget_key,
	);

	// Valid keys are letters, digits, "_" and "-" only, safe in CSS as they are (PRO's keys are mixed case).
	if ( swift_control_is_valid_widget_key( $swift_control_widget_key ) ) {
		$swift_control_selector = '[data-widget-key="' . $swift_control_widget_key . '"]';

		$swift_control_styles .= '.swift-control-widgets ' . $swift_control_selector . ' { transition-delay: ' . absint( $swift_control_hiding_delay ) . 'ms; }';
		$swift_control_styles .= '.swift-control-widgets.is-expanded ' . $swift_control_selector . ' { transition-delay: ' . absint( $swift_control_showing_delay ) . 'ms; }';
	}

	$swift_control_showing_delay += $swift_control_transition;
	$swift_control_hiding_delay  -= $swift_control_transition;
}

// The settings button rounds its bottom corner while the first widget fades out.
$swift_control_styles .= '.swift-control-widgets { --swift-control-radius-delay: ' . absint( max( 0, count( $swift_control_widgets ) - 1 ) * $swift_control_transition + 150 ) . 'ms; }';

$swift_control_display = swift_control_get_display_settings();
$swift_control_saved   = get_user_meta( get_current_user_id(), 'swift_control_position', true );
$swift_control_saved   = is_array( $swift_control_saved ) ? $swift_control_saved : array();
$swift_control_pinned  = '';

if ( $is_preview ) {
	$swift_control_position = array(
		'x'            => 0,
		'x_direction'  => is_rtl() ? 'left' : 'right',
		'y'            => 0,
		'y_direction'  => 'bottom',
		'y_percentage' => 0,
	);

	$swift_control_pinned = is_rtl() ? 'is-pinned-left' : 'is-pinned-right';
} else {
	$swift_control_position = array(
		'x'            => isset( $swift_control_saved['x'] ) ? (float) $swift_control_saved['x'] : 0,
		'x_direction'  => isset( $swift_control_saved['x_direction'] ) && 'right' === $swift_control_saved['x_direction'] ? 'right' : 'left',
		'y'            => isset( $swift_control_saved['y'] ) ? (float) $swift_control_saved['y'] : 0,
		'y_direction'  => isset( $swift_control_saved['y_direction'] ) && 'top' === $swift_control_saved['y_direction'] ? 'top' : 'bottom',
		'y_percentage' => isset( $swift_control_saved['y_percentage'] ) ? (float) $swift_control_saved['y_percentage'] : 0,
	);

	if ( ! isset( $swift_control_saved['x_direction'] ) ) {
		// Not moved yet: start on the right in RTL.
		if ( is_rtl() ) {
			$swift_control_position['x_direction'] = 'right';
			$swift_control_pinned                  = 'is-pinned-right';
		}
	} elseif ( 'right' === $swift_control_position['x_direction'] ) {
		$swift_control_pinned = 'is-pinned-right';
	}
}

$swift_control_classes = array( 'swift-control-widgets', 'is-invisible' );

if ( ! $swift_control_display['remove_indicator'] ) {
	$swift_control_classes[] = 'has-arrow';
}

if ( $swift_control_display['expanded'] ) {
	$swift_control_classes[] = 'is-expanded';
}

if ( $swift_control_pinned ) {
	$swift_control_classes[] = $swift_control_pinned;
}
?>

<style class="swift-control-transition-style"><?php echo $swift_control_styles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from sanitized keys and integers only. ?></style>

<?php if ( $is_preview ) : ?>
	<?php foreach ( array_keys( swift_control_get_default_color_settings() ) as $swift_control_color_field ) : ?>
		<style class="swift-control-preview-style" data-field-id="<?php echo esc_attr( $swift_control_color_field ); ?>"></style>
	<?php endforeach; ?>
	<style class="swift-control-preview-transition-style"></style>
<?php endif; ?>

<ul class="<?php echo esc_attr( implode( ' ', $swift_control_classes ) ); ?>">

	<li class="swift-control-widget-item swift-control-widget-setting">
		<a class="swift-control-widget-link" href="<?php echo esc_url( admin_url( 'options-general.php?page=better-admin-bar' ) ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Toggle the quick access panel', 'better-admin-bar' ); ?>">
			<?php echo swift_control_icon_svg( 'settings' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Our own SVG markup. ?>
		</a>
	</li>

	<?php foreach ( $swift_control_items as $swift_control_item ) : ?>
		<li class="swift-control-widget-item <?php echo esc_attr( $swift_control_item['class'] ); ?>" data-widget-key="<?php echo esc_attr( $swift_control_item['key'] ); ?>">
			<a class="swift-control-widget-link" href="<?php echo esc_url( $swift_control_item['url'] ); ?>"<?php echo $swift_control_item['new_tab'] ? ' target="_blank"' : ''; ?> aria-label="<?php echo esc_attr( $swift_control_item['name'] ); ?>">
				<?php echo swift_control_icon_svg( $swift_control_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Our own SVG markup. ?>
			</a>
			<span class="swift-control-widget-title" aria-hidden="true"><?php echo esc_html( $swift_control_item['name'] ); ?></span>
		</li>
	<?php endforeach; ?>

</ul>

<div class="swift-control-helper-panels"></div>

<?php
wp_localize_script(
	$is_preview ? 'swift-control-preview' : 'swift-control',
	'swiftControlOpt',
	array(
		'size'          => 55,
		'settingButton' => array(
			'hidingDelay' => $swift_control_showing_delay + 350,
		),
		'position'      => $swift_control_position,
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'nonce'         => wp_create_nonce( 'save_position' ),
	)
);
