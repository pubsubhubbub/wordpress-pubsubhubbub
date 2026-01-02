<?php
/**
 * Test Discovery class.
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\Tests;

use Pubsubhubbub\Discovery;

/**
 * Test class for the Discovery class.
 *
 * @coversDefaultClass \Pubsubhubbub\Discovery
 */
class Test_Discovery extends \WP_UnitTestCase {

	/**
	 * Test add_rss_ns_link outputs correct namespace.
	 *
	 * @covers ::add_rss_ns_link
	 */
	public function test_add_rss_ns_link() {
		\ob_start();
		Discovery::add_rss_ns_link();
		$output = \ob_get_clean();

		$this->assertStringContainsString( 'xmlns:atom="http://www.w3.org/2005/Atom"', $output );
	}

	/**
	 * Test add_atom_link_tag outputs hub links when discovery is shown.
	 *
	 * @covers ::add_atom_link_tag
	 */
	public function test_add_atom_link_tag_with_discovery() {
		// Force show_discovery to return true.
		\add_filter( 'websub_show_discovery', '__return_true' );

		\ob_start();
		Discovery::add_atom_link_tag();
		$output = \ob_get_clean();

		\remove_filter( 'websub_show_discovery', '__return_true' );

		$this->assertStringContainsString( 'rel="hub"', $output );
		$this->assertStringContainsString( '<link', $output );
	}

	/**
	 * Test add_atom_link_tag outputs nothing when discovery is not shown.
	 *
	 * @covers ::add_atom_link_tag
	 */
	public function test_add_atom_link_tag_without_discovery() {
		// Force show_discovery to return false.
		\add_filter( 'websub_show_discovery', '__return_false' );

		\ob_start();
		Discovery::add_atom_link_tag();
		$output = \ob_get_clean();

		\remove_filter( 'websub_show_discovery', '__return_false' );

		$this->assertEmpty( $output );
	}

	/**
	 * Test add_rss_link_tag outputs hub links when discovery is shown.
	 *
	 * @covers ::add_rss_link_tag
	 */
	public function test_add_rss_link_tag_with_discovery() {
		// Force show_discovery to return true.
		\add_filter( 'websub_show_discovery', '__return_true' );

		\ob_start();
		Discovery::add_rss_link_tag();
		$output = \ob_get_clean();

		\remove_filter( 'websub_show_discovery', '__return_true' );

		$this->assertStringContainsString( 'rel="hub"', $output );
		$this->assertStringContainsString( '<atom:link', $output );
	}

	/**
	 * Test add_rss_link_tag outputs nothing when discovery is not shown.
	 *
	 * @covers ::add_rss_link_tag
	 */
	public function test_add_rss_link_tag_without_discovery() {
		// Force show_discovery to return false.
		\add_filter( 'websub_show_discovery', '__return_false' );

		\ob_start();
		Discovery::add_rss_link_tag();
		$output = \ob_get_clean();

		\remove_filter( 'websub_show_discovery', '__return_false' );

		$this->assertEmpty( $output );
	}

	/**
	 * Test add_atom_link_tag outputs all configured hubs.
	 *
	 * @covers ::add_atom_link_tag
	 */
	public function test_add_atom_link_tag_outputs_all_hubs() {
		$custom_hubs = "https://hub1.example.com\nhttps://hub2.example.com";
		\update_option( 'pubsubhubbub_endpoints', $custom_hubs );

		// Force show_discovery to return true.
		\add_filter( 'websub_show_discovery', '__return_true' );

		\ob_start();
		Discovery::add_atom_link_tag();
		$output = \ob_get_clean();

		\remove_filter( 'websub_show_discovery', '__return_true' );
		\delete_option( 'pubsubhubbub_endpoints' );

		$this->assertStringContainsString( 'https://hub1.example.com', $output );
		$this->assertStringContainsString( 'https://hub2.example.com', $output );
	}

	/**
	 * Test add_rss_link_tag outputs all configured hubs.
	 *
	 * @covers ::add_rss_link_tag
	 */
	public function test_add_rss_link_tag_outputs_all_hubs() {
		$custom_hubs = "https://hub1.example.com\nhttps://hub2.example.com";
		\update_option( 'pubsubhubbub_endpoints', $custom_hubs );

		// Force show_discovery to return true.
		\add_filter( 'websub_show_discovery', '__return_true' );

		\ob_start();
		Discovery::add_rss_link_tag();
		$output = \ob_get_clean();

		\remove_filter( 'websub_show_discovery', '__return_true' );
		\delete_option( 'pubsubhubbub_endpoints' );

		$this->assertStringContainsString( 'https://hub1.example.com', $output );
		$this->assertStringContainsString( 'https://hub2.example.com', $output );
	}

	/**
	 * Test deprecated pubsubhubbub_show_discovery filter still works.
	 *
	 * @covers ::add_atom_link_tag
	 * @expectedDeprecated pubsubhubbub_show_discovery
	 */
	public function test_deprecated_show_discovery_filter() {
		// Force show_discovery to return true via deprecated filter.
		\add_filter( 'pubsubhubbub_show_discovery', '__return_true' );

		\ob_start();
		Discovery::add_atom_link_tag();
		$output = \ob_get_clean();

		\remove_filter( 'pubsubhubbub_show_discovery', '__return_true' );

		$this->assertStringContainsString( 'rel="hub"', $output );
	}

	/**
	 * Test that deprecated show_discovery filter runs before new filter.
	 *
	 * @covers ::add_atom_link_tag
	 * @expectedDeprecated pubsubhubbub_show_discovery
	 */
	public function test_deprecated_show_discovery_filter_runs_before_new_filter() {
		$order = array();

		$deprecated_filter = function ( $show ) use ( &$order ) {
			$order[] = 'deprecated';
			return $show;
		};

		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$new_filter = function ( $show ) use ( &$order ) {
			$order[] = 'new';
			return true; // Force true to generate output.
		};

		\add_filter( 'pubsubhubbub_show_discovery', $deprecated_filter );
		\add_filter( 'websub_show_discovery', $new_filter );

		\ob_start();
		Discovery::add_atom_link_tag();
		\ob_get_clean();

		$this->assertEquals( array( 'deprecated', 'new' ), $order );

		\remove_filter( 'pubsubhubbub_show_discovery', $deprecated_filter );
		\remove_filter( 'websub_show_discovery', $new_filter );
	}
}
