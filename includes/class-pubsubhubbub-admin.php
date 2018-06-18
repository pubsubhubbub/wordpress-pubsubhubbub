<?php
/**
 *
 */
class Pubsubhubbub_Admin {
	/**
	 * Add a link to our settings page in the WP menu
	 */
	public static function add_plugin_menu() {
		add_options_page(
			'WebSub/PubSubHubbub Settings',
			'WebSub/PubSubHubbub',
			'administrator',
			'pubsubhubbub',
			array(
				'Pubsubhubbub_Admin',
				'add_settings_page',
			)
		);
	}

	/**
	 * Write the content for our settings page that allows you to
	 * define your endpoints
	 */
	public static function add_settings_page() {
		load_template( plugin_dir_path( __FILE__ ) . '../templates/settings-page.php' );
	}

	/**
	 * Register PubSubHubbub settings
	 */
	public static function register_settings() {
		register_setting(
			'pubsubhubbub', 'pubsubhubbub_endpoints', array(
				'type'         => 'string',
				'description'  => __( 'The WebSub/PubSubHubbub endpoints', 'pubsubhubbub' ),
				'show_in_rest' => true,
				'default'      => '',
			)
		);
	}
}
