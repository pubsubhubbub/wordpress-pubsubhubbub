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
	$show_discovery = false;

	$supported_feed_types = apply_filters( 'pubsubhubbub_show_discovery_for_feed_types', pubsubhubbub_get_supported_feed_types() );
	$supported_comment_feed_types = apply_filters( 'pubsubhubbub_show_discovery_for_comment_feed_types', pubsubhubbub_get_supported_comment_feed_types() );

	if (
		( is_feed( $supported_feed_types ) && ! is_date() && ! is_post_type_archive() && ! is_singular() ) ||
		( is_feed( $supported_comment_feed_types ) && is_singular() ) ||
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

/**
 * Return the list of feed types that are supported by PubSubHubbub
 *
 * @return array List of supported feed types
 */
function pubsubhubbub_get_supported_feed_types() {
	return apply_filters( 'pubsubhubbub_supported_feed_types', array( 'atom', 'rss2' ) );
}

/**
 * Return the list of comment feed types that are supported by PubSubHubbub
 *
 * @return array List of supported comment feed types
 */
function pubsubhubbub_get_supported_comment_feed_types() {
	return apply_filters( 'pubsubhubbub_supported_comment_feed_types', array( 'atom', 'rss2' ) );
}
