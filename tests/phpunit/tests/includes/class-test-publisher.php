<?php
/**
 * Test Publisher class.
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\Tests;

use Pubsubhubbub\Publisher;
use Pubsubhubbub\Pubsubhubbub;

/**
 * Test class for the Publisher class.
 *
 * @coversDefaultClass \Pubsubhubbub\Publisher
 */
class Test_Publisher extends \WP_UnitTestCase {

	/**
	 * Test get_hubs returns array.
	 *
	 * @covers ::get_hubs
	 */
	public function test_get_hubs_returns_array() {
		$hubs = Publisher::get_hubs();
		$this->assertIsArray( $hubs );
	}

	/**
	 * Test get_hubs returns default hubs when no option is set.
	 *
	 * @covers ::get_hubs
	 */
	public function test_get_hubs_returns_defaults() {
		\delete_option( 'pubsubhubbub_endpoints' );
		$hubs = Publisher::get_hubs();

		$this->assertEquals( Pubsubhubbub::DEFAULT_HUBS, $hubs );
	}

	/**
	 * Test get_hubs returns custom hubs when option is set.
	 *
	 * @covers ::get_hubs
	 */
	public function test_get_hubs_returns_custom_hubs() {
		$custom_hubs = "https://hub1.example.com\nhttps://hub2.example.com";
		\update_option( 'pubsubhubbub_endpoints', $custom_hubs );

		$hubs = Publisher::get_hubs();

		$this->assertIsArray( $hubs );
		$this->assertCount( 2, $hubs );
		$this->assertContains( 'https://hub1.example.com', $hubs );
		$this->assertContains( 'https://hub2.example.com', $hubs );

		\delete_option( 'pubsubhubbub_endpoints' );
	}

	/**
	 * Test get_hubs filter.
	 *
	 * @covers ::get_hubs
	 */
	public function test_get_hubs_filter() {
		$filter = function ( $hubs ) {
			$hubs[] = 'https://filtered-hub.example.com';
			return $hubs;
		};

		\add_filter( 'websub_hub_urls', $filter );

		$hubs = Publisher::get_hubs();

		$this->assertContains( 'https://filtered-hub.example.com', $hubs );

		\remove_filter( 'websub_hub_urls', $filter );
	}

	/**
	 * Test publish_update with missing hub URL.
	 *
	 * @covers ::publish_update
	 */
	public function test_publish_update_missing_hub_url() {
		$result = Publisher::publish_update( array( 'https://example.com/feed' ), null );

		$this->assertWPError( $result );
		$this->assertEquals( 'missing_hub_url', $result->get_error_code() );
	}

	/**
	 * Test publish_update with invalid hub URL.
	 *
	 * @covers ::publish_update
	 */
	public function test_publish_update_invalid_hub_url() {
		$result = Publisher::publish_update( array( 'https://example.com/feed' ), 'not-a-url' );

		$this->assertWPError( $result );
		$this->assertEquals( 'invalid_hub_url', $result->get_error_code() );
	}

	/**
	 * Test publish_update with missing topic URL.
	 *
	 * @covers ::publish_update
	 */
	public function test_publish_update_missing_topic_url() {
		$result = Publisher::publish_update( null, 'https://hub.example.com' );

		$this->assertWPError( $result );
		$this->assertEquals( 'missing_topic_url', $result->get_error_code() );
	}

	/**
	 * Test get_feed_urls_by_post_id returns array.
	 *
	 * @covers ::get_feed_urls_by_post_id
	 */
	public function test_get_feed_urls_by_post_id_returns_array() {
		$post_id = self::factory()->post->create();

		$feed_urls = Publisher::get_feed_urls_by_post_id( $post_id );

		$this->assertIsArray( $feed_urls );
		$this->assertNotEmpty( $feed_urls );
	}

