<?php
/**
 * The WebSub/PubSubHubbub class
 */
class Pubsubhubbub {
	const DEFAULT_HUBS = array(
		'https://pubsubhubbub.appspot.com',
		'https://pubsubhubbub.superfeedr.com',
		'https://websubhub.com/hub',
	);

	/**
	 * Load the plugin textdomain.
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'pubsubhubbub', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}
