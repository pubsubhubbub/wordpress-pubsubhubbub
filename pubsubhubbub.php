<?php
/**
 * Plugin Name: WebSub/PubSubHubbub
 * Plugin URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub/
 * Description: A better way to tell the world when your blog is updated.
 * Version: 2.2.1
 * Author: Matthias Pfefferle
 * Author URI: https://notiz.blog/
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: pubsubhubbub
 * Domain Path: /languages
 */

add_action( 'init', array( 'PubSubHubbub_Plugin', 'load_textdomain' ) );
add_action( 'plugins_loaded', array( 'PubSubHubbub_Plugin', 'init' ) );

class PubSubHubbub_Plugin {

	/**
	 * Initialize plugin
	 */
	public static function init() {
		require_once( dirname( __FILE__ ) . '/includes/functions.php' );

		add_action( 'publish_post', array( 'PubSubHubbub_Plugin', 'publish_post' ) );
		add_action( 'comment_post', array( 'PubSubHubbub_Plugin', 'publish_comment' ) );

		add_action( 'atom_head', array( 'PubSubHubbub_Plugin', 'add_atom_link_tag' ) );
		add_action( 'comments_atom_head', array( 'PubSubHubbub_Plugin', 'add_atom_link_tag' ) );

		add_action( 'rdf_header', array( 'PubSubHubbub_Plugin', 'add_rss_link_tag' ) );
		add_action( 'rss2_head', array( 'PubSubHubbub_Plugin', 'add_rss_link_tag' ) );
		add_action( 'commentsrss2_head', array( 'PubSubHubbub_Plugin', 'add_rss_link_tag' ) );

		add_action( 'rdf_ns', array( 'PubSubHubbub_Plugin', 'add_rss_ns_link' ) );

		add_action( 'admin_menu', array( 'PubSubHubbub_Plugin', 'add_plugin_menu' ) );
		add_action( 'admin_init', array( 'PubSubHubbub_Plugin', 'register_settings' ) );

		add_filter( 'plugin_action_links', array( 'PubSubHubbub_Plugin', 'add_settings_link' ), 10, 2 );

		add_action( 'template_redirect', array( 'PubSubHubbub_Plugin', 'template_redirect' ) );

		require_once( dirname( __FILE__ ) . '/includes/deprecated.php' );
	}

	/**
	 * Function that is called whenever a new post is published
	 *
	 * @param int $post_id the post-id
	 * @return int the post-id
	 */
	public static function publish_post( $post_id ) {
		// we want to notify the hub for every feed
		$feed_urls = array();
		$feed_urls[] = get_bloginfo( 'atom_url' );
		$feed_urls[] = get_bloginfo( 'rdf_url' );
		$feed_urls[] = get_bloginfo( 'rss2_url' );

		$feed_urls = apply_filters( 'pubsubhubbub_feed_urls', $feed_urls, $post_id );

		// publish them
		pubsubhubbub_publish_to_hub( $feed_urls );
	}

	/**
	 * Function that is called whenever a new comment is published
	 *
	 * @param int $comment_id the comment-id
	 * @return int the comment-id
	 */
	public static function publish_comment( $comment_id ) {
		// get default comment-feeds
		$feed_urls = array();
		$feed_urls[] = get_bloginfo( 'comments_atom_url' );
		$feed_urls[] = get_bloginfo( 'comments_rss2_url' );

		$feed_urls = apply_filters( 'pubsubhubbub_comment_feed_urls', $feed_urls, $comment_id );

		// publish them
		pubsubhubbub_publish_to_hub( $feed_urls );
	}

	/**
	 * Add hub-<link> to the atom feed
	 */
	public static function add_atom_link_tag() {
		// check if current url is one of the feed urls
		if ( ! pubsubhubbub_show_discovery() ) {
			return;
		}

		$hub_urls = pubsubhubbub_get_hubs();

		foreach ( $hub_urls as $hub_url ) {
			printf( '<link rel="hub" href="%s" />', $hub_url ) . PHP_EOL;
		}
	}

	/**
	 * Add hub-<link> to the rss/rdf feed
	 */
	public static function add_rss_link_tag() {
		// check if current url is one of the feed urls
		if ( ! pubsubhubbub_show_discovery() ) {
			return;
		}

		$hub_urls = pubsubhubbub_get_hubs();

		foreach ( $hub_urls as $hub_url ) {
			printf( '<atom:link rel="hub" href="%s"/>', $hub_url ) . PHP_EOL;
		}
	}

	/**
	 * Add atom namespace to rdf-feed
	 */
	public static function add_rss_ns_link() {
		echo ' xmlns:atom="http://www.w3.org/2005/Atom" ' . PHP_EOL;
	}

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
				'PubSubHubbub_Plugin',
				'add_settings_page',
			)
		);
	}

	/**
	 * Write the content for our settings page that allows you to
	 * define your endpoints
	 */
	public static function add_settings_page() {
		load_template( plugin_dir_path( __FILE__ ) . 'templates/settings-page.php' );
	}

	/**
	 * Add a settings link next to deactive / edit
	 */
	public static function add_settings_link( $links, $file ) {
		if ( 'pubsubhubbub/pubsubhubbub.php' == $file && function_exists( 'admin_url' ) ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=pubsubhubbub' ) . '">' . __( 'Settings' ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		}
		return $links;
	}

	/**
	 * Adds link headers as defined in the current v0.4 draft
	 */
	public static function template_redirect() {
		// check if current url is one of the feed urls
		if ( ! pubsubhubbub_show_discovery() ) {
			return false;
		}

		$hub_urls = pubsubhubbub_get_hubs();
		// add all "hub" headers
		foreach ( $hub_urls as $hub_url ) {
			header( sprintf( 'Link: <%s>; rel="hub"', $hub_url ), false );
		}

		// add the "self" header
		header( sprintf( 'Link: <%s>; rel="self"', pubsubhubbub_get_self_link() ), false );
	}

	/**
	 * Register PubSubHubbub settings
	 */
	public static function register_settings() {
		register_setting( 'pubsubhubbub_options', 'pubsubhubbub_endpoints' );
	}

	/**
	 * Load the plugin textdomain.
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'pubsubhubbub', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}
