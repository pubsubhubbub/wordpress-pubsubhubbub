<?php
/**
 * The WebSub/PubSubHubbub class
 */
class Pubsubhubbub {
	/**
	 * Load the plugin textdomain.
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'pubsubhubbub', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}
