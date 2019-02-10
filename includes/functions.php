<?php
/**
 * The ability for other plugins to hook into the PuSH code
 *
 * @param array $feed_urls a list of feed urls you want to publish
 */
function pubsubhubbub_publish_to_hub( $feed_urls ) {
	PubSubHubbub_Publisher::publish_to_hub( $feed_urls );
}

/**
 * Get the endpoints from the WordPress options table
 * valid parameters are "publish" or "subscribe"
 *
 * @uses apply_filters() Calls 'pubsubhubbub_hub_urls' filter
 */
function pubsubhubbub_get_hubs() {
	return PubSubHubbub_Publisher::get_hubs();
}

/**
 * Check if link supports PubSubHubbub or WebSub
 *
 * @return boolean
 */
function pubsubhubbub_show_discovery() {
	global $withcomments;

	if ( ! $withcomments ) {
		$withcomments = 0;
	}

	$show_discovery = false;

	$supported_feed_types = apply_filters( 'pubsubhubbub_show_discovery_for_feed_types', array( 'atom', 'rss2', 'rdf' ) );
	$supported_comment_feed_types = apply_filters( 'pubsubhubbub_show_discovery_for_comment_feed_types', array( 'atom', 'rss2' ) );

	if (
		( is_feed( $supported_feed_types ) && ! is_archive() && ! is_singular() && 0 == $withcomments ) ||
		( is_feed( $supported_comment_feed_types ) && 1 == $withcomments ) ||
		( is_home() && current_theme_supports( 'microformats2' ) )
	) {
		$show_discovery = true;
	}

	return apply_filters( 'pubsubhubbub_show_discovery', $show_discovery );
}

/**
 * Get the correct self URL
 *
 * @return boolean
 */
function pubsubhubbub_get_self_link() {
	$host = wp_parse_url( home_url() );

	return esc_url( apply_filters( 'self_link', set_url_scheme( 'http://' . $host['host'] . wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
}
