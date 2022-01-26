<?php
/**
 * Beeing backwards compatible
 * based on a fix by Stephen Paul Weber (http://singpolyma.net)
 *
 * @deprecated
 */
function publish_to_hub( $deprecated, $feed_urls ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'pubsubhubbub_publish_to_hub()' );

	PubSubHubbub_Publisher::publish_to_hub( $feed_urls );
}

/**
 * The ability for other plugins to hook into the PuSH code
 *
 * @param array $feed_urls a list of feed urls you want to publish
 *
 * @deprecated
 */
function pshb_publish_to_hub( $feed_urls ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'pubsubhubbub_publish_to_hub()' );

	PubSubHubbub_Publisher::publish_to_hub( $feed_urls );
}

/**
 * Map old filter to new filter
 *
 * @param array $feed_urls the list of feed urls
 *
 * @return array filtered list
 *
 * @deprecated
 */
function pshb_feed_urls( $feed_urls ) {
	//_deprecated_function( __FUNCTION__, '3.0.0', 'get_feed_urls_by_post_id()' );

	return apply_filters( 'pshb_feed_urls', $feed_urls );
}
add_filter( 'pubsubhubbub_feed_urls', 'pshb_feed_urls' );
