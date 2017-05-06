<?php
/**
 * The ability for other plugins to hook into the PuSH code
 *
 * @param array $feed_urls a list of feed urls you want to publish
 */
function pubsubhubbub_publish_to_hub( $feed_urls ) {
	require_once( dirname( __FILE__ ) . '/pubsubhubbub-publisher.php' );

	// remove dups (ie. they all point to feedburner)
	$feed_urls = array_unique( $feed_urls );

	pubsubhubbub_update_pinged_urls( $feed_urls );

	// get the list of hubs
	$hub_urls = pubsubhubbub_get_hubs();

	// loop through each hub
	foreach ( $hub_urls as $hub_url ) {
		$p = new PubSubHubbub_Publisher( $hub_url );
		// publish the update to each hub
		$response = $p->publish_update( $feed_urls );

		do_action( 'pubsubhubbub_publish_update_response', $response );
	}
}

/**
 * get the endpoints from the wordpress options table
 * valid parameters are "publish" or "subscribe"
 *
 *	@uses apply_filters() Calls 'pubsubhubbub_hub_urls' filter
 */
function pubsubhubbub_get_hubs() {
	$endpoints = get_option( 'pubsubhubbub_endpoints' );
	$hub_urls = explode( PHP_EOL, $endpoints );

	// if no values have been set, revert to the defaults (websub on app engine & superfeedr)
	if ( ! $endpoints || ! is_array( $endpoints ) ) {
		$hub_urls[] = 'https://pubsubhubbub.appspot.com';
		$hub_urls[] = 'https://pubsubhubbub.superfeedr.com';
	}

	// clean out any blank values
	foreach ( $hub_urls as $key => $value ) {
		if ( is_null( $value ) || '' == $value ) {
			unset( $hub_urls[ $key ] );
		} else {
			$hub_urls[ $key ] = trim( $hub_urls[ $key ] );
		}
	}

	return apply_filters( 'pubsubhubbub_hub_urls', $hub_urls );
}

/**
 * Add new pinged urls
 *
 * @param array $urls list of urls
 */
function pubsubhubbub_update_pinged_urls( $urls ) {
	if ( ! is_array( $urls ) ) {
		return;
	}

	$pinged_urls = pubsubhubbub_get_pinged_urls();
	$pinged_urls = array_merge( $pinged_urls, $urls );

	update_option( 'pubsubhubbub_pinged_urls', array_unique( $pinged_urls ) );
}

/**
 * Return already pinged urls
 *
 * @return array list of urls
 */
function pubsubhubbub_get_pinged_urls() {
	$default_feeds = array(
		get_bloginfo( 'atom_url' ),
		get_bloginfo( 'rdf_url' ),
		get_bloginfo( 'rss2_url' ),
		get_bloginfo( 'comments_atom_url' ),
		get_bloginfo( 'comments_rss2_url' ),
	);

	$feeds = get_option( 'pubsubhubbub_pinged_urls', $default_feeds );

	if ( is_array( $feeds ) ) {
		return $feeds;
	}

	return $default_feeds;
}

/**
 * Check if link supports PubSubHubbub or WebSub
 *
 * @return boolean
 */
function pubsubhubbub_show_discovery() {
	// get current url
	$urls = pubsubhubbub_get_pinged_urls();

	$current_url = home_url( add_query_arg( null, null ) );

	// check if current url is one of the feed urls
	if ( in_array( $current_url, $urls ) ) {
		return true;
	}

	return false;
}
