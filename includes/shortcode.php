<?php
/**
 * [company_lookup] shortcode.
 *
 * @package ScoreCompanyLookup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue front-end assets only when the shortcode or block is present.
 */
function scl_enqueue_assets() {
	wp_register_style(
		'scl-style',
		SCL_PLUGIN_URL . 'assets/css/style.css',
		array(),
		SCL_VERSION
	);

	wp_register_script(
		'scl-script',
		SCL_PLUGIN_URL . 'assets/js/lookup.js',
		array(),
		SCL_VERSION,
		true
	);

	wp_localize_script( 'scl-script', 'sclData', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'scl_search_nonce' ),
		'i18n'    => array(
			'searching'  => __( 'Searching...', 'score-company-lookup' ),
			'noResults'  => __( 'No companies found. Try a different search term.', 'score-company-lookup' ),
			'error'      => __( 'An error occurred. Please try again.', 'score-company-lookup' ),
			'minChars'   => __( 'Please enter at least 2 characters.', 'score-company-lookup' ),
			'name'       => __( 'Company Name', 'score-company-lookup' ),
			'country'    => __( 'Country', 'score-company-lookup' ),
			'revenue'    => __( 'Revenue', 'score-company-lookup' ),
			'employees'  => __( 'Employees', 'score-company-lookup' ),
			'score'      => __( 'Score', 'score-company-lookup' ),
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'scl_enqueue_assets' );

/**
 * Render the [company_lookup] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function scl_shortcode_render( $atts ) {
	$atts = shortcode_atts( array(
		'placeholder' => __( 'Search for a company...', 'score-company-lookup' ),
		'limit'       => 5,
	), $atts, 'company_lookup' );

	// Enqueue assets when shortcode is actually used.
	wp_enqueue_style( 'scl-style' );
	wp_enqueue_script( 'scl-script' );

	ob_start();
	?>
	<div class="scl-wrapper" data-limit="<?php echo absint( $atts['limit'] ); ?>">
		<form class="scl-form" onsubmit="return false;">
			<div class="scl-input-group">
				<input
					type="search"
					class="scl-search-input"
					placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>"
					aria-label="<?php esc_attr_e( 'Company search', 'score-company-lookup' ); ?>"
					autocomplete="off"
					minlength="2"
				/>
				<button type="submit" class="scl-search-btn" aria-label="<?php esc_attr_e( 'Search', 'score-company-lookup' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
					<span><?php esc_html_e( 'Search', 'score-company-lookup' ); ?></span>
				</button>
			</div>
		</form>
		<div class="scl-status" role="status" aria-live="polite"></div>
		<div class="scl-results"></div>
		<p class="scl-powered-by">
			<?php
			printf(
				/* translators: %s: link to SCALA Score */
				esc_html__( 'Powered by %s', 'score-company-lookup' ),
				'<a href="https://score.get-scala.com" target="_blank" rel="noopener noreferrer">SCALA Score</a>'
			);
			?>
			&mdash;
			<?php esc_html_e( '250M+ company records', 'score-company-lookup' ); ?>
		</p>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'company_lookup', 'scl_shortcode_render' );
