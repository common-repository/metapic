=== Metapic ===
Contributors: marcusraketnu
Tags: tagging, images, collage
Requires at least: 5.0.0
Tested up to: 6.2
Requires PHP: 5.4 or later
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use Metapic to tag images, create collages and link products from our service.

== Description ==

Use Metapic to tag images, link content and create image collages through the WordPress editor.
The links created with our editor are connected to our product feed allowing you to make money from the traffic generated from these links.

Major features in Metapic include:

* Automatically checks all post content for links that can be converted to Metapic links (needs to be activated on the options page).
* Our custom editor fully integrated in TinyMCE for tagging images and linking content.
* A collage editor for making composite images with your favorite products.
* A stat summary on your dashboard.
* A more detailed custom statistics view for your account showing you all the traffic generated for your links.

PS: You'll need a [Metapic account](http://metapic.se/) to use it. You can register an account in the plugin.
Metapic is a free service and all you need to do is register in order to use the service.
Metapic reserves the right to contact you for more personal details in order to facilitate payouts for the traffic you generate.
If you have a multi site installation please contact us and we will help you set up a multi site account.

== Installation ==

Upload the Metapic plugin to your blog, Activate it, then enter your Metapic account credentials in Settings -> Metapic.
If you don't already have an account you can register through a link on the settings page.
Once you have linked your account to your blog the new buttons should show up in TinyMCE and you're ready to start tagging and linking!

== Changelog ==

= 1.1 =
* Initial public release.

= 1.1.1 =
* Fix MTPC_DEBUG constant bug and add 4.0 compability.

= 1.1.2 =
* Fix dashboard js link.

= 1.1.3 =
* Restructure user select list.

= 1.1.4 =
* Many global fixes and adjustments.

= 1.1.5 =
* Remove unnecessary AJAX calls to simplify multisite compability.

= 1.1.6 =
* Allow network administrators to force SSL/https on multisites.

= 1.1.7 =
* Fix blog switching bug when synchronizing statistics for a multisite.

= 1.2.0 =
* Updating plugin dependencies and bump minimum PHP version to 5.5

= 1.2.1 - 1.2.4 =
* Minor bugfixes

= 1.2.5 =
* Lazyload by default

= 1.3.0 =
* Remove all third party dependencies

= 1.3.1 =
* Update auto register functionality

= 1.3.2 =
* Fix frontend scripts

= 1.3.3 =
* Fix dashboard widget bug

= 1.3.4 =
* Fix changing user randomly bug

= 1.3.5 =
* Add that the user can chose country on registration.

= 1.3.6 =
* Add translation for Poland and Italy.

= 1.3.7 =
* Update of dependencies.

= 1.3.8 =
* Fix undefined notice error.

= 1.3.9 =
* Send home url as username.

= 1.4.0 =
* Fix deeplinking when switching clients.

= 1.4.1 =
* Remove instructional code.

= 1.4.2 =
* Fix notices
* Force SSL

= 1.4.3 =
* Rename i18n folder
* Add Spain as an option when registering

= 1.5.0 =
* Major change in how deeplinking works in the editor. If deeplinking is activated we will now attempt to deeplink whenever a link is inserted instead of waiting for a post.

= 1.6.0 =
* Metapic blocks for Gutenberg!

= 1.6.1 =
* Remove PHP 7 typehints

= 1.6.2 =
* Fix single user experience

= 1.6.3 =
* Minor bug fixes

= 1.6.4 =
* Fix link matching in Gutenberg

= 1.6.5 =
* Even more URL normalizations
* Fix deeplink publishing for non Gutenberg sites

= 1.6.6 =
* Move up TinyMCE buttons again

= 1.7.0 =
* Major code refactor in effort to simplify and split up the plugin code
* Fix bugs related to switching from a WordPress options page to WordPress admin menu pages
* Evaluate and adjust single user and multi user experience in the WordPress admin
* We recommend running the latest version of WordPress (5.1.1) for an optimal Gutenberg experience

= 1.7.1 =
* Fix the client code for France

= 1.7.2 =
* Fix missing method for non Gutenberg blogs

= 1.7.3 =
* Check date array to avoid notices

= 1.7.4 =
* Fix link tagging start page

= 1.7.5 =
* Fix permission errors when bloggers try to create their own accounts on multi site

= 1.7.6 =
* Fix auto linking in TinyMCE when using the classic editor plugin
* Add French translation

= 1.8.0 =
* Allow automatic registration of all user roles, not just admins
* Update individual blogs when changing the automatic deeplinking setting
* Add network setting for showing commercial message when adding Metapic links

= 1.8.1 =
* Add Norwegian language
* Fix commercial message checkbox

= 1.8.2 =
* Fix desing on new commercial message checkbox
* Fix deeplinking on wordpress 5.2.
* Add new Statspage
* Add new Settingspage.
* Advanced settings buttons have the ability to update accesstoken.

= 1.9.0 =
* Add that you can hide collage and tag image on network level.
* Add a new widget for deeplinking.
* hide how to earn money button

= 1.9.1 =
* Fix mixed content warning caused by http link in widget template file

= 1.9.2 =
* Fix incorrect link to stores in our deeplink widget

= 1.9.3 =
* Remove admin css from the frontend

= 1.9.4 =
* Remove non working blocks
