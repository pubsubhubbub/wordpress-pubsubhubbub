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

	pubsubhubbub_update_topic_urls( $feed_urls );

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
	if ( ! $endpoints || ! $hub_urls || ! is_array( $hub_urls ) ) {
		$hub_urls = array(
			'https://pubsubhubbub.appspot.com',
			'https://pubsubhubbub.superfeedr.com',
		);
	}

	// clean out any blank values
	foreach ( $hub_urls as $key => $value ) {
		if ( empty( $value ) ) {
			unset( $hub_urls[ $key ] );
		} else {
			$hub_urls[ $key ] = trim( $hub_urls[ $key ] );
		}
	}

	return apply_filters( 'pubsubhubbub_hub_urls', $hub_urls );
}

/**
 * Add new topic urls
 *
 * @param array $urls list of urls
 */
function pubsubhubbub_update_topic_urls( $urls ) {
	if ( ! is_array( $urls ) ) {
		return;
	}

	$topic_urls = pubsubhubbub_get_topic_urls();
	$topic_urls = array_merge( $topic_urls, $urls );

	update_option( 'pubsubhubbub_topic_urls', array_unique( $topic_urls ) );
}

/**
 * Return topic urls
 *
 * @return array list of urls
 */
function pubsubhubbub_get_topic_urls() {
	$default_feeds = array(
		get_bloginfo( 'atom_url' ),
		get_bloginfo( 'rdf_url' ),
		get_bloginfo( 'rss2_url' ),
		get_bloginfo( 'comments_atom_url' ),
		get_bloginfo( 'comments_rss2_url' ),
	);

	$feeds = get_option( 'pubsubhubbub_topic_urls', $default_feeds );

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
	return (boolean) pubsubhubbub_get_self_link();
}

/**
 * Get the correct self URL
 *
 * @return boolean
 */
function pubsubhubbub_get_self_link() {
	// get current url
	$urls = pubsubhubbub_get_topic_urls();

	$current_url = home_url( add_query_arg( null, null ) );
	$current_url = untrailingslashit( $current_url );
	$current_url = preg_replace( '/^https?:\/\//i', '', $current_url );

	$matches = preg_grep( '/^https?:\/\/' . preg_quote( $current_url, '/' ) . '\/?$/i', $urls );

	if ( empty( $matches ) ) {
		return false;
	}

	if ( count( $matches ) >= 2 ) {
		return home_url( add_query_arg( null, null ) );
	}

	return current( $matches );
}
