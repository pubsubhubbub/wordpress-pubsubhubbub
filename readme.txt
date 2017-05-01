=== WebSub/PubSubHubbub ===
Contributors: pfefferle, joshfraz
Donate link: http://14101978.de
Tags: webhooks, websub, puhsubhubbub, pubsub, ping
Requires at least: 4.5
Tested up to: 4.7.4
Stable tag: 2.0.0

A better way to tell the world when your blog is updated.

== Description ==

This [WebSub](https://github.com/w3c/websub "WebSub") plugin is a simple way to let people know in real-time when your blog is updated.  WebSub is widely adopted and is used by Google Reader, Google Alerts and many other services.

This plugin:

* Sends realtime notifications when you update your blog
* Supports multi-user installations (Wordpress MU)
* Supports multiple hubs
* Supports all of the feed formats used by WordPress, not just ATOM and RSS2
* Supports latest spec ([Version 0.4](https://websub.github.io/WebSub/websub-core-0.4.html))
* Announces which hubs you are using by adding `<link rel="hub" ...>` declarations to your template header and ATOM feed
* Adds `<atom:link rel="hub" ...>` to your RSS feeds along with the necessary XMLNS declaration for RSS 0.92/1.0

By default this plugin will ping the following hubs:

* [Demo hub on Google App Engine](https://websub.appspot.com "Demo hub on Google App Engine")
* [SuperFeedr](https://websub.superfeedr.com "SuperFeedr")

Please contact me if you operate a hub that you would like to be included as a default option.

== Installation ==

1. Upload the `websub` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select custom hubs under your WebSub Settings (optional)

== Frequently Asked Questions ==

= Where can I learn more about the WebSub protocol? =

You can visit [WebSub on Github](https://github.com/w3c/websub "WebSub on Github")

= Where can I learn more about the PubsSubHubbub protocol? =

You can visit [PubsSubHubbub on Github](https://github.com/pubsubhubbub "PubsSubHubbub on Github")

== Screenshots ==

1. The WebSub Settings page allows you to define which hubs you want to use

== Changelog ==

Project maintined on github at [websub/wordpress-websub](https://github.com/pfefferle/wordpress-websub).

= 1.0.0 =
* First attempt

== Upgrade Notice ==
