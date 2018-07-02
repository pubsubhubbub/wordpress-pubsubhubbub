<?php
/**
 * The WebSub/PubSubHubbub topics class
 */
class Pubsubhubbub_Topics {
	/**
	 * Add hub-<link> to the Atom feed
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
	 * Add hub-<link> to the RSS/RDF feed
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
	 * Add Atom namespace to rdf-feed
	 */
	public static function add_rss_ns_link() {
		echo ' xmlns:atom="http://www.w3.org/2005/Atom" ' . PHP_EOL;
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
}
