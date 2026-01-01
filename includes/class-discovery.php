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
	 * Add hub-<link> to the Atom feed.
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
			echo '<link rel="hub" href="' . \esc_url( $hub_url ) . '" />' . PHP_EOL;
		}
	}

	/**
	 * Add hub-<link> to the RSS/RDF feed.
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
			echo '<atom:link rel="hub" href="' . \esc_url( $hub_url ) . '"/>' . PHP_EOL;
		}
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
	 * Adds link headers as defined in the current v0.4 draft.
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
