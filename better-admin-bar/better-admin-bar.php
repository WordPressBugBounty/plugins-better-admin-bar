<?php
/**
 * Plugin Name: Better Admin Bar
 * Plugin URI: https://betteradminbar.com/
 * Description: Replace the WordPress admin bar with a quick access panel, auto-hide it or hide it for specific user roles.
 * Version: 5.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: David Vongries
 * Author URI: https://mapsteps.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: better-admin-bar
 *
 * @package Better_Admin_Bar
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Helper constants.
define( 'SWIFT_CONTROL_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'SWIFT_CONTROL_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'SWIFT_CONTROL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SWIFT_CONTROL_PLUGIN_VERSION', '5.0' );

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/icons.php';
require_once __DIR__ . '/inc/class-backwards-compatibility.php';
require_once __DIR__ . '/inc/class-output.php';
require_once __DIR__ . '/inc/class-save-settings.php';
require_once __DIR__ . '/inc/class-tools.php';
require_once __DIR__ . '/inc/class-setup.php';

SwiftControl\Backwards_Compatibility::init();
SwiftControl\Setup::init();
