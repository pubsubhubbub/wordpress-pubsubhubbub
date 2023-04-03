=== WebSub (FKA. PubSubHubbub) ===
Contributors: pfefferle, joshfraz
Donate link: https://notiz.blog/donate/
Author: PubSubHubbub Team
Author URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub
Tags: webhook, websub, pubsub, ping, indieweb, ostatus
Requires at least: 4.5
Tested up to: 6.2
Stable tag: 3.1.3

A better way to tell the world when your blog is updated.

== Description ==

This plugin is a simple way to let people know in real-time when your blog is updated.  PubSubHubbub/WebSub is widely adopted and is used by Google Alerts and many other services.  Subscription requests are relayed through hubs, which validate and verify the request. Hubs then distribute new and updated content to subscribers when it becomes available.

This plugin:

* Sends realtime notifications when you update your blog
* Supports multi-user installations (Wordpress MU)
* Supports multiple hubs
* Supports all of the feed formats used by WordPress, not just ATOM and RSS2
* Supports latest specs
	* PubSubHubbub [Version 0.4](https://pubsubhubbub.github.io/PubSubHubbub/pubsubhubbub-core-0.4.html)
	* WebSub [W3C Version](https://www.w3.org/TR/websub/)
* Announces which hubs you are using by adding `<link rel="hub" ...>` declarations to your template header and Atom feed

By default this plugin will ping the following hubs:

* [Demo hub on Google App Engine](https://pubsubhubbub.appspot.com "Demo hub on Google App Engine")
* [SuperFeedr](https://pubsubhubbub.superfeedr.com "SuperFeedr")
* [WebSubHub](https://websubhub.com "WebSubHub")

Please contact me if you operate a hub that you would like to be included as a default option.

== Installation ==

1. Upload the `pubsubhubub` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select custom hubs under your WebSub/PubSubHubbub Settings (optional)

== Frequently Asked Questions ==

= What is WebSub? =

WebSub provides a common mechanism for communication between publishers of any kind of Web content and their subscribers, based on HTTP web hooks. Subscription requests are relayed through hubs, which validate and verify the request. Hubs then distribute new and updated content to subscribers when it becomes available. WebSub was previously known as PubSubHubbub.

= Where can I learn more about the WebSub protocol? =

* [W3C Spec](https://www.w3.org/TR/websub/ "W3C Spec")
* [WebSub on Github](https://github.com/w3c/websub "WebSub on Github")
* [WebSub.rocks - a WebSub validator](https://websub.rocks/ "WebSub.rocks")

= Where can I learn more about the PubSubHubbub protocol? =

* [PubSubHubbub on Github](https://github.com/pubsubhubbub "PubSubHubbub on Github")
* [Latest Spec (0.4)](http://pubsubhubbub.github.io/PubSubHubbub/pubsubhubbub-core-0.4.html)

= What is a Hub? =

A WebSub Hub is an implementation that handles subscription requests and distributes the content to subscribers when the corresponding topic URL has been updated. Hubs MUST support subscription requests with a secret and deliver authenticated requests when requested. Hubs MUST deliver the full contents of the topic URL in the request, and MAY reduce the payload to a diff if the content type supports it.

= What is a Publisher? =

A WebSub Publisher is an implementation that advertises a topic and hub URL on one or more resource URLs.

= What is a Subscriber? =

A WebSub Subscriber is an implementation that discovers the hub and topic URL given a resource URL, subscribes to updates at the hub, and accepts content distribution requests from the hub. The subscriber MAY support authenticated content distribution.

== Screenshots ==

1. The WebSub Settings page allows you to define which hubs you want to use

== Changelog ==

Project maintained on github at [pubsubhubbub/wordpress-pubsubhubbub](https://github.com/pubsubhubbub/wordpress-pubsubhubbub).

= 3.1.3 =

* update configuration files, fix typos, update dependencies

= 3.1.2 =

* add new WebSub Hub

= 3.1.1 =

* fix deprecation issue (PHP8)
* add `_deprecated_function` warnings

= 3.1.0 =

* update wording (more consequent use of WebSub)
* add category-, tag- and author-feeds support

= 3.0.3 =

* update dependencies

= 3.0.2 =

* better `self-link` generator

= 3.0.1 =

* add donation button

= 3.0.0 =

* optimized the DB load
* added help section
* better code encapsulation

= 2.2.2 =

* temporarily remove comment-feed support to be more GDPR compliant

= 2.2.1 =

* display topic URLs on the settings page

= 2.2.0 =

* fixed a discovery bug
* changed http client

= 2.1.0 =

* save pinged URLs to add correct headers

= 2.0.0 =

* Complete rewrite
* Support WebSub

= 1.0.0 =

* First attempt

== Upgrade Notice ==
