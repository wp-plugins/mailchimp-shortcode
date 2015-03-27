<?php
/*
Plugin Name: MailChimp Shortcode
Plugin URI: http://filament.io/
Description: Shortcode generator for MailChimp form
Version: 1.0.2
Author: dtelepathy
Author URI: http://www.dtelepathy.com/
Contributors: kynatro, dtelepathy, dtlabs
License: GPL3

Copyright 2015 digital-telepathy  (email: support@filament.io)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * MailChimp shortcode plugin initializer
 *
 * This function is called on all pages and sets constants for base
 * default option setting in use of the shortcode. Define any of these
 * constants in your theme's functions.php file to customize the default
 * values used in your website. All of these options can be overridden
 * on a per-shortcode use basis with the exception of the API key. See
 * the `shortcode()` method on the `MailChimpShortcode` PHP Class
 * for more details on the format of the override parameters.
 *
 * @uses is_admin()
 * @uses MailChimpShortcode
 */
function mailchimp_shortcode_init(){
  // Your MailChimp API Key
  if( !defined( 'MAILCHIMP_SHORTCODE_API_KEY' ) ) define( 'MAILCHIMP_SHORTCODE_API_KEY', "" );
  // Default MailChimp list ID
  if( !defined( 'MAILCHIMP_SHORTCODE_LIST_ID' ) ) define( 'MAILCHIMP_SHORTCODE_LIST_ID', "" );
  // Default Double opt-in preference
  if( !defined( 'MAILCHIMP_SHORTCODE_DOUBLE_OPTIN' ) ) define( 'MAILCHIMP_SHORTCODE_DOUBLE_OPTIN', true );
  // Default send welcome email preference
  if( !defined( 'MAILCHIMP_SHORTCODE_SEND_WELCOME' ) ) define( 'MAILCHIMP_SHORTCODE_SEND_WELCOME', true );
  // Default form title
  if( !defined( 'MAILCHIMP_SHORTCODE_TITLE' ) ) define( 'MAILCHIMP_SHORTCODE_TITLE', "Sign up to Engage Harder" );
  // Default form sub-title
  if( !defined( 'MAILCHIMP_SHORTCODE_SUBTITLE' ) ) define( 'MAILCHIMP_SHORTCODE_SUBTITLE', "We're writing all about using our own Engagement platform to grow the engagement of this very blog. Don't miss it!" );
  // Default email placeholder
  if( !defined( 'MAILCHIMP_SHORTCODE_PLACEHOLDER' ) ) define( 'MAILCHIMP_SHORTCODE_PLACEHOLDER', "Put it in right here" );
  // Default submit label
  if( !defined( 'MAILCHIMP_SHORTCODE_SUBMIT_LABEL' ) ) define( 'MAILCHIMP_SHORTCODE_SUBMIT_LABEL', "Submit" );
  // Form footer text
  if( !defined( 'MAILCHIMP_SHORTCODE_FOOTER_TEXT' ) ) define( 'MAILCHIMP_SHORTCODE_FOOTER_TEXT', "You'll get early product release access too!" );
  // Success message
  if( !defined( 'MAILCHIMP_SHORTCODE_SUCCESS' ) ) define( 'MAILCHIMP_SHORTCODE_SUCCESS', "Thanks for signing up! Please check your email." );

  if( !is_admin() || ( DOING_AJAX && basename( $_SERVER['PHP_SELF'] ) == "admin-ajax.php" ) ) {
    require dirname( __FILE__ ) . '/lib/mailchimp-shortcode.class.php';

    new MailChimpShortcode();
  }
}
add_action( 'init', 'mailchimp_shortcode_init' );
