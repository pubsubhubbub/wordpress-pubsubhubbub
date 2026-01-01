<?php
/**
 * Subscriber Class
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub;

/**
 * Subscriber Class
 *
 * Handles WebSub subscription management for consumer plugins.
 * This is a stateless implementation - consumer plugins are responsible
 * for storing subscription data and providing secrets for verification.
 *
 * @package Pubsubhubbub
 */
class Subscriber {

	/**
	 * Subscribe to a topic URL.
	 *
	 * @param string $topic_url       The topic URL to subscribe to.
	 * @param string $subscription_id Unique identifier for this subscription (consumer-provided).
	 * @param array  $args            Optional arguments:
	 *                                - hub_url: Specific hub URL (auto-discovered if not provided)
	 *                                - secret: HMAC secret for signature verification
	 *                                - lease_seconds: Requested subscription duration.
	 *
	 * @return array|\WP_Error Response array on success, WP_Error on failure.
	 */
	public static function subscribe( $topic_url, $subscription_id, $args = array() ) {
		return self::send_subscription_request( 'subscribe', $topic_url, $subscription_id, $args );
	}

	/**
	 * Unsubscribe from a topic URL.
	 *
	 * @param string $topic_url       The topic URL to unsubscribe from.
	 * @param string $subscription_id The subscription identifier.
	 * @param string $hub_url         The hub URL to send unsubscribe request to.
	 *
	 * @return array|\WP_Error Response array on success, WP_Error on failure.
	 */
	public static function unsubscribe( $topic_url, $subscription_id, $hub_url ) {
		return self::send_subscription_request(
			'unsubscribe',
			$topic_url,
			$subscription_id,
			array( 'hub_url' => $hub_url )
		);
	}

	/**
	 * Send a subscription or unsubscription request to a hub.
	 *
	 * @param string $mode            Either 'subscribe' or 'unsubscribe'.
	 * @param string $topic_url       The topic URL.
	 * @param string $subscription_id The subscription identifier.
	 * @param array  $args            Additional arguments.
	 *
	 * @return array|\WP_Error Response array on success, WP_Error on failure.
	 */
	protected static function send_subscription_request( $mode, $topic_url, $subscription_id, $args = array() ) {
		$defaults = array(
			'hub_url'       => '',
			'secret'        => '',
			'lease_seconds' => 0,
		);

		$args = \wp_parse_args( $args, $defaults );

		/**
		 * Filter subscription request arguments.
		 *
		 * @param array  $args            The subscription arguments.
		 * @param string $topic_url       The topic URL.
		 * @param string $subscription_id The subscription identifier.
		 */
		$args = \apply_filters( 'websub_subscribe_args', $args, $topic_url, $subscription_id );

		// Discover hub if not provided.
		$hub_url = $args['hub_url'];
		if ( empty( $hub_url ) ) {
			$discovered = self::discover_hub( $topic_url );
			if ( \is_wp_error( $discovered ) ) {
				return $discovered;
			}
			$hub_url = $discovered;
		}

		// Build callback URL.
		$callback_url = self::get_callback_url( $subscription_id );

		/**
		 * Fires before sending a subscription request.
		 *
		 * @param string $topic_url       The topic URL.
		 * @param string $subscription_id The subscription identifier.
		 * @param string $hub_url         The hub URL.
		 */
		\do_action( 'websub_pre_' . $mode, $topic_url, $subscription_id, $hub_url );

		// Build request body.
		$body = array(
			'hub.mode'     => $mode,
			'hub.topic'    => $topic_url,
			'hub.callback' => $callback_url,
		);

		// Add optional parameters.
		if ( ! empty( $args['secret'] ) ) {
			$body['hub.secret'] = $args['secret'];
		}

		/**
		 * Filter the requested lease duration.
		 *
		 * @param int    $seconds         The requested lease duration in seconds.
		 * @param string $subscription_id The subscription identifier.
		 */
		$lease_seconds = \apply_filters( 'websub_lease_seconds', $args['lease_seconds'], $subscription_id );
		if ( $lease_seconds > 0 ) {
			$body['hub.lease_seconds'] = $lease_seconds;
		}

		$wp_version = \get_bloginfo( 'version' );
		$user_agent = \apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . \get_bloginfo( 'url' ) );

		$request_args = array(
			'timeout'    => 30,
			'user-agent' => "$user_agent; WebSub/PubSubHubbub",
			'headers'    => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
			'body'       => $body,
		);

		$response = \wp_remote_post( $hub_url, $request_args );

		if ( \is_wp_error( $response ) ) {
			/**
			 * Fires when a subscription request fails.
			 *
			 * @param string    $topic_url       The topic URL.
			 * @param string    $subscription_id The subscription identifier.
			 * @param \WP_Error $error           The error object.
			 */
			\do_action( 'websub_' . $mode . '_error', $topic_url, $subscription_id, $response );
			return $response;
		}

		$status_code = \wp_remote_retrieve_response_code( $response );

		// Hub should return 202 Accepted for async verification.
		if ( $status_code >= 200 && $status_code < 300 ) {
			/**
			 * Fires when a subscription request succeeds.
			 *
			 * @param string $topic_url       The topic URL.
			 * @param string $subscription_id The subscription identifier.
			 * @param array  $response        The HTTP response.
			 */
			\do_action( 'websub_' . $mode . '_success', $topic_url, $subscription_id, $response );
			return $response;
		}

		$error = new \WP_Error(
			'websub_request_failed',
			\sprintf(
				/* translators: %d is the HTTP status code */
				\__( 'Hub returned HTTP status %d', 'pubsubhubbub' ),
				$status_code
			),
			array(
				'status'   => $status_code,
				'response' => $response,
			)
		);