	/**
	 * Test get_feed_urls_by_post_id includes main feeds.
	 *
	 * @covers ::get_feed_urls_by_post_id
	 */
	public function test_get_feed_urls_by_post_id_includes_main_feeds() {
		$post_id = self::factory()->post->create();

		$feed_urls = Publisher::get_feed_urls_by_post_id( $post_id );

		$this->assertContains( \get_feed_link( 'atom' ), $feed_urls );
		$this->assertContains( \get_feed_link( 'rss2' ), $feed_urls );
	}

	/**
	 * Test get_feed_urls_by_post_id includes category feeds.
	 *
	 * @covers ::get_feed_urls_by_post_id
	 */
	public function test_get_feed_urls_by_post_id_includes_category_feeds() {
		$category_id = self::factory()->category->create();
		$post_id     = self::factory()->post->create(
			array(
				'post_category' => array( $category_id ),
			)
		);

		$feed_urls = Publisher::get_feed_urls_by_post_id( $post_id );

		$this->assertContains( \get_term_feed_link( $category_id, 'category', 'atom' ), $feed_urls );
		$this->assertContains( \get_term_feed_link( $category_id, 'category', 'rss2' ), $feed_urls );
	}

	/**
	 * Test get_feed_urls_by_post_id includes author feeds.
	 *
	 * @covers ::get_feed_urls_by_post_id
	 */
	public function test_get_feed_urls_by_post_id_includes_author_feeds() {
		$user_id = self::factory()->user->create();
		$post_id = self::factory()->post->create(
			array(
				'post_author' => $user_id,
			)
		);

		$feed_urls = Publisher::get_feed_urls_by_post_id( $post_id );

		$this->assertContains( \get_author_feed_link( $user_id, 'atom' ), $feed_urls );
		$this->assertContains( \get_author_feed_link( $user_id, 'rss2' ), $feed_urls );
	}

	/**
	 * Test publish_feed_urls filter.
	 *
	 * @covers ::get_feed_urls_by_post_id
	 */
	public function test_publish_feed_urls_filter() {
		$post_id = self::factory()->post->create();

		$filter = function ( $feed_urls, $filtered_post_id ) use ( $post_id ) {
			$this->assertEquals( $post_id, $filtered_post_id );
			$feed_urls[] = 'https://custom-feed.example.com';
			return $feed_urls;
		};

		\add_filter( 'websub_feed_urls', $filter, 10, 2 );

		$feed_urls = Publisher::get_feed_urls_by_post_id( $post_id );

		$this->assertContains( 'https://custom-feed.example.com', $feed_urls );

		\remove_filter( 'websub_feed_urls', $filter, 10 );
	}

	/**
	 * Test deprecated pubsubhubbub_hub_urls filter still works.
	 *
	 * @covers ::get_hubs
	 * @expectedDeprecated pubsubhubbub_hub_urls
	 */
	public function test_deprecated_hub_urls_filter() {
		$filter = function ( $hubs ) {
			$hubs[] = 'https://deprecated-hub.example.com';
			return $hubs;
		};

		\add_filter( 'pubsubhubbub_hub_urls', $filter );

		$hubs = Publisher::get_hubs();

		$this->assertContains( 'https://deprecated-hub.example.com', $hubs );

		\remove_filter( 'pubsubhubbub_hub_urls', $filter );
	}

	/**
	 * Test deprecated pubsubhubbub_feed_urls filter still works.
	 *
	 * @covers ::get_feed_urls_by_post_id
	 * @expectedDeprecated pubsubhubbub_feed_urls
	 */
	public function test_deprecated_feed_urls_filter() {
		$post_id = self::factory()->post->create();

		$filter = function ( $feed_urls, $filtered_post_id ) use ( $post_id ) {
			$this->assertEquals( $post_id, $filtered_post_id );
			$feed_urls[] = 'https://deprecated-feed.example.com';
			return $feed_urls;
		};

		\add_filter( 'pubsubhubbub_feed_urls', $filter, 10, 2 );

		$feed_urls = Publisher::get_feed_urls_by_post_id( $post_id );

		$this->assertContains( 'https://deprecated-feed.example.com', $feed_urls );

		\remove_filter( 'pubsubhubbub_feed_urls', $filter, 10 );
	}

