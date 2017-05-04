<?php
/**
 * the ability for other plugins to hook into the PuSH code
 *
 * @param array $feed_urls a list of feed urls you want to publish
 */
function pubsubhubbub_publish_to_hub( $feed_urls ) {
	require_once( dirname( __FILE__ ) . '/pubsubhubbub-publisher.php' );

	// remove dups (ie. they all point to feedburner)
	$feed_urls = array_unique( $feed_urls );

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

function pubsubhubbub_get_feed_urls( $post_id = null ) {
	// we want to notify the hub for every feed
	$feed_urls = array();
	$feed_urls[] = get_bloginfo( 'atom_url' );
	$feed_urls[] = get_bloginfo( 'rdf_url' );
	$feed_urls[] = get_bloginfo( 'rss2_url' );

	return apply_filters( 'pubsubhubbub_feed_urls', $feed_urls, $post_id );
}

function pubsubhubbub_get_comment_feed_urls( $comment_id = null ) {
	// get default comment-feeds
	$feed_urls = array();
	$feed_urls[] = get_bloginfo( 'comments_atom_url' );
	$feed_urls[] = get_bloginfo( 'comments_rss2_url' );

	return apply_filters( 'pubsubhubbub_comment_feed_urls', $feed_urls, $comment_id );
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
	if ( ! $endpoints ) {
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
