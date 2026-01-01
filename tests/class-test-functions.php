<?php
/**
 * Test functions.
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\Tests;

use Pubsubhubbub\Pubsubhubbub;
use Pubsubhubbub\Publisher;

/**
 * Test class for functions.
 *
 * @coversDefaultClass \Pubsubhubbub\Pubsubhubbub
 */
class Test_Functions extends \WP_UnitTestCase {

	/**
	 * Test plugin initialization.
	 *
	 * @covers ::get_instance
	 */
	public function test_plugin_instance() {
		$this->assertInstanceOf( Pubsubhubbub::class, Pubsubhubbub::get_instance() );
	}

	/**
	 * Test default hubs constant.
	 *
	 * @covers ::DEFAULT_HUBS
	 */
	public function test_default_hubs() {
		$default_hubs = Pubsubhubbub::DEFAULT_HUBS;

		$this->assertIsArray( $default_hubs );
		$this->assertGreaterThan( 0, count( $default_hubs ) );

		foreach ( $default_hubs as $hub ) {
			$this->assertIsString( $hub );
			$this->assertNotEmpty( $hub );
			$this->assertNotFalse( filter_var( $hub, FILTER_VALIDATE_URL ) );
		}
	}

	/**
	 * Test get_hubs function.
	 *
	 * @covers \Pubsubhubbub\Publisher::get_hubs
	 */
	public function test_get_hubs() {
		$hubs = Publisher::get_hubs();
		$this->assertIsArray( $hubs );
	}

	/**
	 * Test backward compatible functions exist.
	 */
	public function test_backward_compatible_functions() {
		$this->assertTrue( function_exists( 'pubsubhubbub_publish_to_hub' ) );
		$this->assertTrue( function_exists( 'pubsubhubbub_get_hubs' ) );
		$this->assertTrue( function_exists( 'pubsubhubbub_show_discovery' ) );
		$this->assertTrue( function_exists( 'pubsubhubbub_get_self_link' ) );
		$this->assertTrue( function_exists( 'pubsubhubbub_get_supported_feed_types' ) );
		$this->assertTrue( function_exists( 'pubsubhubbub_get_supported_comment_feed_types' ) );
	}

	/**
	 * Test namespaced functions exist.
	 */
	public function test_namespaced_functions() {
		$this->assertTrue( function_exists( 'Pubsubhubbub\publish_to_hub' ) );
		$this->assertTrue( function_exists( 'Pubsubhubbub\get_hubs' ) );
		$this->assertTrue( function_exists( 'Pubsubhubbub\show_discovery' ) );
		$this->assertTrue( function_exists( 'Pubsubhubbub\get_self_link' ) );
		$this->assertTrue( function_exists( 'Pubsubhubbub\get_supported_feed_types' ) );
		$this->assertTrue( function_exists( 'Pubsubhubbub\get_supported_comment_feed_types' ) );
	}

	/**
	 * Test supported feed types.
	 *
	 * @covers \Pubsubhubbub\get_supported_feed_types
	 */
	public function test_supported_feed_types() {
		$feed_types = \Pubsubhubbub\get_supported_feed_types();
		$this->assertIsArray( $feed_types );
		$this->assertContains( 'atom', $feed_types );
		$this->assertContains( 'rss2', $feed_types );
	}

	/**
	 * Test supported comment feed types.
	 *
	 * @covers \Pubsubhubbub\get_supported_comment_feed_types
	 */
	public function test_supported_comment_feed_types() {
		$feed_types = \Pubsubhubbub\get_supported_comment_feed_types();
		$this->assertIsArray( $feed_types );
		$this->assertContains( 'atom', $feed_types );
		$this->assertContains( 'rss2', $feed_types );
	}
}
