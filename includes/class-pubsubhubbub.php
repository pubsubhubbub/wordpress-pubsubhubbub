<?php
/**
 * Pubsubhubbub Class
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub;

use Pubsubhubbub\WP_Admin\Admin;
use Pubsubhubbub\Rest\Subscriber_Controller;

/**
 * Pubsubhubbub Class
 *
 * @package Pubsubhubbub
 */
class Pubsubhubbub {

	/**
	 * Instance of the class.
	 *
	 * @var Pubsubhubbub
	 */
	private static $instance;

	/**
	 * Default hub URLs.
	 *
	 * @var array
	 */
	const DEFAULT_HUBS = array(
		'https://pubsubhubbub.appspot.com',
		'https://pubsubhubbub.superfeedr.com',
		'https://websubhub.com/hub',
	);

	/**
	 * Whether the class has been initialized.
	 *
	 * @var boolean
	 */
	private $initialized = false;

	/**
	 * Get the instance of the class.
	 *
	 * @return Pubsubhubbub
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Do not allow multiple instances of the class.
	 */
	private function __construct() {
		// Do nothing.
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}

		$this->register_hooks();
		$this->register_admin_hooks();
		$this->register_rest_hooks();
		$this->register_subscriber_hooks();

		$this->initialized = true;
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public function get_version() {
		return PUBSUBHUBBUB_VERSION;
	}

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		// Publisher integration.
		\add_action( 'publish_post', array( Publisher::class, 'publish_post' ) );
		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// Uncomment to enable comment publishing: \add_action( 'comment_post', array( Publisher::class, 'publish_comment' ) );

		// Feed integrations.
		\add_action( 'atom_head', array( Discovery::class, 'add_atom_link_tag' ) );
		\add_action( 'rdf_header', array( Discovery::class, 'add_rss_link_tag' ) );
		\add_action( 'rss2_head', array( Discovery::class, 'add_rss_link_tag' ) );

		\add_action( 'comments_atom_head', array( Discovery::class, 'add_atom_link_tag' ) );
		\add_action( 'commentsrss2_head', array( Discovery::class, 'add_rss_link_tag' ) );

		\add_action( 'rdf_ns', array( Discovery::class, 'add_rss_ns_link' ) );

		\add_action( 'template_redirect', array( Discovery::class, 'template_redirect' ) );
	}

	/**
	 * Register admin hooks.
	 */
	public function register_admin_hooks() {
		\add_action( 'admin_init', array( Admin::class, 'register_settings' ) );
		\add_action( 'admin_menu', array( Admin::class, 'add_plugin_menu' ) );
	}

	/**
	 * Register REST API hooks.
	 */
	public function register_rest_hooks() {
		\add_action( 'rest_api_init', array( Subscriber_Controller::class, 'register_routes' ) );
	}

	/**
	 * Register subscriber action hooks.
	 *
	 * These hooks allow other plugins to trigger subscriptions via do_action().
	 */
	public function register_subscriber_hooks() {
		\add_action( 'websub_subscribe', array( Subscriber::class, 'subscribe' ), 10, 3 );
		\add_action( 'websub_unsubscribe', array( Subscriber::class, 'unsubscribe' ), 10, 3 );
	}
}
