=== Add to Any Share/Save/Bookmark Button ===
Contributors: micropat
Donate link: http://www.addtoany.com/contact/
Tags: bookmarking, social, social bookmarking, bookmark, bookmarks, sharing, share, saving, save, Post, posts, page, pages, images, image, admin, statistics, stats, links, plugin, widget, e-mail, email, seo, button, delicious, google, digg, reddit, facebook, myspace, addtoany, add, any
Requires at least: 2.0
Tested up to: 2.7
Stable tag: 0.9.8.6.1

Helps readers share, save, and bookmark your posts and pages using any service, such as Delicious, Digg, Facebook, MySpace, and all the rest.

== Description ==

Helps readers **share**, **save**, and **bookmark** your posts and pages using **any service**, such as Delicious, Digg, Facebook, MySpace, and all the rest.  The button comes with Add to Any's customizable **Smart Menu**, which **places the services visitors use at the top of the menu**, based on each visitor's browsing history.

Within the menu, users are additionally given an e-mail option, and an option to bookmark in-browser (Internet Explorer, Firefox, Opera, Safari, etc.), with directions on how to bookmark if their browser does not support auto-bookmarking from links/shortcuts.

* Add to Any Smart Menu
* Includes all services
* Services updated automatically
* Searchable on key-down
* Keyboard navigation
* Google Analytics integration

Demo: <a href="http://www.addtoany.com/" title="Share, Save, Subscribe buttons">http://www.addtoany.com/</a>

See also, the <a href="/extend/plugins/add-to-any-subscribe/" title="WordPress RSS Subscribe plugin">Subscribe button</a> plugin.

== Installation ==

1. Upload `add-to-any` to the `/wp-content/plugins/` directory
1. Activate the plugin through the `Plugins` menu in WordPress

== Frequently Asked Questions ==

= How often is the list of services updated? =

Constantly... and it's done automatically, without having to upgrade the plugin!

= Where can I choose which button to display and other options? =

Go to `Settings` > `Share/Save Buttons`. 

= How can I force the button to appear in individual posts and pages? =

If your button isn't already set up to appear (it is by default), type the following tag into the page or post that you want the button to appear in: `<!--sharesave-->`

= How can I remove a button from individual posts and pages? =

Type the following tag into the page or post that you do not want the button to appear in: `<!--nosharesave-->`

= Why isn't the drop-down menu appearing? =

It's likely because your your theme wasn't <a href="http://codex.wordpress.org/Theme_Development#Plugin_API_Hooks" target="_blank">coded properly</a>.  Using the Theme Editor, make sure that the following piece of code is included in your theme's `footer.php` file just before the `</body>` line:

`<?php wp_footer(); ?>`

= Why do embedded objects (like Flash) disappear when the menu is displayed? =

This is done to overcome browser limitations that prevent the drop-down menu from displaying on top of intersecting embedded objects.  If you would like to disable this, uncheck the `Hide embedded objects (Flash, video, etc.) that intersect with the menu when displayed` option on the plugin's settings page.

== Screenshots ==

1. Add to Any Share/Save button, featuring the Open <a href="http://www.shareicon.com/">Share Icon</a>
2. Drop-down menu that appears instantly when visitors move the mouse over the Share/Save button
3. E-mail tab, with direct links to the most popular web-based e-mailers' auto-filled Compose page, a web-based sender for use with any e-mail address

== Changelog ==

.9.8.6.1:

* Fixed buttons if WordPress files are in a subdirectory while the blog appears in the site root
 * For example: http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory

.9.8.6:

* Fixed output buffering - button should appear below posts again if option is set

.9.8.5:

* Button targets the current page if called outside The Loop
* Accomodates renamed plugin directory

.9.8.4:

* Fixed a small syntax error (critcal if you're on .9.8.3)

.9.8.3:

* Language & localization update
 * "After clicking OK," removed from the Bookmark tab

.9.8.2:

* Event attributes removed (JS now takes care of button events)
 * This eliminates the chance of errors prior to JS fully loading

.9.8.1:

* Fixed repo problem

.9.8:

* JavaScript removed from blog feed
* Option to display button (without menu) or to not display it at all below posts in blog feed
* Replaced some UTF-8 encoding functions with core WordPress functions
* For XHTML validation, special characters are converted to HTML entities within JavaScript variables
* Reprioritized plugin to load later
* Text-only button option

.9.7:

* Internationalization
* Buttons updated

.9.6:

* Moved external JavaScript to bottom so that content is prioritized over HTTP requests to static.addtoany.com
 * Please note that some improperly-coded themes may prevent this from working. See the FAQ entry for "Why isn't the drop-down menu appearing?" if this is the case.

.9.5.2:

* Fixed bug in Internet Explorer 6 that caused custom buttons to have a height and width of 0
* Removed the XHTML depreciated `name` attribute from the button's anchor

.9.5.1:

* Fixed 1 line to support those without short_open_tag

.9.5:

* New: Custom buttons (specify a URL)
* Fix to permit XHTML Strict validation

.9.4:

* New Menu Styler lets you customize the color of the menus
* New Menu Option: "Only show the menu when the user clicks the Share/Save button"
* New: Set custom JavaScript variables for further customization
* Better support for CSS styling: .addtoany_share_save
* PHP support for short_open_tag
* PHP4 legacy and compatibility fixes