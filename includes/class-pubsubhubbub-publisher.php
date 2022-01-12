<?php
/**
 * The WebSub/PubSubHubbub publisher class
 */
class PubSubHubbub_Publisher {
	/**
	 * Function that is called whenever a new post is published
	 *
	 * @param int $post_id the post-id
	 * @return int the post-id
	 */
	public static function publish_post( $post_id ) {
		// we want to notify the hub for every feed
		$feed_urls = self::get_feed_urls_by_post_id( $post_id );

		// publish them
		self::publish_to_hub( $feed_urls );
	}

	/**
	 * Function that is called whenever a new comment is published
	 *
	 * @param int $comment_id the comment-id
	 * @return int the comment-id
	 */
	public static function publish_comment( $comment_id ) {
		// get default comment-feeds
		$feed_urls   = array();
		$feed_urls[] = get_bloginfo( 'comments_atom_url' );
		$feed_urls[] = get_bloginfo( 'comments_rss2_url' );

		$feed_urls = apply_filters( 'pubsubhubbub_comment_feed_urls', $feed_urls, $comment_id );

		// publish them
		self::publish_to_hub( $feed_urls );
	}

	/**
	 * Accepts either a single url or an array of urls
	 *
	 * @param string|array $topic_urls a single topic url or an array of topic urls
	 */
	public static function publish_update( $topic_urls, $hub_url ) {
		if ( ! isset( $hub_url ) ) {
			return new WP_Error( 'missing_hub_url', __( 'Please specify a hub url', 'pubsubhubbub' ) );
		}

		if ( ! preg_match( '|^https?://|i', $hub_url ) ) {
			/* translators: %s is the $hub_url */
			return new WP_Error( 'invalid_hub_url', sprintf( __( 'The specified hub url does not appear to be valid: %s', 'pubsubhubbub' ), $hub_url ) );
		}

		if ( ! isset( $topic_urls ) ) {
			return new WP_Error( 'missing_topic_url', __( 'Please specify a topic url', 'pubsubhubbub' ) );
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
		return wp_remote_post( $hub_url, $args );
	}

	/**
	 * The ability for other plugins to hook into the PuSH code
	 *
	 * @param array $feed_urls a list of feed urls you want to publish
	 */
	public static function publish_to_hub( $feed_urls ) {
		// remove dups (ie. they all point to feedburner)
		$feed_urls = array_unique( $feed_urls );

		// get the list of hubs
		$hub_urls = self::get_hubs();

		// loop through each hub
		foreach ( $hub_urls as $hub_url ) {
			// publish the update to each hub
			$response = self::publish_update( $feed_urls, $hub_url );

			do_action( 'pubsubhubbub_publish_update_response', $response );
		}
	}

	/**
	 * Get the endpoints from the WordPress options table
	 * valid parameters are "publish" or "subscribe"
	 *
	 * @uses apply_filters() Calls 'pubsubhubbub_hub_urls' filter
	 */
	public static function get_hubs() {
		$endpoints = get_option( 'pubsubhubbub_endpoints' );
		$hub_urls  = explode( PHP_EOL, $endpoints );

		// if no values have been set, revert to the defaults (websub on app engine & superfeedr)
		if ( ! $endpoints || ! $hub_urls || ! is_array( $hub_urls ) ) {
			$hub_urls = array(
				'https://pubsubhubbub.appspot.com',
				'https://pubsubhubbub.superfeedr.com',
				'https://websubhub.com/hub'
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
	 * Returns a list of feed URLs for a given Post
	 *
	 * @param int $post_id The post ID
	 *
	 * @return array An array of feed URLs
	 */
	public static function get_feed_urls_by_post_id( $post_id ) {
		$post = get_post( $post_id );

		$feed_types = pubsubhubbub_get_supported_feed_types();

		foreach ( $feed_types as $feed_type ) {
			$feed_urls[] = get_feed_link( $feed_type );

			// add tag-feeds
			$tags = wp_get_post_tags( $post_id );

			foreach ( $tags as $tag ) {
				$feed_urls[] = get_term_feed_link( $tag->term_id, 'post_tag', $feed_type );
			}

			// add category-feeds
			$categories = wp_get_post_categories( $post_id );

			foreach ( $categories as $category ) {
				$feed_urls[] = get_term_feed_link( $category, 'category', $feed_type );
			}

			// add author-feeds
			$feed_urls[] = get_author_feed_link( $post->post_author, $feed_type );
		}

		if ( current_theme_supports( 'microformats2' ) ) {
			$feed_urls[] = site_url( '/' );
		}

		$feed_urls = apply_filters( 'pubsubhubbub_feed_urls', $feed_urls, $post_id );

		return $feed_urls;
	}
}