=== MailChimp Shortcode ===
Contributors: kynatro, dtelepathy, dtlabs
Donate link: http://filament.io/
Tags: Filament, MailChimp
Requires at least: 3.0
Tested up to: 4.1
License: GPL3
Stable tag: trunk

This plugin provides a shortcode for output of a simple MailChimp subscription form anywhere in your post. The form submits to an end-point provided by this plugin and uses the MailChimp API to submit the subscription request.

== Description ==
This plugin provides a shortcode for output of a simple MailChimp subscription form anywhere in your post. The form submits to an end-point provided by this plugin and uses the MailChimp API to submit the subscription request.

== Using this plugin ==
This plugin simply provides a shortcode for output of a MailChimp subscription form anywhere that is processed by `do_shortcode()`.

= Configuration =
To keep this plugin simple, there is no UI. All configuration options can be configured either by defining PHP constants or specifying options on the shortcode itself. For more information on defining constants, check out the in-line documentation in the primary `mailchimp-shortcode.php` file. For more information on shortcode parameters, checkout the in-line documentation in the `lib/mailchimp-shortcode.class.php` file on the `shortcode()` method.

== Changelog ==
= 1.0.2 =
* Add redirect_to option to shortcode
* Add support for AJAX response to shortcode submission

= 1.0.1 =
* Trim `groups` values in shortcode to ensure proper group names when sending to MailChimp

= 1.0.0 =
Initial release
