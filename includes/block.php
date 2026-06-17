<?php
/**
 * Gutenberg block registration.
 *
 * @package ScoreCompanyLookup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Company Score Lookup Gutenberg block.
 */
function scl_register_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Editor-only script.
	wp_register_script(
		'scl-block-editor',
		SCL_PLUGIN_URL . 'src/blocks/company-lookup/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		SCL_VERSION,
		true
	);

	// Editor-only style.
	wp_register_style(
		'scl-block-editor-style',
		SCL_PLUGIN_URL . 'assets/css/style.css',
		array(),
		SCL_VERSION
	);

	register_block_type( 'score-company-lookup/lookup', array(
		'editor_script'   => 'scl-block-editor',
		'editor_style'    => 'scl-block-editor-style',
		'render_callback' => 'scl_block_render',
		'attributes'      => array(
			'placeholder' => array(
				'type'    => 'string',
				'default' => '',
			),
			'limit' => array(
				'type'    => 'number',
				'default' => 5,
			),
		),
	) );
}
add_action( 'init', 'scl_register_block' );

/**
 * Server-side render callback for the block.
 * Re-uses the shortcode renderer.
 *
 * @param array $attributes Block attributes.
 * @return string HTML.
 */
function scl_block_render( $attributes ) {
	$atts = array(
		'placeholder' => ! empty( $attributes['placeholder'] )
			? $attributes['placeholder']
			: __( 'Search for a company...', 'score-company-lookup' ),
		'limit' => isset( $attributes['limit'] ) ? absint( $attributes['limit'] ) : 5,
	);
	return scl_shortcode_render( $atts );
}
