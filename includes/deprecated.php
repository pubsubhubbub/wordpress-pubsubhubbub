<?php
/**
 * Deprecated functions for backward compatibility.
 *
 * @package Pubsubhubbub
 */

use Pubsubhubbub\Publisher;

/**
 * Being backwards compatible.
 * Based on a fix by Stephen Paul Weber (http://singpolyma.net)
 *
 * @deprecated 3.0.0 Use pubsubhubbub_publish_to_hub() instead.
 *
 * @param mixed $deprecated Deprecated parameter.
 * @param array $feed_urls  A list of feed urls you want to publish.
 *
 * @return void
 */
function publish_to_hub( $deprecated, $feed_urls ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'pubsubhubbub_publish_to_hub()' );

	Publisher::publish_to_hub( $feed_urls );
}

/**
 * The ability for other plugins to hook into the PuSH code.
 *
 * @deprecated 3.0.0 Use pubsubhubbub_publish_to_hub() instead.
 *
 * @param array $feed_urls A list of feed urls you want to publish.
 *
 * @return void
 */
function pshb_publish_to_hub( $feed_urls ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'pubsubhubbub_publish_to_hub()' );

	Publisher::publish_to_hub( $feed_urls );
}

/**
 * The ability for other plugins to hook into the PuSH code.
 *
 * This function maintains backward compatibility with the old naming convention.
 *
 * @param array $feed_urls A list of feed urls you want to publish.
 *
 * @return void
 */
function pubsubhubbub_publish_to_hub( $feed_urls ) {
	Publisher::publish_to_hub( $feed_urls );
}

/**
 * Get the endpoints from the WordPress options table.
 *
 * This function maintains backward compatibility with the old naming convention.
 *
 * @uses apply_filters() Calls 'websub_hub_urls' filter.
 *
 * @return array The hub URLs.
 */
function pubsubhubbub_get_hubs() {
	return Publisher::get_hubs();
}

/**
 * Check if link supports PubSubHubbub or WebSub.
 *
 * This function maintains backward compatibility with the old naming convention.
 *
 * @return boolean
 */
function pubsubhubbub_show_discovery() {
	return \Pubsubhubbub\show_discovery();
}

/**
 * Get the correct self URL.
 *
 * This function maintains backward compatibility with the old naming convention.
 *
 * @return string The self link URL.
 */
function pubsubhubbub_get_self_link() {
	return \Pubsubhubbub\get_self_link();
}

/**
 * Return the list of feed types that are supported by PubSubHubbub.
 *
 * This function maintains backward compatibility with the old naming convention.
 *
 * @return array List of supported feed types.
 */
function pubsubhubbub_get_supported_feed_types() {
	return \Pubsubhubbub\get_supported_feed_types();
}

/**
 * Return the list of comment feed types that are supported by PubSubHubbub.
 *
 * This function maintains backward compatibility with the old naming convention.
 *
 * @return array List of supported comment feed types.
 */
function pubsubhubbub_get_supported_comment_feed_types() {
	return \Pubsubhubbub\get_supported_comment_feed_types();
}

/**
 * Map old filter to new filter.
 *
 * @deprecated 3.0.0
 *
 * @param array $feed_urls The list of feed urls.
 *
 * @return array Filtered list.
 */
function pshb_feed_urls( $feed_urls ) {
	return apply_filters( 'pshb_feed_urls', $feed_urls );
}
add_filter( 'websub_feed_urls', 'pshb_feed_urls' );
