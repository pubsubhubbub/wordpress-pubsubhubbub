<?php
/**
 * Test Pubsubhubbub class.
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\Tests;

use Pubsubhubbub\Pubsubhubbub;

/**
 * Test class for the main Pubsubhubbub class.
 *
 * @coversDefaultClass \Pubsubhubbub\Pubsubhubbub
 */
class Test_Pubsubhubbub extends \WP_UnitTestCase {

	/**
	 * Test plugin initialization.
	 *
	 * @covers ::get_instance
	 */
	public function test_get_instance() {
		$this->assertInstanceOf( Pubsubhubbub::class, Pubsubhubbub::get_instance() );
	}

	/**
	 * Test singleton pattern.
	 *
	 * @covers ::get_instance
	 */
	public function test_singleton() {
		$instance1 = Pubsubhubbub::get_instance();
		$instance2 = Pubsubhubbub::get_instance();

		$this->assertSame( $instance1, $instance2 );
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
	 * Test get_version method.
	 *
	 * @covers ::get_version
	 */
	public function test_get_version() {
		$instance = Pubsubhubbub::get_instance();
		$version  = $instance->get_version();

		$this->assertIsString( $version );
		$this->assertNotEmpty( $version );
		$this->assertEquals( PUBSUBHUBBUB_VERSION, $version );
	}
}
