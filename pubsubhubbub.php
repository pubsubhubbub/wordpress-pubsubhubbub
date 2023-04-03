<?php
/**
 * Plugin Name: WebSub (FKA. PubSubHubbub)
 * Plugin URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub/
 * Description: A better way to tell the world when your blog is updated.
 * Version: 3.1.3
 * Author: PubSubHubbub Team
 * Author URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: pubsubhubbub
 * Domain Path: /languages
 */

/**
 * Initialize plugin
 */
function pubsubhubbub_init() {
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );

	/**
	 * Publisher integration
	 */
	require_once( dirname( __FILE__ ) . '/includes/class-pubsubhubbub-publisher.php' );

	add_action( 'publish_post', array( 'PubSubHubbub_Publisher', 'publish_post' ) );
	//add_action( 'comment_post', array( 'PubSubHubbub_Publisher', 'publish_comment' ) );

	/**
	 * Admin panel
	 */
	require_once( dirname( __FILE__ ) . '/includes/class-pubsubhubbub-admin.php' );

	add_action( 'admin_init', array( 'PubSubHubbub_Admin', 'register_settings' ) );
	add_action( 'admin_menu', array( 'Pubsubhubbub_Admin', 'add_plugin_menu' ) );

	/**
	 * Feed integrations
	 */
	require_once( dirname( __FILE__ ) . '/includes/class-pubsubhubbub-topics.php' );

	add_action( 'atom_head', array( 'Pubsubhubbub_Topics', 'add_atom_link_tag' ) );
	add_action( 'rdf_header', array( 'Pubsubhubbub_Topics', 'add_rss_link_tag' ) );
	add_action( 'rss2_head', array( 'Pubsubhubbub_Topics', 'add_rss_link_tag' ) );

	add_action( 'comments_atom_head', array( 'Pubsubhubbub_Topics', 'add_atom_link_tag' ) );
	add_action( 'commentsrss2_head', array( 'Pubsubhubbub_Topics', 'add_rss_link_tag' ) );

	add_action( 'rdf_ns', array( 'Pubsubhubbub_Topics', 'add_rss_ns_link' ) );

	add_action( 'template_redirect', array( 'Pubsubhubbub_Topics', 'template_redirect' ) );

	/**
	 * Main class
	 */
	require_once( dirname( __FILE__ ) . '/includes/class-pubsubhubbub.php' );

	add_action( 'init', array( 'PubSubHubbub', 'load_textdomain' ) );

	/**
	 * Deprecated functions
	 */
	require_once( dirname( __FILE__ ) . '/includes/deprecated.php' );
}

add_action( 'plugins_loaded', 'pubsubhubbub_init' );
