<?php
class MailChimpShortcode {
  var $namespace = "mailchimp_shortcode";
  var $slug = "mcs";
  var $version = '1.0.2';
  var $plugin_dirname;

  /**
   * Initialize the plugin
   *
   * @uses add_action()
   * @uses add_shortcode()
   */
  public function __construct() {
    $this->plugin_dirname = dirname( dirname( __FILE__ ) );

    // Bind shortcode form submission
    add_action( 'wp_ajax_' . $this->namespace, array( &$this, 'ajax_form_submission' ) );
    add_action( 'wp_ajax_nopriv_' . $this->namespace, array( &$this, 'ajax_form_submission' ) );

    // MailChimp form shortcode
    add_shortcode( 'mailchimp_signup', array( &$this, 'shortcode' ) );
  }

  /**
   * Clean unnecessary keys from MailChimp groupings arrays
   *
   * @param (array) $value The grouping array to clean
   *
   * @return (array)
   */
  private function _clean_groups( $value ) {
    unset( $value['identifier'] );
    return $value;
  }

  /**
   * Form submission processor for `wp_ajax_mailchimp_shortcode` and `wp_ajax_nopriv_mailchimp_shortcode` action
   *
   * Validates email subscription form submission and subscribes the user to the
   * specified list and groupings using the Mailchimp API. Redirects back to the
   * URL submitted from and adds `error_code` and `error_message` query parameters
   * to the redirect URL if an error occurred.
   *
   * @uses Mailchimp
   * @uses WP_Error
   * @uses Exception->getMessage()
   * @uses Mailchimp->subscribe()
   * @uses Mailchimp_Exception->error_code()
   * @uses Mailchimp_Exception->get_error_message()
   * @uses add_query_arg()
   * @uses wp_die()
   * @uses wp_redirect()
   * @uses wp_verify_nonce()
   */
  public function ajax_form_submission() {
    if( !wp_verify_nonce( $_REQUEST['_wpnonce'], "{$this->namespace}_form_submission" ) ){
      wp_die( "Unauthorized form submission.", "Unauthorized", array( 'response' => 401 ) );
    }

    require $this->plugin_dirname . '/vendor/Mailchimp.php';

    $this->MCAPI = new Mailchimp( MAILCHIMP_SHORTCODE_API_KEY );

    // Extract the MailChimp fields from the form submission
    $mc = (array) $_REQUEST[$this->slug];
    // Redirect location after form submission
    $redirect_url = isset( $_REQUEST['redirect_to'] ) && !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $_REQUEST['_wp_http_referer'];
    // Response validity
    $response = true;

    if( !empty( $mc ) ) {
      // Email validation
      if( !$mc['email'] || !preg_match( '/^[\w+\-.]+@[a-z\d\-.]+\.[a-z]+$/i', $mc['email'] ) ) {
        $response = new WP_Error( 'invalid_email', "Invalid or missing email address" );
      }
      // List ID validation
      if( !$mc['list_id'] ) {
        $response = new WP_Error( 'invalid_list_id', "Invalid or missing MailChimp list ID" );
      }
    } else {
      $response = new WP_Error( 'invalid_submission', "Invalid form submission, no MailChimp parameters supplied" );
    }

    // Process the form if the minimum required values are valid
    if( !is_wp_error( $response ) ) {
      $merge_vars = array(
        'optin_ip' => $_SERVER['REMOTE_ADDR'],
        'optin_time' => date_i18n( 'c' )
      );

      // Interest groupings
      if( $mc['groupings'] ) $merge_vars['groupings'] = array_map( $this->_clean_groups, $mc['groupings'] );

      try {
        $this->MCAPI->lists->subscribe(
          // MailChimp list ID
          "{$mc['list_id']}",
          // Email to subscribe
          array( 'email' => "{$mc['email']}" ),
          // Merge vars to send with the subscription
          $merge_vars,
          // Email format
          'html',
          // Double opt-in preference
          !!$mc['double_optin'],
          // Update existing records
          true,
          // Replace interest groups
          true,
          // Send welcome email preference
          !!$mc['send_welcome']
        );

        $redirect_url = add_query_arg( array(
          "_{$this->slug}_status" => "success",
          "_{$this->slug}_message" => urlencode( MAILCHIMP_SHORTCODE_SUCCESS )
        ), $redirect_url );
      } catch( Exception $e ) {
        $response = new WP_Error( 'mailchimp_error', $e->getMessage() );
      }
    } else {
      $redirect_url = add_query_arg( array(
        "_{$this->slug}_status" => "error",
        "_{$this->slug}_error_code" => $response->error_code(),
        "_{$this->slug}_message" => $response->get_error_message()
      ), $redirect_url );
    }

    if( $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest" ) {
      print_r( $response );
    } else {
      wp_redirect( $redirect_url );
    }

    exit;
  }

