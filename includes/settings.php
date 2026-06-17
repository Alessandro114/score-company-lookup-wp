<?php
/**
 * Settings page: Settings > Score API.
 *
 * @package ScoreCompanyLookup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the settings page under Settings.
 */
function scl_add_settings_page() {
	add_options_page(
		__( 'Score API Settings', 'score-company-lookup' ),
		__( 'Score API', 'score-company-lookup' ),
		'manage_options',
		'score-api-settings',
		'scl_render_settings_page'
	);
}
add_action( 'admin_menu', 'scl_add_settings_page' );

/**
 * Register settings, section, and fields.
 */
function scl_register_settings() {
	register_setting(
		'scl_settings_group',
		'scl_api_endpoint',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'scl_sanitize_endpoint',
			'default'           => 'https://score.get-scala.com/api',
		)
	);

	add_settings_section(
		'scl_main_section',
		__( 'API Configuration', 'score-company-lookup' ),
		'scl_section_description',
		'score-api-settings'
	);

	add_settings_field(
		'scl_api_endpoint_field',
		__( 'API Endpoint', 'score-company-lookup' ),
		'scl_endpoint_field_render',
		'score-api-settings',
		'scl_main_section'
	);
}
add_action( 'admin_init', 'scl_register_settings' );

/**
 * Sanitize the endpoint URL.
 *
 * @param string $value Raw input.
 * @return string Sanitised URL (trailing slash stripped).
 */
function scl_sanitize_endpoint( $value ) {
	$value = esc_url_raw( $value );
	return untrailingslashit( $value );
}

/**
 * Section description callback.
 */
function scl_section_description() {
	echo '<p>' . esc_html__( 'Configure the Score API endpoint used for company lookups.', 'score-company-lookup' ) . '</p>';
}

/**
 * Render the endpoint input field.
 */
function scl_endpoint_field_render() {
	$value = get_option( 'scl_api_endpoint', 'https://score.get-scala.com/api' );
	printf(
		'<input type="url" id="scl_api_endpoint" name="scl_api_endpoint" value="%s" class="regular-text" placeholder="https://score.get-scala.com/api" />',
		esc_attr( $value )
	);
	echo '<p class="description">' . esc_html__( 'The base URL of the Score API (without trailing slash).', 'score-company-lookup' ) . '</p>';
}

/**
 * Render the settings page.
 */
function scl_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'scl_settings_group' );
			do_settings_sections( 'score-api-settings' );
			submit_button( __( 'Save Settings', 'score-company-lookup' ) );
			?>
		</form>
		<hr />
		<h2><?php esc_html_e( 'Usage', 'score-company-lookup' ); ?></h2>
		<p><?php esc_html_e( 'Add the search form to any post or page with the shortcode:', 'score-company-lookup' ); ?></p>
		<code>[company_lookup]</code>
		<p><?php esc_html_e( 'Or use the "Company Score Lookup" block in the Gutenberg editor.', 'score-company-lookup' ); ?></p>
	</div>
	<?php
}
