<?php
/**
 * The WebSub/PubSubHubbub admin class
 */
class Pubsubhubbub_Admin {
	/**
	 * Add a link to our settings page in the WP menu
	 */
	public static function add_plugin_menu() {
		$options_page = add_options_page(
			'WebSub/PubSubHubbub Settings',
			'WebSub/PubSubHubbub',
			'administrator',
			'pubsubhubbub',
			array(
				'Pubsubhubbub_Admin',
				'add_settings_page',
			)
		);

		add_action( 'load-' . $options_page, array( 'Pubsubhubbub_Admin', 'add_help_tab' ) );
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

	public static function add_help_tab() {
		get_current_screen()->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'pubsubhubbub' ),
				'content' => '<p>' . __( 'WebSub provides a common mechanism for communication between publishers of any kind of Web content and their subscribers, based on HTTP web hooks. Subscription requests are relayed through hubs, which validate and verify the request. Hubs then distribute new and updated content to subscribers when it becomes available. WebSub was previously known as PubSubHubbub.', 'pubsubhubbub' ) . '</p>',
			)
		);

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'screencast',
				'title'   => __( 'Terms', 'pubsubhubbub' ),
				'content' =>
					'<p>
						<strong>' . __( 'Publisher', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'A WebSub Publisher is an implementation that advertises a topic and hub URL on one or more resource URLs.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . __( 'Subscriber', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'A WebSub Subscriber is an implementation that discovers the hub and topic URL given a resource URL, subscribes to updates at the hub, and accepts content distribution requests from the hub. The subscriber MAY support authenticated content distribution.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . __( 'Hub', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'A WebSub Hub is an implementation that handles subscription requests and distributes the content to subscribers when the corresponding topic URL has been updated. Hubs MUST support subscription requests with a secret and deliver authenticated requests when requested. Hubs MUST deliver the full contents of the topic URL in the request, and MAY reduce the payload to a diff if the content type supports it.', 'pubsubhubbub' ) .
					'</p>',
			)
		);

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'indieweb',
				'title'   => __( 'The IndieWeb', 'pubsubhubbub' ),
				'content' =>
					'<p>' . __( 'The IndieWeb is a people-focused alternative to the "corporate web".', 'pubsubhubbub' ) . '</p>' .
					'<p>
						<strong>' . __( 'Your content is yours', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'When you post something on the web, it should belong to you, not a corporation. Too many companies have gone out of business and lost all of their users’ data. By joining the IndieWeb, your content stays yours and in your control.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . __( 'You are better connected', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'Your articles and status messages can go to all services, not just one, allowing you to engage with everyone. Even replies and likes on other services can come back to your site so they’re all in one place.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . __( 'You are in control', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'You can post anything you want, in any format you want, with no one monitoring you. In addition, you share simple readable links such as example.com/ideas. These links are permanent and will always work.', 'pubsubhubbub' ) .
					'</p>',
			)
		);

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'pubsubhubbub' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://indieweb.org/WebSub">IndieWeb Wiki page</a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . __( '<a href="https://websub.rocks/">Test suite</a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . __( '<a href="https://www.w3.org/TR/websub/">W3C Spec</a>', 'pubsubhubbub' ) . '</p>'
		);
	}
}
