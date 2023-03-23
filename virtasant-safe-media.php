<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/adnanhyder/
 * @since             1.0.0
 * @package           Virtasant_Safe_Media
 *
 * @wordpress-plugin
 * Plugin Name:       Virtasant Safe Media
 * Plugin URI:        https://#
 * Description:       Important things to consider, - Git Commits – Make use of proper git commits and commit messages to ensure a healthy git history. - Performance – Consider performance aspects, assume the plugin will be used in high traffic sites. - Edge Cases - Best Practices - Code Quality
 * Version:           1.0.0
 * Author:            Adnan
 * Author URI:        https://profiles.wordpress.org/adnanhyder/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       virtasant-safe-media
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VIRTASANT_SAFE_MEDIA_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-virtasant-safe-media-activator.php
 */
function activate_virtasant_safe_media() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-virtasant-safe-media-activator.php';
	Virtasant_Safe_Media_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-virtasant-safe-media-deactivator.php
 */
function deactivate_virtasant_safe_media() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-virtasant-safe-media-deactivator.php';
	Virtasant_Safe_Media_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_virtasant_safe_media' );
register_deactivation_hook( __FILE__, 'deactivate_virtasant_safe_media' );


/*
 * The library CMB2 activation, including in Plugin,
 * */

require_once  plugin_dir_path( __FILE__ ) . '/includes/cmb2/init.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

require plugin_dir_path( __FILE__ ) . 'includes/class-virtasant-safe-media.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_virtasant_safe_media() {

	$plugin = new Virtasant_Safe_Media();
	$plugin->run();

}
run_virtasant_safe_media();
