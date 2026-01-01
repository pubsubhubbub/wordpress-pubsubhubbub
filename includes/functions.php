<?php
/**
 * Helper functions for Pubsubhubbub.
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub;

/**
 * The ability for other plugins to hook into the PuSH code.
 *
 * @param array $feed_urls A list of feed urls you want to publish.
 *
 * @return void
 */
function publish_to_hub( $feed_urls ) {
	Publisher::publish_to_hub( $feed_urls );
}

/**
 * Get the endpoints from the WordPress options table.
 *
 * @uses apply_filters() Calls 'websub_hub_urls' filter.
 *
 * @return array The hub URLs.
 */
function get_hubs() {
	return Publisher::get_hubs();
}

/**
 * Check if link supports WebSub.
 *
 * @return boolean
 */
function show_discovery() {
	$show_discovery = false;

	/**
	 * Filter the list of feed types that show WebSub discovery.
	 *
	 * @since 4.0.0
	 *
	 * @param array $feed_types List of feed types (e.g., 'atom', 'rss2').
	 */
	$supported_feed_types = \apply_filters_deprecated( 'pubsubhubbub_show_discovery_for_feed_types', array( get_supported_feed_types() ), '4.0.0', 'websub_show_discovery_for_feed_types' );
	$supported_feed_types = \apply_filters( 'websub_show_discovery_for_feed_types', $supported_feed_types );

	/**
	 * Filter the list of comment feed types that show WebSub discovery.
	 *
	 * @since 4.0.0
	 *
	 * @param array $feed_types List of comment feed types (e.g., 'atom', 'rss2').
	 */
	$supported_comment_feed_types = \apply_filters_deprecated( 'pubsubhubbub_show_discovery_for_comment_feed_types', array( get_supported_comment_feed_types() ), '4.0.0', 'websub_show_discovery_for_comment_feed_types' );
	$supported_comment_feed_types = \apply_filters( 'websub_show_discovery_for_comment_feed_types', $supported_comment_feed_types );

	if (
		( \is_feed( $supported_feed_types ) && ! \is_date() && ! \is_post_type_archive() && ! \is_singular() ) ||
		( \is_feed( $supported_comment_feed_types ) && \is_singular() ) ||
		( \is_home() && \current_theme_supports( 'microformats2' ) )
	) {
		$show_discovery = true;
	}

	/**
	 * Filter whether to show WebSub discovery links.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $show_discovery Whether to show discovery links.
	 */
	$show_discovery = \apply_filters_deprecated( 'pubsubhubbub_show_discovery', array( $show_discovery ), '4.0.0', 'websub_show_discovery' );
	$show_discovery = \apply_filters( 'websub_show_discovery', $show_discovery );

	return $show_discovery;
}

/**
 * Get the correct self URL.
 *
 * @return string The self link URL.
 */
function get_self_link() {
	$host = \wp_parse_url( \home_url() );
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? \wp_unslash( $_SERVER['REQUEST_URI'] ) : '';

	return \esc_url( \apply_filters( 'self_link', \set_url_scheme( 'http://' . $host['host'] . $request_uri ) ) );
}

/**
 * Return the list of feed types that are supported by PubSubHubbub.
 *
 * @return array List of supported feed types.
 */
function get_supported_feed_types() {
	/**
	 * Filter the list of supported feed types for WebSub.
	 *
	 * @since 4.0.0
	 *
	 * @param array $feed_types List of supported feed types. Default: array( 'atom', 'rss2' ).
	 */
	$feed_types = \apply_filters_deprecated( 'pubsubhubbub_supported_feed_types', array( array( 'atom', 'rss2' ) ), '4.0.0', 'websub_supported_feed_types' );
	$feed_types = \apply_filters( 'websub_supported_feed_types', $feed_types );

	return $feed_types;
}

/**
 * Return the list of comment feed types that are supported by PubSubHubbub.
 *
 * @return array List of supported comment feed types.
 */
function get_supported_comment_feed_types() {
	/**
	 * Filter the list of supported comment feed types for WebSub.
	 *
	 * @since 4.0.0
	 *
	 * @param array $feed_types List of supported comment feed types. Default: array( 'atom', 'rss2' ).
	 */
	$feed_types = \apply_filters_deprecated( 'pubsubhubbub_supported_comment_feed_types', array( array( 'atom', 'rss2' ) ), '4.0.0', 'websub_supported_comment_feed_types' );
	$feed_types = \apply_filters( 'websub_supported_comment_feed_types', $feed_types );

	return $feed_types;
}
