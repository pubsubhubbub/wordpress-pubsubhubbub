# WebSub Subscriber API

The WebSub plugin provides a subscriber API that allows other plugins (like feed readers) to subscribe to WebSub-enabled feeds using WordPress hooks.

## Overview

The implementation is stateless - consumer plugins are responsible for storing subscription data and providing secrets for verification. The plugin provides:

* REST API callback endpoint for subscription verification and content delivery
* Hub discovery from topic URLs (HTTP Link headers and feed content)
* HMAC signature verification (SHA1, SHA256, SHA384, SHA512)
* Full lifecycle hooks for integration

## Subscribing to a Feed

```php
do_action(
    'websub_subscribe',
    'https://example.com/feed.xml',  // Topic URL
    'my-plugin-subscription-123',     // Unique subscription ID
    array(
        'hub_url'       => '',        // Optional: specific hub URL (auto-discovered if empty)
        'secret'        => 'my-secret', // Optional: HMAC secret for signature verification
        'lease_seconds' => 86400,     // Optional: requested lease duration
    )
);
```

## Unsubscribing from a Feed

```php
do_action(
    'websub_unsubscribe',
    'https://example.com/feed.xml',  // Topic URL
    'my-plugin-subscription-123',     // Subscription ID
    'https://hub.example.com'         // Hub URL
);
```

## Verifying Subscriptions

When a hub sends a verification request, you must confirm the subscription via the `websub_verify_subscription` filter:

```php
add_filter( 'websub_verify_subscription', function( $allow, $subscription_id, $topic, $mode ) {
    // Only verify subscriptions from your plugin
    if ( str_starts_with( $subscription_id, 'my-plugin-' ) ) {
        // Verify this matches a subscription you requested
        $stored_topic = get_option( 'my_plugin_sub_' . $subscription_id );
        if ( $stored_topic === $topic ) {
            return true;
        }
    }
    return $allow;
}, 10, 4 );
```

## Providing Secrets for Signature Verification

If you provided a secret when subscribing, you must return it for signature verification:

```php
add_filter( 'websub_subscription_secret', function( $secret, $subscription_id ) {
    if ( str_starts_with( $subscription_id, 'my-plugin-' ) ) {
        return get_option( 'my_plugin_secret_' . $subscription_id );
    }
    return $secret;
}, 10, 2 );
```

## Receiving Content Updates

When the hub delivers new content, handle it via the `websub_received` action:

```php
add_action( 'websub_received', function( $subscription_id, $topic, $content, $content_type ) {
    if ( str_starts_with( $subscription_id, 'my-plugin-' ) ) {
        // Process the feed content
        $feed = simplexml_load_string( $content );
        // Update local cache, notify users, etc.
    }
}, 10, 4 );
```

## Complete Example

Here's a complete example of a plugin using the Subscriber API:

```php
<?php
/**
 * Plugin Name: My Feed Reader
 */

class My_Feed_Reader {

    public function __construct() {
        add_filter( 'websub_verify_subscription', array( $this, 'verify' ), 10, 4 );
        add_filter( 'websub_subscription_secret', array( $this, 'get_secret' ), 10, 2 );
        add_action( 'websub_received', array( $this, 'handle_content' ), 10, 4 );
        add_action( 'websub_verified', array( $this, 'subscription_verified' ), 10, 4 );
    }

    /**
     * Subscribe to a feed.
     */
    public function subscribe( $feed_url ) {
        $subscription_id = 'my-reader-' . md5( $feed_url );
        $secret = wp_generate_password( 32, false );

        // Store subscription data
        update_option( 'my_reader_topic_' . $subscription_id, $feed_url );
        update_option( 'my_reader_secret_' . $subscription_id, $secret );

        // Trigger subscription
        do_action( 'websub_subscribe', $feed_url, $subscription_id, array(
            'secret' => $secret,
        ) );
    }

    /**
     * Verify subscription requests.
     */
    public function verify( $allow, $subscription_id, $topic, $mode ) {
        if ( ! str_starts_with( $subscription_id, 'my-reader-' ) ) {
            return $allow;
        }

        $stored_topic = get_option( 'my_reader_topic_' . $subscription_id );
        return $stored_topic === $topic;
    }

    /**
     * Provide secret for signature verification.
     */
    public function get_secret( $secret, $subscription_id ) {
        if ( str_starts_with( $subscription_id, 'my-reader-' ) ) {
            return get_option( 'my_reader_secret_' . $subscription_id, '' );
        }
        return $secret;
    }

    /**
     * Handle subscription verification success.
     */
    public function subscription_verified( $subscription_id, $topic, $lease_seconds, $mode ) {
        if ( ! str_starts_with( $subscription_id, 'my-reader-' ) ) {
            return;
        }

        if ( 'subscribe' === $mode ) {
            update_option( 'my_reader_active_' . $subscription_id, true );
            update_option( 'my_reader_expires_' . $subscription_id, time() + $lease_seconds );
        } else {
            delete_option( 'my_reader_active_' . $subscription_id );
        }
    }

    /**
     * Handle received content.
     */
    public function handle_content( $subscription_id, $topic, $content, $content_type ) {
        if ( ! str_starts_with( $subscription_id, 'my-reader-' ) ) {
            return;
        }

        // Parse and process the feed
        $feed = simplexml_load_string( $content );
        if ( $feed ) {
            // Process feed items...
        }
    }
}

new My_Feed_Reader();
```

## Available Hooks

For a complete list of available actions and filters, see the [Hooks Wiki](https://github.com/pubsubhubbub/wordpress-pubsubhubbub/wiki).
