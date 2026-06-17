<?php
/**
 * Plugin Name: Company Score Lookup
 * Plugin URI:  https://github.com/Alessandro114/score-company-lookup-wp
 * Description: Look up company data from 250M+ records. Revenue, employees, credit score.
 * Version:     1.0.0
 * Author:      SCALA
 * Author URI:  https://get-scala.com
 * License:     GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: score-company-lookup
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SCL_VERSION', '1.0.0' );
define( 'SCL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SCL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Load plugin text domain for translations.
 */
function scl_load_textdomain() {
	load_plugin_textdomain(
		'score-company-lookup',
		false,
		dirname( SCL_PLUGIN_BASENAME ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'scl_load_textdomain' );

// Include core files.
require_once SCL_PLUGIN_DIR . 'includes/settings.php';
require_once SCL_PLUGIN_DIR . 'includes/shortcode.php';
require_once SCL_PLUGIN_DIR . 'includes/ajax.php';
require_once SCL_PLUGIN_DIR . 'includes/block.php';

/**
 * Register activation hook — set default options.
 */
function scl_activate() {
	if ( false === get_option( 'scl_api_endpoint' ) ) {
		add_option( 'scl_api_endpoint', 'https://score.get-scala.com/api' );
	}
}
register_activation_hook( __FILE__, 'scl_activate' );

/**
 * Add "Settings" link on the Plugins page.
 *
 * @param array $links Existing links.
 * @return array
 */
function scl_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=score-api-settings' ) ),
		esc_html__( 'Settings', 'score-company-lookup' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . SCL_PLUGIN_BASENAME, 'scl_plugin_action_links' );
