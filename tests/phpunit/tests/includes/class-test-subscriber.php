<?php
/**
 * Test Subscriber class.
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\Tests;

use Pubsubhubbub\Subscriber;

/**
 * Test class for the Subscriber class.
 *
 * @coversDefaultClass \Pubsubhubbub\Subscriber
 */
class Test_Subscriber extends \WP_UnitTestCase {

	/**
	 * Test get_callback_url.
	 *
	 * @covers ::get_callback_url
	 */
	public function test_get_callback_url() {
		$subscription_id = 'test-subscription-123';
		$callback_url    = Subscriber::get_callback_url( $subscription_id );

		$this->assertStringContainsString( 'pubsubhubbub/v1/callback', $callback_url );
		$this->assertStringContainsString( 'subscription_id=test-subscription-123', $callback_url );
	}

	/**
	 * Test get_callback_url with special characters.
	 *
	 * @covers ::get_callback_url
	 */
	public function test_get_callback_url_encodes_special_chars() {
		$subscription_id = 'test&subscription=123';
		$callback_url    = Subscriber::get_callback_url( $subscription_id );

		$this->assertStringContainsString( 'subscription_id=test%26subscription%3D123', $callback_url );
	}

	/**
	 * Test verify_signature with valid SHA256 signature.
	 *
	 * @covers ::verify_signature
	 */
	public function test_verify_signature_sha256_valid() {
		$body   = 'test content body';
		$secret = 'mysecret';

		$signature = 'sha256=' . hash_hmac( 'sha256', $body, $secret );

		$this->assertTrue( Subscriber::verify_signature( $body, $signature, $secret ) );
	}

	/**
	 * Test verify_signature with valid SHA1 signature.
	 *
	 * @covers ::verify_signature
	 */
	public function test_verify_signature_sha1_valid() {
		$body   = 'test content body';
		$secret = 'mysecret';

		$signature = 'sha1=' . hash_hmac( 'sha1', $body, $secret );

		$this->assertTrue( Subscriber::verify_signature( $body, $signature, $secret ) );
	}

	/**
	 * Test verify_signature with valid SHA512 signature.
	 *
	 * @covers ::verify_signature
	 */
	public function test_verify_signature_sha512_valid() {
		$body   = 'test content body';
		$secret = 'mysecret';

		$signature = 'sha512=' . hash_hmac( 'sha512', $body, $secret );

		$this->assertTrue( Subscriber::verify_signature( $body, $signature, $secret ) );
	}

	/**
	 * Test verify_signature with invalid signature.
	 *
	 * @covers ::verify_signature
	 */
	public function test_verify_signature_invalid() {
		$body   = 'test content body';
		$secret = 'mysecret';

		$signature = 'sha256=invalidsignature';

		$this->assertFalse( Subscriber::verify_signature( $body, $signature, $secret ) );
	}

	/**
	 * Test verify_signature with empty signature.
	 *
	 * @covers ::verify_signature
	 */
	public function test_verify_signature_empty() {
		$body   = 'test content body';
		$secret = 'mysecret';

		$this->assertFalse( Subscriber::verify_signature( $body, '', $secret ) );
	}

	/**
	 * Test verify_signature with empty secret.
	 *
	 * @covers ::verify_signature
	 */
	public function test_verify_signature_empty_secret() {
		$body      = 'test content body';
		$signature = 'sha256=something';

		$this->assertFalse( Subscriber::verify_signature( $body, $signature, '' ) );
	}

	/**
	 * Test verify_signature with malformed signature.
	 *
	 * @covers ::verify_signature
	 */
	public function test_verify_signature_malformed() {
		$body   = 'test content body';
		$secret = 'mysecret';

		$this->assertFalse( Subscriber::verify_signature( $body, 'invalidsignatureformat', $secret ) );
		$this->assertFalse( Subscriber::verify_signature( $body, 'md5=abc123', $secret ) );
	}

	/**
	 * Test get_topic_from_link_header.
	 *
	 * @covers ::get_topic_from_link_header
	 */
	public function test_get_topic_from_link_header() {
		$link_header = '<https://example.com/feed>; rel="self", <https://hub.example.com>; rel="hub"';

		$topic = Subscriber::get_topic_from_link_header( $link_header );

		$this->assertEquals( 'https://example.com/feed', $topic );
	}

	/**
	 * Test get_topic_from_link_header with quoted rel.
	 *
	 * @covers ::get_topic_from_link_header
	 */
	public function test_get_topic_from_link_header_quoted_rel() {
		$link_header = '<https://example.com/feed>; rel=self';

		$topic = Subscriber::get_topic_from_link_header( $link_header );

		$this->assertEquals( 'https://example.com/feed', $topic );
	}

	/**
	 * Test get_topic_from_link_header with no self link.
	 *
	 * @covers ::get_topic_from_link_header
	 */
	public function test_get_topic_from_link_header_no_self() {
		$link_header = '<https://hub.example.com>; rel="hub"';

		$topic = Subscriber::get_topic_from_link_header( $link_header );

		$this->assertFalse( $topic );
	}

	/**
	 * Test subscribe fires actions.
	 *
	 * @covers ::subscribe
	 */
	public function test_subscribe_fires_action() {
		$action_fired = false;
		$captured     = array();

		\add_action(
			'websub_subscribe',
			function ( $topic, $id, $hub ) use ( &$action_fired, &$captured ) {
				$action_fired      = true;
				$captured['topic'] = $topic;
				$captured['id']    = $id;
				$captured['hub']   = $hub;
			},
			10,
			3
		);

		// Mock HTTP request to avoid actual network call.
		\add_filter(
			'pre_http_request',
			function () {
				return array(
					'response' => array( 'code' => 202 ),
					'body'     => '',
				);
			}
		);

		Subscriber::subscribe(
			'https://example.com/feed',
			'test-sub-1',
			array( 'hub_url' => 'https://hub.example.com' )
		);

		$this->assertTrue( $action_fired );
		$this->assertEquals( 'https://example.com/feed', $captured['topic'] );
		$this->assertEquals( 'test-sub-1', $captured['id'] );
		$this->assertEquals( 'https://hub.example.com', $captured['hub'] );
	}

	/**
	 * Test subscribe_args filter.
	 *
	 * @covers ::subscribe
	 */
	public function test_subscribe_args_filter() {
		$filter_called = false;

		\add_filter(
			'websub_subscribe_args',
			// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			function ( $args, $topic, $id ) use ( &$filter_called ) {
				$filter_called   = true;
				$args['hub_url'] = 'https://filtered-hub.example.com';
				$args['secret']  = 'filtered-secret';
				return $args;
			},
			10,
			3
		);

		$captured_hub = '';
		\add_action(
			'websub_subscribe',
			function ( $topic, $id, $hub ) use ( &$captured_hub ) {
				$captured_hub = $hub;
			},
			10,
			3
		);

		// Mock HTTP request.
		\add_filter(
			'pre_http_request',
			function () {
				return array(
					'response' => array( 'code' => 202 ),
					'body'     => '',
				);
			}
		);

		Subscriber::subscribe( 'https://example.com/feed', 'test-sub-2', array() );

		$this->assertTrue( $filter_called );
		$this->assertEquals( 'https://filtered-hub.example.com', $captured_hub );
	}
}