	/**
	 * Test that deprecated filter runs before new filter.
	 *
	 * @covers ::get_hubs
	 * @expectedDeprecated pubsubhubbub_hub_urls
	 */
	public function test_deprecated_filter_runs_before_new_filter() {
		$order = array();

		$deprecated_filter = function ( $hubs ) use ( &$order ) {
			$order[] = 'deprecated';
			return $hubs;
		};

		$new_filter = function ( $hubs ) use ( &$order ) {
			$order[] = 'new';
			return $hubs;
		};

		\add_filter( 'pubsubhubbub_hub_urls', $deprecated_filter );
		\add_filter( 'websub_hub_urls', $new_filter );

		Publisher::get_hubs();

		$this->assertEquals( array( 'deprecated', 'new' ), $order );

		\remove_filter( 'pubsubhubbub_hub_urls', $deprecated_filter );
		\remove_filter( 'websub_hub_urls', $new_filter );
	}

	/**
	 * Test deprecated pubsubhubbub_comment_feed_urls filter still works.
	 *
	 * @covers ::publish_comment
	 * @expectedDeprecated pubsubhubbub_comment_feed_urls
	 */
	public function test_deprecated_comment_feed_urls_filter() {
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$filter = function ( $feed_urls, $comment_id ) {
			$feed_urls[] = 'https://deprecated-comment-feed.example.com';
			return $feed_urls;
		};

		\add_filter( 'pubsubhubbub_comment_feed_urls', $filter, 10, 2 );

		// We need to capture the feed URLs before they're published.
		$captured_urls = array();
		$capture       = function ( $feed_urls ) use ( &$captured_urls ) {
			$captured_urls = $feed_urls;
			return array(); // Return empty to prevent actual HTTP requests.
		};
		\add_filter( 'websub_feed_urls', $capture, 999 );

		// Create a comment to trigger the filter.
		$post_id    = self::factory()->post->create();
		$comment_id = self::factory()->comment->create( array( 'comment_post_ID' => $post_id ) );

		// Use reflection to test the filter is applied.
		$feed_urls   = array();
		$feed_urls[] = \get_bloginfo( 'comments_atom_url' );
		$feed_urls[] = \get_bloginfo( 'comments_rss2_url' );
		$feed_urls   = \apply_filters( 'pubsubhubbub_comment_feed_urls', $feed_urls, $comment_id );

		$this->assertContains( 'https://deprecated-comment-feed.example.com', $feed_urls );

		\remove_filter( 'pubsubhubbub_comment_feed_urls', $filter, 10 );
		\remove_filter( 'websub_feed_urls', $capture, 999 );
	}

	/**
	 * Test websub_comment_feed_urls filter works.
	 *
	 * @covers ::publish_comment
	 */
	public function test_websub_comment_feed_urls_filter() {
		$post_id    = self::factory()->post->create();
		$comment_id = self::factory()->comment->create( array( 'comment_post_ID' => $post_id ) );

		$filter = function ( $feed_urls, $filtered_comment_id ) use ( $comment_id ) {
			$this->assertEquals( $comment_id, $filtered_comment_id );
			$feed_urls[] = 'https://custom-comment-feed.example.com';
			return $feed_urls;
		};

		\add_filter( 'websub_comment_feed_urls', $filter, 10, 2 );

		// Apply the filter chain manually to test.
		$feed_urls   = array();
		$feed_urls[] = \get_bloginfo( 'comments_atom_url' );
		$feed_urls[] = \get_bloginfo( 'comments_rss2_url' );
		$feed_urls   = \apply_filters( 'websub_comment_feed_urls', $feed_urls, $comment_id );

		$this->assertContains( 'https://custom-comment-feed.example.com', $feed_urls );

		\remove_filter( 'websub_comment_feed_urls', $filter, 10 );
	}
}
