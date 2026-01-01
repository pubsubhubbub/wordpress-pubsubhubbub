# WebSub (FKA. PubSubHubbub)

- Contributors: pfefferle, joshfraz
- Donate link: https://notiz.blog/donate/
- Author: PubSubHubbub Team
- Author URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub
- Tags: websub, pubsubhubbub, pubsub, indieweb, feed
- License: MIT
- License URI: http://opensource.org/licenses/MIT
- Requires at least: 4.5
- Tested up to: 6.9
- Stable tag: 3.2.1
- Requires PHP: 7.2

A WebSub plugin for WordPress that enables real-time publishing and subscription capabilities.

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

### How can I use the Subscriber API in my plugin?

The Subscriber API allows other plugins to subscribe to WebSub-enabled feeds using WordPress hooks. See the [Subscriber API documentation](https://github.com/pubsubhubbub/wordpress-pubsubhubbub/blob/main/docs/subscriber-api.md) for detailed examples and usage.

### Where can I find a list of available hooks?

For a complete list of available actions and filters, see the [Hooks Wiki](https://github.com/pubsubhubbub/wordpress-pubsubhubbub/wiki).

## Screenshots

1. The WebSub Settings page allows you to define which hubs you want to use

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

### 4.0.0

Major update with namespace refactoring and new Subscriber API. Review your code if you were using internal functions directly.