		\do_action( 'websub_' . $mode . '_error', $topic_url, $subscription_id, $error );

		return $error;
	}

	/**
	 * Get the callback URL for a subscription.
	 *
	 * @param string $subscription_id The subscription identifier.
	 *
	 * @return string The callback URL.
	 */
	public static function get_callback_url( $subscription_id ) {
		return \rest_url( 'pubsubhubbub/v1/callback' ) . '?subscription_id=' . \rawurlencode( $subscription_id );
	}

	/**
	 * Discover hub URL from a topic URL.
	 *
	 * @param string $topic_url The topic URL to discover hub from.
	 *
	 * @return string|\WP_Error The hub URL on success, WP_Error on failure.
	 */
	public static function discover_hub( $topic_url ) {
		$response = \wp_remote_get(
			$topic_url,
			array(
				'timeout' => 15,
			)
		);

		if ( \is_wp_error( $response ) ) {
			return $response;
		}

		// Check Link headers first.
		$link_header = \wp_remote_retrieve_header( $response, 'link' );
		if ( ! empty( $link_header ) ) {
			$hub_url = self::parse_link_header( $link_header, 'hub' );
			if ( $hub_url ) {
				return $hub_url;
			}
		}

		// Parse body for link elements.
		$body = \wp_remote_retrieve_body( $response );
		if ( ! empty( $body ) ) {
			$hub_url = self::parse_feed_for_hub( $body );
			if ( $hub_url ) {
				return $hub_url;
			}
		}

		return new \WP_Error(
			'websub_no_hub_found',
			\__( 'No WebSub hub found for this topic URL', 'pubsubhubbub' )
		);
	}

	/**
	 * Parse Link header for a specific rel value.
	 *
	 * @param string|array $header The Link header value(s).
	 * @param string       $rel    The rel value to find.
	 *
	 * @return string|false The URL if found, false otherwise.
	 */
	protected static function parse_link_header( $header, $rel ) {
		$headers = is_array( $header ) ? $header : array( $header );

		foreach ( $headers as $link ) {
			// Match pattern: <URL>; rel="value" or <URL>; rel=value.
			if ( \preg_match( '/<([^>]+)>;\s*rel=["\']?' . \preg_quote( $rel, '/' ) . '["\']?/i', $link, $matches ) ) {
				return $matches[1];
			}
		}

		return false;
	}

	/**
	 * Parse feed content for hub link.
	 *
	 * @param string $content The feed content.
	 *
	 * @return string|false The hub URL if found, false otherwise.
	 */
	protected static function parse_feed_for_hub( $content ) {
		// Try to parse as XML (Atom/RSS).
		\libxml_use_internal_errors( true );
		$xml = \simplexml_load_string( $content );

		if ( false !== $xml ) {
			// Check for Atom namespace.
			$namespaces = $xml->getNamespaces( true );

			// Atom feed.
			if ( isset( $namespaces[''] ) && false !== \strpos( $namespaces[''], 'Atom' ) ) {
				foreach ( $xml->link as $link ) {
					$attrs = $link->attributes();
					if ( isset( $attrs['rel'] ) && 'hub' === (string) $attrs['rel'] ) {
						return (string) $attrs['href'];
					}
				}
			}

			// RSS with Atom namespace.
			if ( isset( $namespaces['atom'] ) ) {
				$atom = $xml->channel->children( $namespaces['atom'] );
				foreach ( $atom->link as $link ) {
					$attrs = $link->attributes();
					if ( isset( $attrs['rel'] ) && 'hub' === (string) $attrs['rel'] ) {
						return (string) $attrs['href'];
					}
				}
			}

			// Check channel directly for RSS.
			if ( isset( $xml->channel ) ) {
				foreach ( $xml->channel->children() as $child ) {
					if ( 'link' === $child->getName() ) {
						$attrs = $child->attributes();
						if ( isset( $attrs['rel'] ) && 'hub' === (string) $attrs['rel'] ) {
							return (string) $attrs['href'];
						}
					}
				}
			}
		}

		\libxml_clear_errors();

		// Try HTML parsing as fallback.
		if ( \preg_match( '/<link[^>]+rel=["\']?hub["\']?[^>]+href=["\']?([^"\'>\s]+)["\']?/i', $content, $matches ) ) {
			return $matches[1];
		}

		if ( \preg_match( '/<link[^>]+href=["\']?([^"\'>\s]+)["\']?[^>]+rel=["\']?hub["\']?/i', $content, $matches ) ) {
			return $matches[1];
		}

		return false;
	}

	/**
	 * Verify HMAC signature from X-Hub-Signature header.
	 *
	 * @param string $body             The request body.
	 * @param string $signature_header The X-Hub-Signature header value.
	 * @param string $secret           The shared secret.
	 *
	 * @return bool True if signature is valid, false otherwise.
	 */
	public static function verify_signature( $body, $signature_header, $secret ) {
		if ( empty( $signature_header ) || empty( $secret ) ) {
			return false;
		}

		// Parse signature header: method=signature.
		if ( ! \preg_match( '/^(sha1|sha256|sha384|sha512)=([a-f0-9]+)$/i', $signature_header, $matches ) ) {
			return false;
		}

		$algorithm = \strtolower( $matches[1] );
		$signature = $matches[2];

		$expected = \hash_hmac( $algorithm, $body, $secret );

		return \hash_equals( $expected, $signature );
	}

	/**
	 * Extract topic URL from Link headers.
	 *
	 * @param string|array $link_header The Link header value(s).
	 *
	 * @return string|false The topic URL if found, false otherwise.
	 */
	public static function get_topic_from_link_header( $link_header ) {
		return self::parse_link_header( $link_header, 'self' );
	}
}
