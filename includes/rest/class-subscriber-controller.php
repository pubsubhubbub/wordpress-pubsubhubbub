<?php
/**
 * Subscriber REST Controller
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\Rest;

use Pubsubhubbub\Subscriber;

/**
 * Subscriber REST Controller
 *
 * Handles WebSub callback endpoint for subscription verification
 * and content distribution.
 *
 * @package Pubsubhubbub
 */
class Subscriber_Controller {

	/**
	 * Namespace for the REST API.
	 *
	 * @var string
	 */
	const NAMESPACE = 'pubsubhubbub/v1';

	/**
	 * Route for the callback endpoint.
	 *
	 * @var string
	 */
	const ROUTE = '/callback';

	/**
	 * Register the REST API routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		\register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( static::class, 'handle_verification' ),
					'permission_callback' => '__return_true',
					'args'                => self::get_verification_args(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( static::class, 'handle_distribution' ),
					'permission_callback' => '__return_true',
					'args'                => self::get_distribution_args(),
				),
			)
		);
	}

	/**
	 * Get verification request arguments schema.
	 *
	 * @return array The arguments schema.
	 */
	protected static function get_verification_args() {
		return array(
			'subscription_id'   => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'hub_mode'          => array(
				'required'          => true,
				'type'              => 'string',
				'enum'              => array( 'subscribe', 'unsubscribe', 'denied' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'hub_topic'         => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
			),
			'hub_challenge'     => array(
				'required'          => false,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'hub_lease_seconds' => array(
				'required'          => false,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'hub_reason'        => array(
				'required'          => false,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Get distribution request arguments schema.
	 *
	 * @return array The arguments schema.
	 */
	protected static function get_distribution_args() {
		return array(
			'subscription_id' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Handle verification request from hub (GET).
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return \WP_REST_Response|\WP_Error The response.
	 */
	public static function handle_verification( $request ) {
		$subscription_id = $request->get_param( 'subscription_id' );
		$mode            = $request->get_param( 'hub_mode' );
		$topic           = $request->get_param( 'hub_topic' );
		$challenge       = $request->get_param( 'hub_challenge' );
		$lease_seconds   = $request->get_param( 'hub_lease_seconds' );
		$reason          = $request->get_param( 'hub_reason' );

		// Handle denial notification.
		if ( 'denied' === $mode ) {
			/**
			 * Fires when a subscription is denied by the hub.
			 *
			 * @param string $subscription_id The subscription identifier.
			 * @param string $topic           The topic URL.
			 * @param string $reason          The denial reason.
			 */
			\do_action( 'websub_denied', $subscription_id, $topic, $reason );

			return new \WP_REST_Response( '', 200 );
		}

		// Verify that consumer wants to accept this subscription.
		/**
		 * Filter whether to accept a subscription verification.
		 *
		 * @param bool   $allow           Whether to allow the subscription. Default false.
		 * @param string $subscription_id The subscription identifier.
		 * @param string $topic           The topic URL.
		 * @param string $mode            The mode ('subscribe' or 'unsubscribe').
		 */
		$allow = \apply_filters( 'websub_verify_subscription', false, $subscription_id, $topic, $mode );

		if ( ! $allow ) {
			return new \WP_Error(
				'websub_verification_denied',
				\__( 'Subscription verification denied', 'pubsubhubbub' ),
				array( 'status' => 404 )
			);
		}

		// Challenge is required for subscribe/unsubscribe.
		if ( empty( $challenge ) ) {
			return new \WP_Error(
				'websub_missing_challenge',
				\__( 'Missing hub.challenge parameter', 'pubsubhubbub' ),
				array( 'status' => 400 )
			);
		}

		/**
		 * Fires when a subscription is verified.
		 *
		 * @param string $subscription_id The subscription identifier.
		 * @param string $topic           The topic URL.
		 * @param int    $lease_seconds   The lease duration in seconds.
		 * @param string $mode            The mode ('subscribe' or 'unsubscribe').
		 */
		\do_action( 'websub_verified', $subscription_id, $topic, $lease_seconds, $mode );

		// Return challenge to confirm verification.
		$response = new \WP_REST_Response( $challenge, 200 );
		$response->set_headers( array( 'Content-Type' => 'text/plain; charset=utf-8' ) );

		return $response;
	}

	/**
	 * Handle content distribution from hub (POST).
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function handle_distribution( $request ) {
		$subscription_id = $request->get_param( 'subscription_id' );
		$content         = $request->get_body();
		$content_type    = $request->get_content_type();
		$signature       = $request->get_header( 'X-Hub-Signature' );
		$link_header     = $request->get_header( 'Link' );

		// Extract topic URL from Link header.
		$topic = '';
		if ( ! empty( $link_header ) ) {
			$topic = Subscriber::get_topic_from_link_header( $link_header );
		}

		// Verify signature if present.
		if ( ! empty( $signature ) ) {
			/**
			 * Filter to get the secret for a subscription.
			 *
			 * @param string $secret          The secret. Default empty.
			 * @param string $subscription_id The subscription identifier.
			 */
			$secret = \apply_filters( 'websub_subscription_secret', '', $subscription_id );

			if ( ! empty( $secret ) ) {
				$is_valid = Subscriber::verify_signature( $content, $signature, $secret );

				if ( $is_valid ) {
					/**
					 * Fires when signature verification succeeds.
					 *
					 * @param string $subscription_id The subscription identifier.
					 * @param string $topic           The topic URL.
					 */
					\do_action( 'websub_signature_valid', $subscription_id, $topic );
				} else {
					/**
					 * Fires when signature verification fails.
					 *
					 * @param string $subscription_id The subscription identifier.
					 * @param string $topic           The topic URL.
					 * @param string $signature       The received signature.
					 */
					\do_action( 'websub_signature_invalid', $subscription_id, $topic, $signature );

					// Per spec: locally ignore but MAY acknowledge with 2xx.
					// We still fire the action but consumer should check signature_invalid.
				}
			}
		}

		$content_type_value = '';
		if ( is_array( $content_type ) && isset( $content_type['value'] ) ) {
			$content_type_value = $content_type['value'];
		} elseif ( is_string( $content_type ) ) {
			$content_type_value = $content_type;
		}

		/**
		 * Fires when content is received from a hub.
		 *
		 * @param string $subscription_id The subscription identifier.
		 * @param string $topic           The topic URL.
		 * @param string $content         The content body.
		 * @param string $content_type    The content type.
		 */
		\do_action( 'websub_received', $subscription_id, $topic, $content, $content_type_value );

		// Return 200 OK to acknowledge receipt.
		return new \WP_REST_Response( '', 200 );
	}
}
