=== BuddyPress Album+ ===
Contributors: francescolaffi, foxly
Donate link: http://code.google.com/p/buddypress-media/
Tags: BuddyPress, BP, album, albums, picture, pictures, photos, gallery, galleries, media, privacy
Requires at least: WP 2.9, BP 1.2
Tested up to: WP 3.0.1, BP 1.2.5.2
Stable tag: 0.1.8

Photo Albums for BuddyPress. Includes Posts to Wire, Member Comments, and Gallery Privacy Controls.

== Description ==

This plugin adds full photo album capabilities to BuddyPress, mimicking Facebook's photo app! The current BETA version also supports audio files, embedded videos from YouTube, Revver, Vimeo, and
dozens of other sites, as well as embedded images from Flickr, Imgur, and many others.

=IMPORTANT=
We only use wordpress.org to distribute the current STABLE version of the plugin. We do not support, answer questions, or accept bug reports through wordpress.org.

=Latest Version=
For the latest beta version, which is often 6 MONTHS+ ahead of the version posted on wordpress.org, please visit our [Google Code](http://code.google.com/p/buddypress-media/) project page.

=User Support=
We handle all user support through our buddypress.org [Support Forum](http://buddypress.org/community/groups/bp-album/forum/). Support requests posted on wordpress.org will not be answered.

=Bug Reports=
We accept bug reports through our [Google Code Bug Tracker](http://code.google.com/p/buddypress-media/issues/list). Bug reports posted on wordpress.org will be ignored.


= About This Release =

This version of the plugin fixes the 'Incorrect Image File URL' problem that some users with badly configured shared / virtual servers were experiencing. A typical example would be where the URL to an image file is "www.example.com/wp-content/album/01/image.jpg" but BP-Album returns the URL
"www.example.com/virtual/~customer_name/site_name/wp-content/album/01/image.jpg". 

The plugin now includes an admin option that lets users to set the base URL that BP-Album uses for images, so the correct URL can be sent to a web browser no matter how screwed-up your server is.


= Disclaimer =

This is an early release version of the plugin that should NOT be used in a production environment. It's provided on an “as is” basis without any warranty. We are not liable for any damage or losses.

= Resources =

For the [Latest News](http://code.google.com/p/buddypress-media/updates/list), [Developer Resources](http://code.google.com/p/buddypress-media/w/list), [Project Schedule](http://bit.ly/aUn5dZ),
[Development Roadmap](http://code.google.com/p/buddypress-media/wiki/BuddyPressMediaFeaturesList), and [SVN Code Repository](http://code.google.com/p/buddypress-media/source/list), please visit our [Official Plugin Website](http://code.google.com/p/buddypress-media/)


= Translations =

* it_IT by francescolaffi
* en_US by foxly
* nl_NL by Xevo
* fr_FR by Chouf1, DanielH
* tr_TR by Şerafettin Yarar, Hüseyin Cahid Doğan
* de_DE by Micheal Berra
* es_ES by Jorge Ocampo
* ru_RU by Chimit
* fi_FI by Jyri Väätäinen
* he_IL by Raffi Vitis

* Please submit new/updated translations via our [Issue Tracker](http://code.google.com/p/buddypress-media/issues/list)


== Installation ==

1. Upload `bp-album` to the `/wp-content/plugins/` directory or use automatic installation from wp plugin panel
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

We handle all user support through our [BuddyPress Support Forum](http://buddypress.org/community/groups/bp-album/forum/). Support requests posted on wordpress.org will not be answered.


== Screenshots ==

For screen shots, our development roadmap, and a live project calendar, please visit our [Official Plugin Website](http://code.google.com/p/buddypress-media/)


== Changelog ==

= 0.1.8 =
* Added URL re-mapping functionality to fix 'Incorrect Image File URL Problem' that some users with badly configured shared / virtual servers were experiencing. A typical example would be where the URL to an image file is "www.example.com/wp-content/album/01/image.jpg" but BP-Album returns the URL
"www.example.com/virtual/~customer_name/site_name/wp-content/album/01/image.jpg".

= 0.1.7 =
* Fix the loader so it don't give error when updating/deactivating bp or when it is not installed.
* es_ES updated; fi_FI,he_IL added.

= 0.1.6 =
* Fix added for 'There were problems saving picture details' problem.

= 0.1.5 =
* Some files were not updated in last release, now all should work fine

= 0.1.4 =
* Admin menu added
* Added global comment disable option
* Added global wire post disable option
* Added es_ES and ru_RU

= 0.1.3 =
* Buddybar menu link fixed
* Broken image link fixed
* Now tested with success on FF,Webkit,IE7,IE8, IE6 display well but without cool effects
* fr_FR updated; tr_TR,de_DE added

= 0.1.2 =
* Corrected readme text encoding
* Added fr_FR translation

= 0.1.1 =
* General bugfixes
* Added nl_NL translation

= 0.1 =
* Early release of the plugin

== Upgrade Notice ==

= 0.1.5 =
* this will resolve several errors

= 0.1.4 =
admin menu added,  es_ES, ru_RU

= 0.1.3=
bug fixes,css fixes, tr_TR,de_DE translations

