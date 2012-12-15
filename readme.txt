=== BuddyPress Album ===
Contributors: foxly, fanquake, anancy, francescolaffi
Tags: BuddyPress, BP-Album, BuddyPress Media, album, albums, picture, pictures, photos, gallery, galleries, media, privacy, social networking, activity, profiles, messaging, friends, groups, forums, microblogging, social, community, networks, networking,
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 0.1.8.14

Photo Albums for BuddyPress. Includes Posts to Activity Stream, Member Comments, and Gallery Privacy Controls.

== Description ==
This plugin adds full photo album capabilities to BuddyPress, mimicking Facebook's photo app.

= IMPORTANT =
We only use wordpress.org to distribute the current STABLE version of the plugin. We do not support, answer questions, or accept bug reports through wordpress.org.

= Latest Version =
For the latest beta version, which is often 6 MONTHS ahead of the version posted on wordpress.org, please visit our [Google Code](http://code.google.com/p/buddypress-media/) project page.

= Development Schedule =
For a real-time view of our development schedule, please see our [Pivotal Tracker](http://bit.ly/f5zPJ9).

= User Support =
We handle all user support through our buddypress.org [Support Forum](http://buddypress.org/community/groups/bp-album/forum/). Support requests posted on wordpress.org will not be answered.

= Bug Reports =
We accept bug reports through our [Google Code Bug Tracker](http://code.google.com/p/buddypress-media/issues/list). Bug reports posted on wordpress.org will be ignored.

= About This Release =

* Fixes "Incorrect items displayed in user album view" defect
* Fixes "Warning: Missing argument 2 for wpdb::prepare()" in WP 3.5
* Fixes "Fatal error: Call to undefined function wp_create_thumbnail()" in WP 3.5 
* Updated the plugins internal version checker
* Updated strings in the admin menu and user screens for better clarity
* Added action hooks to allow integration with other plugins

= Resources =
For the [Latest News](https://github.com/BP-Media/bp-media/commits/master), [Developer Resources](http://code.google.com/p/buddypress-media/w/list),
[Project Schedule](http://bit.ly/f5zPJ9), [Development Roadmap](http://code.google.com/p/buddypress-media/wiki/BuddyPressMediaFeaturesList),
and [GIT Code Repository](https://github.com/BP-Media/), please visit our [Official Plugin Website](http://code.google.com/p/buddypress-media/)

= Translations =

* it_IT (Italian)   by francescolaffi
* en_US (English)   by foxly
* es_ES (Spanish)   by Jose M. Villar & Selu Vega
* ja_JA (Japanese)  by chestnut_jp
* pl_PL (Polish)    by Jacek Wu
* ru_RU (Russian) by [ig0r74](weblr.ru)
* zh_CN (Chinese)   by Calvin Hsu
* de_DE (German) by Olaf Baumann
* fr_FR (French) by [Chouf1](bp-fr.net)
* hu_HU (Hungarian) by Baka Attila Tamás

* Please submit new or updated translations through our [Support Forum](http://buddypress.org/community/groups/bp-album/forum/)


== Installation ==

1. Upload `bp-album` to the `/wp-content/plugins/` directory or use the automatic installation in the WordPress plugin panel.
2. Activate the plugin through the WordPress 'Plugins' menu


== Changelog ==

= 0.1.8.14 =
* Fixes even more problems caused by changes in the WordPress core

= 0.1.8.13 =
* Fixes corrupted file caused by SVN failure in WP plugin repo

= 0.1.8.12 =
* Compatibility with BuddyPress 1.6
* Fixes "Warning: Missing argument 2 for wpdb::prepare()" in WP 3.5
* Fixes "Fatal error: Call to undefined function wp_create_thumbnail()" in WP 3.5 
* Updated the plugins internal version checker
* Updated strings in the admin menu and user screens for better clarity
* Added action hooks to allow integration with other plugins

= 0.1.8.11 =
* Compatibility with BuddyPress 1.5
* Fixed the bug that was restricting file upload size
* Added de_DE (German) and fr_FR (French) translations
* Fixed a bug when users could not be deleted when the activity stream was turned off
* Removed a filter that was adding <p></p> tags to descriptions
* Fixed global media page causing user media items to render incorrectly when added to BuddyPress template header

= 0.1.8.10 =
* Ability to upload files with extensions other than .jpg, .png, and .gif
* Missing translation text domains on file upload error messages

= 0.1.8.9 =
* "Please upload only JPG, GIF or PNG photos" error when user uploads a file with an upper-case extension (.JPG vs .jpg) fixed. This was defect was caused by changes made in BuddyPress 1.2.8 [Tracker Entry](https://www.pivotaltracker.com/story/show/11097197)
* ABSPATH incorrectly added to file path on multi-site WordPress installs fixed. [Tracker Entry](https://www.pivotaltracker.com/story/show/10567999)
* Wrong images displayed on servers with persistent caching fixed. [Tracker Entry](https://www.pivotaltracker.com/story/show/10494941)
* Improved support for Asian languages [Tracker Entry](https://www.pivotaltracker.com/story/show/10567805)

= 0.1.8.8 =
* "Please upload only JPG, GIF or PNG photos" error fixed. This was defect was caused by changes made in BuddyPress 1.2.8
* "No BP-Album menu visible inside the BuddyPress admin interface" error fixed. This defect was caused by changes made in WordPress 3.1

= 0.1.8.7 =
* "All BP-Album activity stream posts dropped when admin deletes a user" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=60)
* "Broken activity stream images on virtual servers when using URL Re-mapping" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=56)
* "Plugin does not enforce image upload limits"
* "Error on single image page when activity stream disabled" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=48)
* "Admin cannot delete other user's images" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=11)
* "Incorrect redirect when admin posts edit on another user's image" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=11)
* "Incorrect translation text domain for some text strings"
* "HTML special character fragments in thumbnail view titles"
* "Escape characters in image titles" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=30)
* "Incorrect character case in image titles" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=29)
* "Escape characters in image descriptions" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=30)
* "Unicode support for image titles" [Tracker Entry](http://code.google.com/p/buddypress-media/issues/detail?id=58)
* "Post all uploaded images to site activity stream, even if not submitted by users"
* "Display all uploaded images in any template file"

= 0.1.8.0 =
* Added URL re-mapping functionality to fix "Incorrect Image File URL Problem"

= 0.1.7 =
* Fixed the loader so it doesn't give an error when updating/deactivating BuddyPress or when it is not installed.
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
* Now tested with success on FF, Webkit, IE7, and IE8. IE6 displays well but without cool effects.
* fr_FR updated; tr_TR,de_DE added

= 0.1.2 =
* Corrected readme text encoding
* Added fr_FR translation

= 0.1.1 =
* General bugfixes
* Added nl_NL translation

= 0.1 =
* Early release of the plugin
