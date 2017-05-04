<?php
/**
 * Beeing backwards compatible
 * based on a fix by Stephen Paul Weber (http://singpolyma.net)
 *
 * @deprecated
 */
function publish_to_hub( $deprecated = null, $feed_urls ) {
	pubsubhubbub_publish_to_hub( $feed_urls );
}

/**
 * The ability for other plugins to hook into the PuSH code
 *
 * @param array $feed_urls a list of feed urls you want to publish
 *
 * @deprecated
 */
function pshb_publish_to_hub( $feed_urls ) {
	pubsubhubbub_publish_to_hub( $feed_urls );
}
