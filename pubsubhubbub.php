<?php
/**
 * Plugin Name: WebSub/PubSubHubbub
 * Plugin URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub/
 * Description: A better way to tell the world when your blog is updated.
 * Version: 2.0.0
 * Author: Matthias Pfefferle
 * Author Email: pfefferle@gmail.com
 * Author URI: https://notiz.blog/
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: pubsubhubbub
 * Domain Path: /languages
 */

add_action( 'plugins_loaded', array( 'PubSubHubbub_Plugin', 'init' ) );

class PubSubHubbub_Plugin {

	/**
	 * Initialize plugin
	 */
	public static function init() {
		require_once( dirname( __FILE__ ) . '/includes/pubsubhubbub-publisher.php' );
		require_once( dirname( __FILE__ ) . '/includes/functions.php' );

		add_action( 'publish_post', array( 'PubSubHubbub_Plugin', 'publish_post' ) );
		add_action( 'comment_post', array( 'PubSubHubbub_Plugin', 'publish_comment' ) );

		add_action( 'atom_head', array( 'PubSubHubbub_Plugin', 'add_atom_link_tag' ) );
		add_action( 'comments_atom_head', array( 'PubSubHubbub_Plugin', 'add_atom_link_tag' ) );

		add_action( 'rss_head', array( 'PubSubHubbub_Plugin', 'add_rss_link_tag' ) );
		add_action( 'rdf_header', array( 'PubSubHubbub_Plugin', 'add_rss_link_tag' ) );
		add_action( 'rss2_head', array( 'PubSubHubbub_Plugin', 'add_rss_link_tag' ) );
		add_action( 'commentsrss2_head', array( 'PubSubHubbub_Plugin', 'add_rss_link_tag' ) );

		add_action( 'rdf_ns', array( 'PubSubHubbub_Plugin', 'add_rdf_ns_link' ) );

		add_action( 'do_feed_rss', array( 'PubSubHubbub_Plugin', 'start_rss_link_tag' ), 9 ); // run before output
		add_action( 'do_feed_rss', array( 'PubSubHubbub_Plugin', 'end_rss_link_tag' ), 11 ); // run after output

		add_action( 'admin_menu', array( 'PubSubHubbub_Plugin', 'add_plugin_menu' ) );
		add_action( 'admin_init', array( 'PubSubHubbub_Plugin', 'register_settings' ) );

		add_filter( 'plugin_action_links', array( 'PubSubHubbub_Plugin', 'add_settings_link' ), 10, 2 );

		add_action( 'template_redirect', array( 'PubSubHubbub_Plugin', 'template_redirect' ) );

		add_action( 'init', array( 'PubSubHubbub_Plugin', 'load_textdomain' ) );

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
		$feed_urls = pubsubhubbub_get_feed_urls( $post_id );

		// publish them
		publish_to_hub( $feed_urls );

		return $post_id;
	}

	/**
	 * Function that is called whenever a new comment is published
	 *
	 * @param int $comment_id the comment-id
	 * @return int the comment-id
	 */
	public static function publish_comment( $comment_id ) {
		$feed_urls = pubsubhubbub_get_comment_feed_urls( $comment_id );

		// publish them
		publish_to_hub( $feed_urls );

		return $comment_id;
	}

	/**
	 * Add hub-<link> to the atom feed
	 */
	public static function add_atom_link_tag() {
		$hub_urls = pubsubhubbub_get_hubs();

		foreach ( $hub_urls as $hub_url ) {
			printf( '<link rel="hub" href="%s" />', $hub_url ) . PHP_EOL;
		}
	}

	/**
	 * Add hub-<link> to the rss/rdf feed
	 */
	public static function add_rss_link_tag() {
		$hub_urls = pubsubhubbub_get_hubs();

		foreach ( $hub_urls as $hub_url ) {
			printf( '<atom:link rel="hub" href="%s"/>', $hub_url ) . PHP_EOL;
		}
	}

	/**
	 * Add atom namespace to rdf-feed
	 */
	public static function add_rdf_ns_link() {
		echo ' xmlns:atom="http://www.w3.org/2005/Atom" ' . PHP_EOL;
	}

	/**
	 * Hack to add the atom definition to the RSS feed
	 * start capturing the feed output. this is run at priority 9 (before output)
	 */
	public static function start_rss_link_tag() {
		ob_start();
	}

	/**
	 * This is run at priority 11 (after output)
	 * add in the xmlns atom definition link
	 */
	public static function end_rss_link_tag() {
		$feed = ob_get_clean();
		$pattern = '/<rss version="(.+)">/i';
		$replacement = '<rss version="$1" xmlns:atom="http://www.w3.org/2005/Atom">';
		// change <rss version="X.XX"> to <rss version="X.XX" xmlns:atom="http://www.w3.org/2005/Atom">
		echo preg_replace( $pattern, $replacement, $feed );
	}

	/**
	 * Add a link to our settings page in the WP menu
	 */
	public static function add_plugin_menu() {
		add_options_page( 'WebSub/PubSubHubbub_Plugin Settings', 'PubSubHubbub_Plugin', 'administrator', 'pubsubhubbub', array( 'PubSubHubbub_Plugin', 'add_settings_page' ) );
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
	 * Adds some query vars
	 *
	 * @param array $vars a list of query-vars
	 * @return array the list with the added PuSH params
	 */
	public static function query_var( $vars ) {
		$vars[] = 'hub_mode';
		$vars[] = 'hub_challenge';
		$vars[] = 'hub_topic';
		$vars[] = 'hub_url';

		return $vars;
	}

	/**
	 * Adds link headers as defined in the current v0.4 draft
	 */
	public static function template_redirect() {
		global $wp;

		// get all feeds
		$feed_urls = pubsubhubbub_get_feed_urls();
		$comment_feed_urls = pubsubhubbub_get_comment_feed_urls();

		// get current url
		$urls = array_unique( array_merge( $feed_urls, $comment_feed_urls ) );
		$current_url = home_url( add_query_arg( null, null ) );

		// check if current url is one of the feed urls
		if ( in_array( $current_url, $urls ) ) {
			$hub_urls = pubsubhubbub_get_hubs();
			// add all "hub" headers
			foreach ( $hub_urls as $hub_url ) {
				header( sprintf( 'Link: <%s>; rel="hub"', $hub_url ), false );
			}
			// add the "self" header
			header( sprintf( 'Link: <%s>; rel="self"', $current_url ), false );
		}
	}

	/**
	 *
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
