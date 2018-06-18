=== WebSub/PubSubHubbub ===
Contributors: pfefferle, joshfraz
Donate link: https://notiz.blog/donate/
Tags: webhooks, websub, puhsubhubbub, pubsub, ping, push, indieweb, openweb, ostatus
Requires at least: 4.5
Tested up to: 4.9.7
Stable tag: 3.0.0

A better way to tell the world when your blog is updated.

== Description ==

This plugin is a simple way to let people know in real-time when your blog is updated.  PubSubHubbub/WebSub is widely adopted and is used by Google Reader, Google Alerts and many other services.  Subscription requests are relayed through hubs, which validate and verify the request. Hubs then distribute new and updated content to subscribers when it becomes available.

This plugin:

* Sends realtime notifications when you update your blog
* Supports multi-user installations (Wordpress MU)
* Supports multiple hubs
* Supports all of the feed formats used by WordPress, not just ATOM and RSS2
* Supports latest specs
	* PubSubHubbub [Version 0.4](https://pubsubhubbub.github.io/PubSubHubbub/pubsubhubbub-core-0.4.html)
	* WebSub [Dev Version](https://www.w3.org/TR/websub/)
* Announces which hubs you are using by adding `<link rel="hub" ...>` declarations to your template header and ATOM feed
* Adds `<atom:link rel="hub" ...>` to your RSS feeds along with the necessary XMLNS declaration for RSS 0.92/1.0

By default this plugin will ping the following hubs:

* [Demo hub on Google App Engine](https://pubsubhubbub.appspot.com "Demo hub on Google App Engine")
* [SuperFeedr](https://pubsubhubbub.superfeedr.com "SuperFeedr")

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

= Where can I learn more about the PubsSubHubbub protocol? =

* [PubsSubHubbub on Github](https://github.com/pubsubhubbub "PubsSubHubbub on Github")
* [Latest Spec (0.4)](http://pubsubhubbub.github.io/PubSubHubbub/pubsubhubbub-core-0.4.html)

== Screenshots ==

1. The WebSub Settings page allows you to define which hubs you want to use

== Changelog ==

Project maintained on github at [pubsubhubbub/wordpress-pubsubhubbub](https://github.com/pubsubhubbub/wordpress-pubsubhubbub).

= 3.0.0 =

* refactored version to lower the DB load

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
