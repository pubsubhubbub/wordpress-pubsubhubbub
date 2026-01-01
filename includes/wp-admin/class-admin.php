<?php
/**
 * Admin Class
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub\WP_Admin;

use Pubsubhubbub\Pubsubhubbub;

/**
 * Admin Class
 *
 * Handles admin panel management for WebSub/PubSubHubbub.
 *
 * @package Pubsubhubbub
 */
class Admin {

	/**
	 * Add a link to our settings page in the WP menu.
	 *
	 * @return void
	 */
	public static function add_plugin_menu() {
		$options_page = \add_options_page(
			\__( 'WebSub Settings', 'pubsubhubbub' ),
			\__( 'WebSub', 'pubsubhubbub' ),
			'manage_options',
			'pubsubhubbub',
			array( static::class, 'settings_page' )
		);

		\add_action( 'load-' . $options_page, array( static::class, 'register_settings_fields' ) );
		\add_action( 'load-' . $options_page, array( static::class, 'add_help_tab' ) );
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public static function settings_page() {
		?>
		<div class="wrap">
			<h1><?php \esc_html_e( 'WebSub', 'pubsubhubbub' ); ?> <small><?php \esc_html_e( '(FKA. PubSubHubbub)', 'pubsubhubbub' ); ?></small></h1>

			<form method="post" action="options.php">
				<?php
				\settings_fields( 'pubsubhubbub' );
				\do_settings_sections( 'pubsubhubbub' );
				\submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register settings sections and fields.
	 *
	 * @return void
	 */
	public static function register_settings_fields() {
		\add_settings_section(
			'publisher',
			\__( 'Publisher Settings', 'pubsubhubbub' ),
			array( static::class, 'render_publisher_section' ),
			'pubsubhubbub'
		);

		\add_settings_field(
			'pubsubhubbub_endpoints',
			\__( 'Hubs', 'pubsubhubbub' ),
			array( static::class, 'render_endpoints_field' ),
			'pubsubhubbub',
			'publisher',
			array(
				'label_for' => 'pubsubhubbub_endpoints',
			)
		);
	}

	/**
	 * Render publisher section description.
	 *
	 * @return void
	 */
	public static function render_publisher_section() {
		echo '<p>' . \esc_html__( 'A WebSub Publisher is an implementation that advertises a topic and hub URL on one or more resource URLs.', 'pubsubhubbub' ) . '</p>';
	}

	/**
	 * Render endpoints field.
	 *
	 * @return void
	 */
	public static function render_endpoints_field() {
		$endpoints = \get_option( 'pubsubhubbub_endpoints' );
		?>
		<textarea
			name="pubsubhubbub_endpoints"
			id="pubsubhubbub_endpoints"
			rows="10"
			cols="50"
			class="large-text code"
		><?php echo \esc_textarea( $endpoints ); ?></textarea>
		<p class="description">
			<?php \esc_html_e( 'Add one hub URL per line. These hubs will be notified when you publish new content.', 'pubsubhubbub' ); ?>
		</p>
		<?php
	}

	/**
	 * Register PubSubHubbub settings.
	 *
	 * @return void
	 */
	public static function register_settings() {
		\register_setting(
			'pubsubhubbub',
			'pubsubhubbub_endpoints',
			array(
				'type'              => 'string',
				'description'       => \__( 'The WebSub/PubSubHubbub endpoints', 'pubsubhubbub' ),
				'show_in_rest'      => true,
				'default'           => \implode( PHP_EOL, Pubsubhubbub::DEFAULT_HUBS ),
				'sanitize_callback' => array( static::class, 'sanitize_endpoints' ),
			)
		);
	}

	/**
	 * Sanitize the endpoints setting.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public static function sanitize_endpoints( $value ) {
		$value = \explode( PHP_EOL, $value );
		$value = \array_filter( \array_map( 'trim', $value ) );
		$value = \array_filter( \array_map( 'esc_url_raw', $value ) );
		$value = \implode( PHP_EOL, $value );

		return $value;
	}

	/**
	 * Add help tabs to the settings page.
	 *
	 * @return void
	 */
	public static function add_help_tab() {
		\get_current_screen()->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => \__( 'Overview', 'pubsubhubbub' ),
				'content' => '<p>' . \__( 'WebSub provides a common mechanism for communication between publishers of any kind of Web content and their subscribers, based on HTTP web hooks. Subscription requests are relayed through hubs, which validate and verify the request. Hubs then distribute new and updated content to subscribers when it becomes available. WebSub was previously known as PubSubHubbub.', 'pubsubhubbub' ) . '</p>',
			)
		);

		\get_current_screen()->add_help_tab(
			array(
				'id'      => 'terms',
				'title'   => \__( 'Terms', 'pubsubhubbub' ),
				'content' =>
					'<p>
						<strong>' . \__( 'Publisher', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'A WebSub Publisher is an implementation that advertises a topic and hub URL on one or more resource URLs.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . \__( 'Subscriber', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'A WebSub Subscriber is an implementation that discovers the hub and topic URL given a resource URL, subscribes to updates at the hub, and accepts content distribution requests from the hub. The subscriber MAY support authenticated content distribution.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . \__( 'Hub', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'A WebSub Hub is an implementation that handles subscription requests and distributes the content to subscribers when the corresponding topic URL has been updated. Hubs MUST support subscription requests with a secret and deliver authenticated requests when requested. Hubs MUST deliver the full contents of the topic URL in the request, and MAY reduce the payload to a diff if the content type supports it.', 'pubsubhubbub' ) .
					'</p>',
			)
		);

		\get_current_screen()->add_help_tab(
			array(
				'id'      => 'indieweb',
				'title'   => \__( 'The IndieWeb', 'pubsubhubbub' ),
				'content' =>
					'<p>' . \__( 'WebSub is part of the IndieWeb stack.', 'pubsubhubbub' ) . '</p>' .
					'<p>' . \__( 'The IndieWeb is a people-focused alternative to the "corporate web".', 'pubsubhubbub' ) . '</p>' .
					'<p>
						<strong>' . \__( 'Your content is yours', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'When you post something on the web, it should belong to you, not a corporation. Too many companies have gone out of business and lost all of their users\' data. By joining the IndieWeb, your content stays yours and in your control.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . \__( 'You are better connected', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'Your articles and status messages can go to all services, not just one, allowing you to engage with everyone. Even replies and likes on other services can come back to your site so they\'re all in one place.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . \__( 'You are in control', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'You can post anything you want, in any format you want, with no one monitoring you. In addition, you share simple readable links such as example.com/ideas. These links are permanent and will always work.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . \__( 'IndieWeb for WordPress', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'Try out "<a href="https://wordpress.org/plugins/indieweb/" target="_blank">IndieWeb for WordPress</a>"', 'pubsubhubbub' ) .
					'</p>',
			)
		);

		\get_current_screen()->add_help_tab(
			array(
				'id'      => 'fediverse',
				'title'   => \__( 'The Fediverse', 'pubsubhubbub' ),
				'content' =>
					'<p>' . \__( 'WebSub is one of the building blocks of OStatus, which is the base of the Fediverse.', 'pubsubhubbub' ) . '</p>' .
					'<p>
						<strong>' . \__( 'The Fediverse', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'Fediverse is a portmanteau of "federation" and "universe". It is a common name for a broad federation of social network servers. By running social network software that supports protocols like ActivityPub, independently run servers can connect to the Fediverse.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . \__( 'OStatus', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'OStatus lets people on different social networks follow each other. It applies a group of related protocols (PubSubHubbub, ActivityStreams, Salmon, Portable Contacts, and Webfinger) to enable distributed status updates or microblogging.', 'pubsubhubbub' ) .
					'</p>' .
					'<p>
						<strong>' . \__( 'OStatus for WordPress', 'pubsubhubbub' ) . '</strong><br />' .
						\__( 'Try out "<a href="https://wordpress.org/plugins/ostatus-for-wordpress/" target="_blank">OStatus for WordPress</a>"!', 'pubsubhubbub' ) .
					'</p>',
			)
		);

		\get_current_screen()->set_help_sidebar(
			'<p><strong>' . \__( 'For more information:', 'pubsubhubbub' ) . '</strong></p>' .
			'<p>' . \__( '<a href="https://websub.rocks/">Test suite</a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . \__( '<a href="https://www.w3.org/TR/websub/">W3C Spec</a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . \__( '<a href="https://indieweb.org/WebSub">IndieWeb Wiki</a>', 'pubsubhubbub' ) . '</p>' .
			'<p>' . \__( '<a href="https://www.w3.org/community/ostatus/">OStatus Community</a>', 'pubsubhubbub' ) . '</p>'
		);
	}
}
