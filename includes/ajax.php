<?php
/**
 * AJAX handler for company search.
 *
 * @package ScoreCompanyLookup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX search requests (logged-in and logged-out users).
 */
function scl_ajax_search() {
	check_ajax_referer( 'scl_search_nonce', 'nonce' );

	$query = isset( $_GET['query'] ) ? sanitize_text_field( wp_unslash( $_GET['query'] ) ) : '';
	$limit = isset( $_GET['limit'] ) ? absint( $_GET['limit'] ) : 5;

	if ( strlen( $query ) < 2 ) {
		wp_send_json_error( array(
			'message' => __( 'Please enter at least 2 characters.', 'score-company-lookup' ),
		) );
	}

	if ( $limit < 1 || $limit > 50 ) {
		$limit = 5;
	}

	$endpoint = get_option( 'scl_api_endpoint', 'https://score.get-scala.com/api' );
	$url      = sprintf(
		'%s/search?q=%s&limit=%d',
		untrailingslashit( $endpoint ),
		rawurlencode( $query ),
		$limit
	);

	$response = wp_remote_get( $url, array(
		'timeout' => 15,
		'headers' => array(
			'Accept' => 'application/json',
		),
	) );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( array(
			'message' => __( 'Could not connect to the Score API.', 'score-company-lookup' ),
		) );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );

	if ( 200 !== $code ) {
		wp_send_json_error( array(
			'message' => sprintf(
				/* translators: %d: HTTP status code */
				__( 'API returned status %d.', 'score-company-lookup' ),
				$code
			),
		) );
	}

	$data = json_decode( $body, true );

	if ( ! is_array( $data ) ) {
		wp_send_json_error( array(
			'message' => __( 'Invalid response from the API.', 'score-company-lookup' ),
		) );
	}

	// Normalise: the API may return results at the top level (array of companies)
	// or inside a "results" / "data" / "companies" key.
	$companies = $data;
	foreach ( array( 'results', 'data', 'companies' ) as $key ) {
		if ( isset( $data[ $key ] ) && is_array( $data[ $key ] ) ) {
			$companies = $data[ $key ];
			break;
		}
	}

	// Sanitise output.
	$clean = array();
	foreach ( $companies as $company ) {
		if ( ! is_array( $company ) ) {
			continue;
		}
		$clean[] = array(
			'name'      => isset( $company['name'] )      ? sanitize_text_field( $company['name'] )      : '',
			'country'   => isset( $company['country'] )    ? sanitize_text_field( $company['country'] )   : '',
			'revenue'   => isset( $company['revenue'] )    ? $company['revenue']                           : null,
			'employees' => isset( $company['employees'] )  ? $company['employees']                         : null,
			'score'     => isset( $company['score'] )      ? $company['score']                             : null,
		);
	}

	wp_send_json_success( array(
		'companies' => $clean,
		'total'     => count( $clean ),
	) );
}
add_action( 'wp_ajax_scl_search', 'scl_ajax_search' );
add_action( 'wp_ajax_nopriv_scl_search', 'scl_ajax_search' );
