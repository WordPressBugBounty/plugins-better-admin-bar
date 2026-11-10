<?php
/**
 * Admin bar settings boxes.
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$swift_control_admin_bar = swift_control_get_admin_bar_settings();
$swift_control_hide_all  = in_array( 'all', $swift_control_admin_bar['remove_by_roles'], true );
?>

<div class="heatbox admin-bar-settings-box">
	<h2><?php esc_html_e( 'Remove Admin Bar', 'better-admin-bar' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Remove it for', 'better-admin-bar' ); ?>
				<p class="description"><?php esc_html_e( 'Removes the admin bar from the frontend of your website. Nothing selected: everyone sees it.', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><?php esc_html_e( 'Remove it for', 'better-admin-bar' ); ?></legend>
					<div class="setting-fields">
						<div class="setting-field">
							<label class="label checkbox-label">
								<?php esc_html_e( 'All users', 'better-admin-bar' ); ?>
								<input type="checkbox" name="remove_by_roles[]" value="all" id="remove_by_roles_all" class="remove-by-roles-all" <?php checked( $swift_control_hide_all ); ?>>
								<div class="indicator"></div>
							</label>
						</div>
						<?php foreach ( swift_control_get_roles() as $swift_control_role => $swift_control_role_name ) : ?>
							<div class="setting-field">
								<label class="label checkbox-label">
									<?php echo esc_html( translate_user_role( $swift_control_role_name ) ); ?>
									<input type="checkbox" name="remove_by_roles[]" value="<?php echo esc_attr( $swift_control_role ); ?>" class="remove-by-roles-role" <?php checked( $swift_control_hide_all || in_array( $swift_control_role, $swift_control_admin_bar['remove_by_roles'], true ) ); ?> <?php disabled( $swift_control_hide_all ); ?>>
									<div class="indicator"></div>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</td>
		</tr>
	</table>
</div>

<div class="heatbox admin-bar-settings-box" data-show-if-field-unchecked="remove_by_roles_all">
	<h2><?php esc_html_e( 'Auto-Hide Admin Bar', 'better-admin-bar' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Auto-hide', 'better-admin-bar' ); ?>
				<p class="description"><?php esc_html_e( 'The admin bar reappears when you hover over the top of the browser window.', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<label class="label checkbox-label">
					<?php esc_html_e( 'Auto-hide the admin bar', 'better-admin-bar' ); ?>
					<input type="checkbox" name="auto_hide" id="auto_hide" value="1" class="general-setting-field" <?php checked( $swift_control_admin_bar['auto_hide'], 1 ); ?>>
					<div class="indicator"></div>
				</label>
			</td>
		</tr>
		<tr data-show-if-field-checked="auto_hide">
			<th scope="row">
				<label for="hiding_transition_delay"><?php esc_html_e( 'Delay', 'better-admin-bar' ); ?></label>
				<p class="description"><?php esc_html_e( 'How long the admin bar stays after the mouse leaves it. Default: 1500ms', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<input type="number" name="hiding_transition_delay" id="hiding_transition_delay" class="small-text general-setting-field" min="0" step="50" placeholder="1500" value="<?php echo esc_attr( $swift_control_admin_bar['hiding_transition_delay'] ); ?>">
				<span class="heatbox-unit">ms</span>
			</td>
		</tr>
		<tr data-show-if-field-checked="auto_hide">
			<th scope="row">
				<label for="transition_duration"><?php esc_html_e( 'Animation speed', 'better-admin-bar' ); ?></label>
				<p class="description"><?php esc_html_e( 'Default: 500ms', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<input type="number" name="transition_duration" id="transition_duration" class="small-text general-setting-field" min="0" step="50" placeholder="500" value="<?php echo esc_attr( $swift_control_admin_bar['transition_duration'] ); ?>">
				<span class="heatbox-unit">ms</span>
			</td>
		</tr>
	</table>
</div>

<div class="heatbox admin-bar-settings-box" data-show-if-field-unchecked="remove_by_roles_all">
	<h2><?php esc_html_e( 'Display Settings', 'better-admin-bar' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Top gap', 'better-admin-bar' ); ?>
				<p class="description"><?php esc_html_e( 'The admin bar adds a 32px gap to the top of your website.', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<label class="label checkbox-label">
					<?php esc_html_e( 'Remove the admin bar gap', 'better-admin-bar' ); ?>
					<input type="checkbox" name="remove_top_gap" id="remove_top_gap" value="1" class="general-setting-field" <?php checked( $swift_control_admin_bar['remove_top_gap'], 1 ); ?>>
					<div class="indicator"></div>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Menu items', 'better-admin-bar' ); ?>
				<p class="description"><?php esc_html_e( 'With many menu items, the admin bar can break into a second line on smaller screens.', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<label class="label checkbox-label">
					<?php esc_html_e( 'Fix overflowing menu items', 'better-admin-bar' ); ?>
					<input type="checkbox" name="fix_menu_item_overflow" id="fix_menu_item_overflow" value="1" class="general-setting-field" <?php checked( $swift_control_admin_bar['fix_menu_item_overflow'], 1 ); ?>>
					<div class="indicator"></div>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="hide_below_screen_width"><?php esc_html_e( 'Hide on small screens', 'better-admin-bar' ); ?></label>
				<p class="description"><?php esc_html_e( 'Hides the admin bar on screens smaller than this. Empty: never.', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<input type="number" name="hide_below_screen_width" id="hide_below_screen_width" class="small-text general-setting-field" min="0" value="<?php echo esc_attr( $swift_control_admin_bar['hide_below_screen_width'] ); ?>">
				<span class="heatbox-unit">px</span>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="inactive_opacity"><?php esc_html_e( 'Opacity', 'better-admin-bar' ); ?></label>
				<p class="description"><?php esc_html_e( 'Default: 100%', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<input type="number" name="inactive_opacity" id="inactive_opacity" class="small-text general-setting-field" min="0" max="100" placeholder="100" value="<?php echo esc_attr( $swift_control_admin_bar['inactive_opacity'] ); ?>">
				<span class="heatbox-unit">%</span>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="active_opacity"><?php esc_html_e( 'Opacity on hover', 'better-admin-bar' ); ?></label>
				<p class="description"><?php esc_html_e( 'Empty: same as the opacity above.', 'better-admin-bar' ); ?></p>
			</th>
			<td>
				<input type="number" name="active_opacity" id="active_opacity" class="small-text general-setting-field" min="0" max="100" value="<?php echo esc_attr( $swift_control_admin_bar['active_opacity'] ); ?>">
				<span class="heatbox-unit">%</span>
			</td>
		</tr>
	</table>
</div>
