<?php
/**
 * Test functions.
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\Tests;

use Pubsubhubbub\Publisher;

/**
 * Test class for functions.
 */
class Test_Functions extends \WP_UnitTestCase {

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

	/**
	 * Test deprecated pubsubhubbub_supported_feed_types filter still works.
	 *
	 * @covers \Pubsubhubbub\get_supported_feed_types
	 * @expectedDeprecated pubsubhubbub_supported_feed_types
	 */
	public function test_deprecated_supported_feed_types_filter() {
		$filter = function ( $feed_types ) {
			$feed_types[] = 'json';
			return $feed_types;
		};

		\add_filter( 'pubsubhubbub_supported_feed_types', $filter );

		$feed_types = \Pubsubhubbub\get_supported_feed_types();

		$this->assertContains( 'json', $feed_types );

		\remove_filter( 'pubsubhubbub_supported_feed_types', $filter );
	}

	/**
	 * Test deprecated pubsubhubbub_supported_comment_feed_types filter still works.
	 *
	 * @covers \Pubsubhubbub\get_supported_comment_feed_types
	 * @expectedDeprecated pubsubhubbub_supported_comment_feed_types
	 */
	public function test_deprecated_supported_comment_feed_types_filter() {
		$filter = function ( $feed_types ) {
			$feed_types[] = 'json';
			return $feed_types;
		};

		\add_filter( 'pubsubhubbub_supported_comment_feed_types', $filter );

		$feed_types = \Pubsubhubbub\get_supported_comment_feed_types();

		$this->assertContains( 'json', $feed_types );

		\remove_filter( 'pubsubhubbub_supported_comment_feed_types', $filter );
	}

	/**
	 * Test websub_supported_feed_types filter works.
	 *
	 * @covers \Pubsubhubbub\get_supported_feed_types
	 */
	public function test_websub_supported_feed_types_filter() {
		$filter = function ( $feed_types ) {
			$feed_types[] = 'rdf';
			return $feed_types;
		};

		\add_filter( 'websub_supported_feed_types', $filter );

		$feed_types = \Pubsubhubbub\get_supported_feed_types();

		$this->assertContains( 'rdf', $feed_types );

		\remove_filter( 'websub_supported_feed_types', $filter );
	}

	/**
	 * Test that deprecated filter runs before new filter for supported feed types.
	 *
	 * @covers \Pubsubhubbub\get_supported_feed_types
	 * @expectedDeprecated pubsubhubbub_supported_feed_types
	 */
	public function test_deprecated_supported_feed_types_filter_runs_before_new() {
		$order = array();

		$deprecated_filter = function ( $types ) use ( &$order ) {
			$order[] = 'deprecated';
			return $types;
		};

		$new_filter = function ( $types ) use ( &$order ) {
			$order[] = 'new';
			return $types;
		};

		\add_filter( 'pubsubhubbub_supported_feed_types', $deprecated_filter );
		\add_filter( 'websub_supported_feed_types', $new_filter );

		\Pubsubhubbub\get_supported_feed_types();

		$this->assertEquals( array( 'deprecated', 'new' ), $order );

		\remove_filter( 'pubsubhubbub_supported_feed_types', $deprecated_filter );
		\remove_filter( 'websub_supported_feed_types', $new_filter );
	}

	/**
	 * Test deprecated pubsubhubbub_show_discovery_for_feed_types filter still works.
	 *
	 * @covers \Pubsubhubbub\show_discovery
	 * @expectedDeprecated pubsubhubbub_show_discovery_for_feed_types
	 */
	public function test_deprecated_show_discovery_for_feed_types_filter() {
		$filter = function ( $feed_types ) {
			$feed_types[] = 'custom';
			return $feed_types;
		};

		\add_filter( 'pubsubhubbub_show_discovery_for_feed_types', $filter );

		// Call the function to trigger the filter.
		\Pubsubhubbub\show_discovery();

		// Verify the filter was called by applying it manually.
		$feed_types = \apply_filters( 'pubsubhubbub_show_discovery_for_feed_types', array( 'atom', 'rss2' ) );
		$this->assertContains( 'custom', $feed_types );

		\remove_filter( 'pubsubhubbub_show_discovery_for_feed_types', $filter );
	}

	/**
	 * Test deprecated pubsubhubbub_show_discovery_for_comment_feed_types filter still works.
	 *
	 * @covers \Pubsubhubbub\show_discovery
	 * @expectedDeprecated pubsubhubbub_show_discovery_for_comment_feed_types
	 */
	public function test_deprecated_show_discovery_for_comment_feed_types_filter() {
		$filter = function ( $feed_types ) {
			$feed_types[] = 'custom-comment';
			return $feed_types;
		};

		\add_filter( 'pubsubhubbub_show_discovery_for_comment_feed_types', $filter );

		// Call the function to trigger the filter.
		\Pubsubhubbub\show_discovery();

		// Verify the filter was called by applying it manually.
		$feed_types = \apply_filters( 'pubsubhubbub_show_discovery_for_comment_feed_types', array( 'atom', 'rss2' ) );
		$this->assertContains( 'custom-comment', $feed_types );

		\remove_filter( 'pubsubhubbub_show_discovery_for_comment_feed_types', $filter );
	}

	/**
	 * Test websub_show_discovery_for_feed_types filter works.
	 *
	 * @covers \Pubsubhubbub\show_discovery
	 */
	public function test_websub_show_discovery_for_feed_types_filter() {
		$filter = function ( $feed_types ) {
			$feed_types[] = 'json-feed';
			return $feed_types;
		};

		\add_filter( 'websub_show_discovery_for_feed_types', $filter );

		// Verify the filter modifies the feed types.
		$feed_types = \apply_filters( 'websub_show_discovery_for_feed_types', array( 'atom', 'rss2' ) );
		$this->assertContains( 'json-feed', $feed_types );

		\remove_filter( 'websub_show_discovery_for_feed_types', $filter );
	}

	/**
	 * Test websub_show_discovery_for_comment_feed_types filter works.
	 *
	 * @covers \Pubsubhubbub\show_discovery
	 */
	public function test_websub_show_discovery_for_comment_feed_types_filter() {
		$filter = function ( $feed_types ) {
			$feed_types[] = 'json-comment-feed';
			return $feed_types;
		};

		\add_filter( 'websub_show_discovery_for_comment_feed_types', $filter );

		// Verify the filter modifies the comment feed types.
		$feed_types = \apply_filters( 'websub_show_discovery_for_comment_feed_types', array( 'atom', 'rss2' ) );
		$this->assertContains( 'json-comment-feed', $feed_types );

		\remove_filter( 'websub_show_discovery_for_comment_feed_types', $filter );
	}
}
