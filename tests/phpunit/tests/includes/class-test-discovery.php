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
}
