=== Plugin Name ===
Contributors: joshfraz, pfefferle
Tags: pubsubhubbub
Requires at least: 2.5
Tested up to: 3.6.1
Stable tag: 1.6.5

A better way to tell the world when your blog is updated.

== Description ==

This [PubSubHubbub](http://code.google.com/p/pubsubhubbub/ "PubSubHubbub") plugin is a simple way to let people know in real-time when your blog is updated.  PubSubHubbub is widely adopted and is used by Google Reader, Google Alerts and many other services.

This plugin:

* Sends realtime notifications when you update your blog
* Supports multi-user installations (Wordpress MU)
* Supports multiple hubs
* Supports all of the feed formats used by WordPress, not just ATOM and RSS2
* Supports latest spec ([Version 0.4](https://pubsubhubbub.googlecode.com/git/pubsubhubbub-core-0.4.html))
* Announces which hubs you are using by adding `<link rel="hub" ...>` declarations to your template header and ATOM feed
* Adds `<atom:link rel="hub" ...>` to your RSS feeds along with the necessary XMLNS declaration for RSS 0.92/1.0

By default this plugin will ping the following hubs:

* [Demo hub on Google App Engine](http://pubsubhubbub.appspot.com "Demo hub on Google App Engine")
* [SuperFeedr](http://pubsubhubbub.superfeedr.com "SuperFeedr")

Please contact me if you operate a hub that you would like to be included as a default option.

== Installation ==

1. Upload the `pubsubhubbub` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select custom hubs under your PubSubHubbub Settings (optional)

== Frequently Asked Questions ==

= Where can I learn more about the PubSubHubbub protocol? =

You can visit [PubSubHubbb on Google Code](http://code.google.com/p/pubsubhubbub/ "PubSubHubbb on Google Code")

= Where can I learn more about the authors of this plugin? =

You can learn more about [Josh Fraser](http://www.joshfraser.com "Josh Fraser") at [Online Aspect](http://www.onlineaspect.com "Online Aspect")
and [Matthias Pfefferle](http://pfefferle.org "Matthias Pfefferle") at [Notizblog](http://notizblog.org/ "Notizblog")

== Screenshots ==

1. The PubSubHubbub Settings page allows you to define which hubs you want to use

== Changelog ==

= 1.6.5 =
* hotfix

= 1.6.4 =
* removed pubsubhubbub client
* improvements for a better PuSH v0.4 support
* fixed small bugs

= 1.6.3 =
* Update hub URL for SuperFeedr (now pubsubhubbub.superfeedr.com)
* Update credits and documentation

= 1.6.1 =
* Bug fixes

= 1.6 =
* Added comment-feed support 
* Added simple subscriber functions
* Added link header

= 1.5 =
* Added filter to modify $feed_urls
* Re-Added Stephen Paul Webers changes

= 1.4 =
* Added name spacing to avoid conflicts with other plugins & added patch from pfefferle

= 1.3 =
* Added multi-user support and now tested up to 2.9.1

= 1.2 =
* Added support for multiple hubs

= 1.1 =
* Added RSS support

= 1.0 =
* First attempt

== Upgrade Notice ==

= 1.4 =
Upgrade eliminates conflicts with other Wordpress plugins