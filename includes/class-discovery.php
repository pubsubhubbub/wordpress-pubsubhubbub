<?php
/**
 * Discovery Class
 *
 * @package Pubsubhubbub
 */

namespace Pubsubhubbub;

/**
 * Discovery Class
 *
 * Manages feed discovery and link generation for WebSub.
 *
 * @package Pubsubhubbub
 */
class Discovery {

	/**
	 * Add hub and self links to the Atom feed.
	 *
	 * @see https://www.w3.org/TR/websub/#discovery
	 *
	 * @return void
	 */
	public static function add_atom_link_tag() {
		// Check if current url is one of the feed urls.
		if ( ! show_discovery() ) {
			return;
		}

		$hub_urls = get_hubs();

		foreach ( $hub_urls as $hub_url ) {
			\printf( '<link rel="hub" href="%s" />' . PHP_EOL, \esc_url( $hub_url ) );
		}

		// Add self link for WebSub discovery.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_self_link() already escapes.
		\printf( '<link rel="self" href="%s" />' . PHP_EOL, get_self_link() );
	}

	/**
	 * Add hub and self links to the RSS/RDF feed.
	 *
	 * @see https://www.w3.org/TR/websub/#discovery
	 *
	 * @return void
	 */
	public static function add_rss_link_tag() {
		// Check if current url is one of the feed urls.
		if ( ! show_discovery() ) {
			return;
		}

		$hub_urls = get_hubs();

		foreach ( $hub_urls as $hub_url ) {
			\printf( '<atom:link rel="hub" href="%s"/>' . PHP_EOL, \esc_url( $hub_url ) );
		}

		// Add self link for WebSub discovery.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_self_link() already escapes.
		\printf( '<atom:link rel="self" href="%s"/>' . PHP_EOL, get_self_link() );
	}

	/**
	 * Add Atom namespace to rdf-feed.
	 *
	 * @return void
	 */
	public static function add_rss_ns_link() {
		echo ' xmlns:atom="http://www.w3.org/2005/Atom" ' . PHP_EOL;
	}

	/**
	 * Adds Link headers as defined in the W3C WebSub Recommendation.
	 *
	 * @see https://www.w3.org/TR/websub/#discovery
	 *
	 * @return void
	 */
	public static function template_redirect() {
		// Check if current url is one of the feed urls.
		if ( ! show_discovery() ) {
			return;
		}

		$hub_urls = get_hubs();

		// Add all "hub" headers.
		foreach ( $hub_urls as $hub_url ) {
			\header( \sprintf( 'Link: <%s>; rel="hub"', \esc_url( $hub_url ) ), false );
		}

		// Add the "self" header.
		\header( \sprintf( 'Link: <%s>; rel="self"', get_self_link() ), false );
	}
}
