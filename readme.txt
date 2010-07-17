=== BuddyPress Album+ ===
Contributors: francescolaffi, foxly
Donate link: http://flweb.it/
Tags: Buddypress, BP, album, albums, picture, pictures, photos, privacy
Requires at least: WP 2.9, BP 1.2
Tested up to: WP 2.9.2, BP 1.2.3
Stable tag: 0.1.7

Adds User Photo Albums to BuddyPress! Includes Photo Posts to Wire, Member Comments on Photos, and Privacy Controls. Works with the current BuddyPress theme and includes Easy To Skin Templates.

== Description ==

This plugin adds full photo album capabilities to BuddyPress, mimicking Facebook's photo app!

A user can upload pictures to their album, add a title, description, and choose privacy options (public, visible to members, visible to friends, and private). Users can also enable and disable comments on a per-image basis.

Using the plugin's administration interface in the WordPress backend, administrators can set how many pictures users are allowed to upload for each visibility level.

Administrators can globally enable and disable comments on photos, and can control whether or not thumbnails of uploaded pictures are posted on a user's wire. Administrators can also set whether an image's description can be empty, number of images per page in the default query.

For each uploaded picture, the plugin will generate a thumbnail image and a gallery size image. The admin can set thumbnail size, gallery image size and if original file should be kept.

Site administrators can edit or delete any picture. They can also hide members' pictures without deleting them.

At the user level, image comments are connected to the image's activity. Enabling comments for an image also enables wire post activity for that image. Disabling comments deletes previously posted wire activity for that image.

Templating this plugin is very easy, because the template class works in a way similar to the wp posts template.

= Configuration =

This version of the plugin fixes the 'There were problems saving picture details' that some users experienced with previous versions. It also includes an admin panel, allowing all plugin admin options to be set from the WP back end.

To override the plugin's template files, simply create an `album/` folder in your theme's folder and put your custom template files in it, making sure to name them the same as the plugin's template files.

= Disclaimer =

This is an early release version and should NOT be used in a production environment. You can test this plugin in [my BP test site](http://bptest.flweb.it/)

It is provided on an “as is” basis without any warranty. We are not liable for any damage or losses.


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
* send translations at francesco (dot) laffi (at) gmail (dot) com

= Future release will include (in no specific order): =

* multiple albums per user
* albums for groups
* shortcode embeddable photos
* cache integration
* clean uninstall command

Other good ideas we are considering, subject to community feedback:

* sidebar photo widget
* audio and video capability
* flash-based content uploader
* member photo rating
* top photos page
* watermarks on photos
* EXIF photo data import
* Gallery stats (popularity, views, etc)


= Known issues: =

* If a public pic that have comments changed to private, then the pic activity will be hidden from sitewide stream, but the comments will not (i think this is a bp issue, but i need to look more into it)
* Picture thumbnails might not be immediately posted to a user's wire. The user might have to perform an edit action on a photo or other site activity to get the wire to update.
* Admin menu numeric values are not type-checked. If you enter text in a box that should have a number in it, unpredictible things could happen.

= Contact =

For issues/bugs/suggestions use the wordpress.org support forum with tag 'bp-album'. After [browsing existing topics](http://wordpress.org/tags/bp-album?forum_id=10), you can [open a new one](http://wordpress.org/tags/bp-album?forum_id=10#postform). No duplicates please.


== Installation ==

1. Upload `bp-album` to the `/wp-content/plugins/` directory or use automatic installation from wp plugin panel
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= no faq here? =

still no faq

== Screenshots ==

1. screenshot-1.(png|jpg|jpeg|gif)

== Changelog ==

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

= 0.1.2 =

i forgot to include fr_FR in last release

= 0.1.1 =

all known bug are fixed