  /**
   * MailChimp Form shortcode
   *
   * Renders the MailChimp form shortcode markup. Takes the following arguments:
   *
   * @param (string)  title                 Title text for the form
   * @param (string)  subtitle              Sub-title text for the form
   * @param (string)  footer_text           Footer text for the form
   * @param (integer) list_id               MailChimp list ID
   * @param (boolean) double_optin          Double opt-in preference
   * @param (boolean) send_welcome          Send welcome email preference
   * @param (string)  email_placeholder     Email field placeholder text
   * @param (string)  submit_label          Submit button label
   * @param (string)  groupings_#_(id|name) The name or ID of the grouping. (e.g. groupings_0_id="Foobar")
   * @param (string)  groupings_#_groups    A comma delimited list of groups in the grouping (e.g. groupings_0_groups="Group 1, Group 2")
   * @param (string)  redirect_to           Optional redirect location after form submission
   *
   * Example shortcode with all parameters (line-breaks and indentation added for presentation):
   *
   * [mailchimp_signup title="My Title"
   *                   subtitle="My Subtitle"
   *                   footer_text="Footer text appears"
   *                   list_id="0a2102bd"
   *                   double_optin="1"
   *                   send_welcome="1"
   *                   email_placeholder="email@domain.com"
   *                   submit_label="Submit"
   *                   groupings_0_name="Group Name"
   *                   groupings_0_groups="Group 1, Group 2"
   *                   groupings_1_id="124121"
   *                   groupings_1_groups="Group 1, Group 2"
   *                   redirect_to="http://mydomain.com/destination-url"]
   *
   * @uses admin_url()
   * @uses shortcode_atts()
   * @uses wp_nonce_field()
   *
   * @return (string)
   */
  public function shortcode( $atts ) {
    extract( shortcode_atts( array(
      'title' => MAILCHIMP_SHORTCODE_TITLE,
      'subtitle' => MAILCHIMP_SHORTCODE_SUBTITLE,
      'footer_text' => MAILCHIMP_SHORTCODE_FOOTER_TEXT,
      'list_id' => MAILCHIMP_SHORTCODE_LIST_ID,
      'double_optin' => MAILCHIMP_SHORTCODE_DOUBLE_OPTIN,
      'send_welcome' => MAILCHIMP_SHORTCODE_SEND_WELCOME,
      'email_placeholder' => MAILCHIMP_SHORTCODE_PLACEHOLDER,
      'submit_label' => MAILCHIMP_SHORTCODE_SUBMIT_LABEL,
      'groupings' => array(),
      'redirect_to' => false
    ), $atts ) );

    foreach( (array) $atts as $key => $val ) {
      /**
       * Match values:
       *
       * 0 - whole match
       * 1 - index of the shortcode parameter set (0, 1, 2, etc.)
       * 2 - identifier of the property of the parameter set (id, name, groups)
       */
      if( preg_match( '/groupings_(\d+)_(id|name|groups)/', $key, $groupings_match ) && !empty( $groupings_match ) ) {
        // Identifier logged for proper input field naming in the output HTML
        if( in_array( $groupings_match[2], array( 'id', 'name' ) ) ) $groupings[$groupings_match[1]]['identifier'] = $groupings_match[2];

        if( $groupings_match[2] == 'groups' ) {
          $groupings[$groupings_match[1]][$groupings_match[2]] = array_map( 'trim', explode( ",", $val ) );
        } else {
          $groupings[$groupings_match[1]][$groupings_match[2]] = $val;
        }
      }
    }

    $namespace = $this->namespace;
    $slug = $this->slug;
    $nonce_action = "{$namespace}_form_submission";
    $nonce_field = wp_nonce_field( $nonce_action, '_wpnonce', true, false );
    $action = admin_url( "admin-ajax.php?action={$namespace}" );

    if( $_REQUEST["_{$slug}_message"] ) $message = $_REQUEST["_{$slug}_message"];
    if( $_REQUEST["_{$slug}_status"] ) $status = $_REQUEST["_{$slug}_status"];
    if( $_REQUEST["_{$slug}_error_code"] ) $error_code = $_REQUEST["_{$slug}_error_code"];

    ob_start();
      include $this->plugin_dirname . '/views/form.php';
      $html = ob_get_contents();
    ob_end_clean();

    return $html;
  }
}
