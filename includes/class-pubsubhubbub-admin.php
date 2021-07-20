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
			'WebSub Settings',
			'WebSub',
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
					'<p>' . __( 'WebSub is a proposed standard of the IndieWeb stack.', 'pubsubhubbub' ) . '</p>' .
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
					'</p>' .
					'<p>
						<strong>' . __( 'IndieWeb for WordPress', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'Try out "<a href="https://wordpress.org/plugins/indieweb/" target="_blank">IndieWeb for WordPress</a>"', 'pubsubhubbub' ) .
					'</p>',
			)
		);

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'ostatus',
				'title'   => __( 'The Fediverse', 'pubsubhubbub' ),
				'content' =>
					'<p>' . __( 'WebSub is one of the building blocks of OStauts, wich is the base of the Fediverse.', 'pubsubhubbub' ) . '</p>' .
					'<p>
						<strong>' . __( 'The Fediverse', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'Fediverse is a portmanteau of "federation" and "universe". It is a common, informal name for a somewhat broad federation of social network servers whose main purpose is microblogging, the sharing of short, public messages.<br />By running social network software that supports a standard set of protocols called ActivityPub, independently run servers can connect to the Fediverse, allowing its users to follow and receive short messages from each other, regardless of which particular ActivityPub server implementation they are running. The Fediverse is built on Free Software. Some of its social networks are vaguely Twitter-ish in style (for example Mastodon, or GNU Social, similar in activities and their microblogging function), while other federated platforms include more communication and transaction options that are instead comparable to Google+ or Facebook (such as is the case with Friendica). (Wikipedia)', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . __( 'OStatus', 'pubsubhubbub' ) . '</strong><br />' .
						__( ' OStatus lets people on different social networks follow each other. It applies a group of related protocols (PubSubHubbub, ActivityStreams, Salmon, Portable Contacts, and Webfinger) to this problem in what we believe is a simple and obvious way. OStatus is a minimal specification for distributed status updates or microblogging.<br />Many social applications can be modelled with status updates, however. Practically any software that generates RSS or Atom feeds could be OStatus-enabled. Travel networks, event invitation systems, wikis, photo-sharing systems, social news sites, social music sites, podcasting servers, blogs, version control systems, and general purpose social networks would all be candidates for OStatus use. ', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . __( 'OStatus for WordPress', 'pubsubhubbub' ) . '</strong><br />' .
						__( 'Try out "<a href="https://wordpress.org/plugins/ostatus-for-wordpress/" target="_blank">OStatus for WordPress</a>"!', 'pubsubhubbub' ) .
					'</p>',
			)
		);

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'pubsubhubbub' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://websub.rocks/">Test suite</a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . __( '<a href="https://www.w3.org/TR/websub/">W3C Spec</a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . __( '<a href="https://indieweb.org/WebSub">IndieWeb <small>(Wiki)</small></a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . __( '<a href="https://www.w3.org/community/ostatus/">OStatus <small>(W3C Community)</small></a>', 'pubsubhubbub' ) . '</p>' .
			'<hr />' .
			'<p>' . __( '<a href="https://notiz.blog/donate">Donate</a>', 'pubsubhubbub' ) . '</p>'
		);
	}
}
