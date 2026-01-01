# WebSub (FKA. PubSubHubbub)

- Contributors: pfefferle, joshfraz, indieweb
- Donate link: https://notiz.blog/donate/
- Author: PubSubHubbub Team
- Author URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub
- Tags: websub, pubsub, indieweb, ostatus, rss
- License: MIT
- License URI: http://opensource.org/licenses/MIT
- Requires at least: 4.5
- Tested up to: 6.9
- Stable tag: 3.2.1

A WebSub (PubSubHubbub) plugin for WordPress that provides real-time publishing and subscription capabilities.

## Description

This plugin implements the [WebSub](https://www.w3.org/TR/websub/) protocol (formerly known as PubSubHubbub) for WordPress. It enables real-time notifications when your blog is updated and provides a subscriber API for other plugins to consume WebSub-enabled feeds.

### Publisher Features

When you publish or update a post, this plugin automatically notifies WebSub hubs, which then distribute the update to all subscribers in real-time.

* Sends realtime notifications when you update your blog
* Supports multi-user installations (WordPress MU)
* Supports multiple hubs
* Supports all feed formats used by WordPress (Atom, RSS2, RDF)
* Adds `<link rel="hub">` and `<link rel="self">` declarations to feeds and HTML

### Subscriber Features

The plugin provides a subscriber API that allows other plugins (like feed readers) to subscribe to WebSub-enabled feeds using WordPress hooks.

* REST API callback endpoint for subscription verification and content delivery
* Hub discovery from topic URLs (HTTP Link headers and feed content)
* HMAC signature verification (SHA1, SHA256, SHA384, SHA512)
* Full lifecycle hooks for integration with other plugins

### Supported Specifications

* [WebSub W3C Recommendation](https://www.w3.org/TR/websub/)
* [PubSubHubbub 0.4](https://pubsubhubbub.github.io/PubSubHubbub/pubsubhubbub-core-0.4.html)

### Default Hubs

By default this plugin will ping the following hubs:

* [Demo hub on Google App Engine](https://pubsubhubbub.appspot.com)
* [SuperFeedr](https://pubsubhubbub.superfeedr.com)
* [WebSubHub](https://websubhub.com)

Please contact us if you operate a hub that you would like to be included as a default option.

## Installation

1. Upload the `pubsubhubbub` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select custom hubs under your WebSub/PubSubHubbub Settings (optional)

## Subscriber API

The subscriber API allows other plugins to subscribe to WebSub-enabled feeds. The implementation is stateless - consumer plugins are responsible for storing subscription data and providing secrets for verification.

### Subscribing to a Feed

```php
// Subscribe to a feed using do_action
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

### Unsubscribing from a Feed

```php
do_action(
    'websub_unsubscribe',
    'https://example.com/feed.xml',  // Topic URL
    'my-plugin-subscription-123',     // Subscription ID
    'https://hub.example.com'         // Hub URL
);
```

### Verifying Subscriptions

When a hub sends a verification request, you must confirm the subscription:

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

### Providing Secrets for Signature Verification

```php
add_filter( 'websub_subscription_secret', function( $secret, $subscription_id ) {
    if ( str_starts_with( $subscription_id, 'my-plugin-' ) ) {
        return get_option( 'my_plugin_secret_' . $subscription_id );
    }
    return $secret;
}, 10, 2 );
```

### Receiving Content Updates

```php
add_action( 'websub_received', function( $subscription_id, $topic, $content, $content_type ) {
    if ( str_starts_with( $subscription_id, 'my-plugin-' ) ) {
        // Process the feed content
        $feed = simplexml_load_string( $content );
        // Update local cache, notify users, etc.
    }
}, 10, 4 );
```

### Available Hooks

#### Actions

| Hook | Parameters | Description |
|------|------------|-------------|
| `websub_subscribe` | `$topic_url, $subscription_id, $args` | Trigger a subscription request |
| `websub_unsubscribe` | `$topic_url, $subscription_id, $hub_url` | Trigger an unsubscribe request |
| `websub_pre_subscribe` | `$topic_url, $subscription_id, $hub_url` | Fires before sending subscribe request |
| `websub_pre_unsubscribe` | `$topic_url, $subscription_id, $hub_url` | Fires before sending unsubscribe request |
| `websub_subscribe_success` | `$topic_url, $subscription_id, $response` | Subscribe request accepted by hub |
| `websub_subscribe_error` | `$topic_url, $subscription_id, $error` | Subscribe request failed |
| `websub_unsubscribe_success` | `$topic_url, $subscription_id, $response` | Unsubscribe request accepted |
| `websub_unsubscribe_error` | `$topic_url, $subscription_id, $error` | Unsubscribe request failed |
| `websub_verified` | `$subscription_id, $topic, $lease_seconds, $mode` | Subscription verified by hub |
| `websub_denied` | `$subscription_id, $topic, $reason` | Subscription denied by hub |
| `websub_received` | `$subscription_id, $topic, $content, $content_type` | Content received from hub |
| `websub_signature_valid` | `$subscription_id, $topic` | Signature verification passed |
| `websub_signature_invalid` | `$subscription_id, $topic, $signature` | Signature verification failed |

#### Filters

| Filter | Parameters | Description |
|--------|------------|-------------|
| `websub_verify_subscription` | `$allow, $subscription_id, $topic, $mode` | Allow/deny subscription verification |
| `websub_subscription_secret` | `$secret, $subscription_id` | Get secret for signature verification |
| `websub_subscribe_args` | `$args, $topic_url, $subscription_id` | Modify subscribe request arguments |
| `websub_lease_seconds` | `$seconds, $subscription_id` | Request specific lease duration |

## Frequently Asked Questions

### What is WebSub?

WebSub provides a common mechanism for communication between publishers of any kind of Web content and their subscribers, based on HTTP web hooks. Subscription requests are relayed through hubs, which validate and verify the request. Hubs then distribute new and updated content to subscribers when it becomes available. WebSub was previously known as PubSubHubbub.

### Where can I learn more about WebSub?

* [W3C WebSub Recommendation](https://www.w3.org/TR/websub/)
* [WebSub on GitHub](https://github.com/w3c/websub)
* [WebSub.rocks - WebSub validator](https://websub.rocks/)

### What is a Hub?

A WebSub Hub is an implementation that handles subscription requests and distributes content to subscribers when the corresponding topic URL has been updated.

### What is a Publisher?

A WebSub Publisher is an implementation that advertises a topic and hub URL on one or more resource URLs. This plugin acts as a publisher for your WordPress site.

### What is a Subscriber?

A WebSub Subscriber is an implementation that discovers the hub and topic URL given a resource URL, subscribes to updates at the hub, and accepts content distribution requests from the hub. This plugin provides a subscriber API for other plugins to use.

## Screenshots

### 1. The WebSub Settings page allows you to define which hubs you want to use
![The WebSub Settings page allows you to define which hubs you want to use](https://ps.w.org/pubsubhubbub/trunk/screenshot-1.png)


## Changelog

Project maintained on GitHub at [pubsubhubbub/wordpress-pubsubhubbub](https://github.com/pubsubhubbub/wordpress-pubsubhubbub).

### 4.0.0

* Complete code refactoring with namespaces
* Added WebSub Subscriber API with hooks for consumer plugins
* Added REST API callback endpoint for subscriptions
* Added hub discovery and HMAC signature verification
* Improved WebSub spec compliance

### 3.2.1

* fix: empty option on fresh install leads to broken feed links

### 3.2.0

* improved validating, sanitizing, and escaping
* simplified code

### 3.1.4

* add Content-Type header to WebSub requests

### 3.1.3

* update configuration files, fix typos, update dependencies

### 3.1.2

* add new WebSub Hub

### 3.1.1

* fix deprecation issue (PHP8)
* add `_deprecated_function` warnings

### 3.1.0

* update wording (more consequent use of WebSub)
* add category-, tag- and author-feeds support

### 3.0.3

* update dependencies

### 3.0.2

* better `self-link` generator

### 3.0.1

* add donation button

### 3.0.0

* optimized the DB load
* added help section
* better code encapsulation

### 2.2.2

* temporarily remove comment-feed support to be more GDPR compliant

### 2.2.1

* display topic URLs on the settings page

### 2.2.0

* fixed a discovery bug
* changed http client

### 2.1.0

* save pinged URLs to add correct headers

### 2.0.0

* Complete rewrite
* Support WebSub

### 1.0.0

* First attempt

## Upgrade Notice
