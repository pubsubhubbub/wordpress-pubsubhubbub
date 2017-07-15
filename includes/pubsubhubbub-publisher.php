<?php
// a PHP client library for pubsubhubbub
// as defined at http://code.google.com/p/pubsubhubbub/
// written by Josh Fraser | joshfraser.com | josh@eventvue.com
// modified by Matthias Pfefferle | notizblog.org | matthias@pfefferle.org
// Released under Apache License 2.0

/**
 * A pubsubhubbub publisher
 *
 * @author Josh Fraser
 * @author Matthias Pfefferle
 */
class PubSubHubbub_Publisher {
	protected $hub_url;

	// create a new Publisher
	public function __construct( $hub_url ) {

		if ( ! isset( $hub_url ) ) {
			throw new Exception( 'Please specify a hub url' );
		}

		if ( ! preg_match( '|^https?://|i', $hub_url ) ) {
			throw new Exception( 'The specified hub url does not appear to be valid: ' . $hub_url );
		}

		$this->hub_url = $hub_url;
	}

	/**
	 * accepts either a single url or an array of urls
	 *
	 * @param string|array $topic_urls a single topic url or an array of topic urls
	 */
	public function publish_update( $topic_urls ) {
		if ( ! isset( $topic_urls ) ) {
			throw new Exception( 'Please specify a topic url' );
		}

		// check that we're working with an array
		if ( ! is_array( $topic_urls ) ) {
			$topic_urls = array( $topic_urls );
		}

		// set the mode to publish
		$post_string = 'hub.mode=publish';
		// loop through each topic url
		foreach ( $topic_urls as $topic_url ) {
			// lightweight check that we're actually working w/ a valid url
			if ( preg_match( '|^https?://|i', $topic_url ) ) {
				// append the topic url parameters
				$post_string .= '&hub.url=' . esc_url( $topic_url );
			}
		}

		$wp_version = get_bloginfo( 'version' );
		$user_agent = apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) );
		$args = array(
			'timeout' => 100,
			'limit_response_size' => 1048576,
			'redirection' => 20,
			'user-agent' => "$user_agent; PubSubHubbub/WebSub",
			'body' => $post_string,
		);

		// make the http post request
		return wp_remote_post( $this->hub_url, $args );
	}
}
