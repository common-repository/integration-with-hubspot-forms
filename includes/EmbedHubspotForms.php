<?php
/**
 * @package EmbedHubspotForms
 */

namespace EmbedHubspotForms;

class EmbedHubspotForms {

  /**
   * Implements __construct(). 
   * Construct the plugin object.
   */
  public function __construct( $add_menu = true ) {
    if ( $add_menu ) {
      add_action( 'init', array( &$this, 'init' ) );
      add_action( 'admin_head' , array( &$this, 'admin_head' ) );
      add_action( 'template_redirect', array( &$this, 'page_rewrite_redirect' ) );
      add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
      add_action( 'admin_init', array( &$this, 'admin_init' ) );
      add_action( 'media_buttons', array( &$this, 'media_buttons' ) );
      add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
      add_shortcode( 'hubspotform', array( &$this, 'hubspotform_output' ) );
      // Add link to plugin settings page.
      add_filter( 'plugin_action_links_' . EmbedHubspotForms_PluginName, array( &$this, 'action_links' ));
      // Add TinyMCE plugin
      add_filter( 'mce_external_plugins', array( $this, 'embed_hubspot_mce_plugin' ) );
    }
    add_action( 'wp_enqueue_scripts', array( &$this, 'loadAssets' ) );
  }

  /**
   * Implements 'init'.
   */
  public function init() {
    // Plugin redirect callback paths.
    add_rewrite_tag( '%hubspot_embed_plugin%', '([^&]+)' );
    add_rewrite_rule( '^hubspotforms\-preview/?(.*)?', 'index.php?hubspot_embed_plugin=$matches[1]', 'top' );
  }

  /**
   * TinyMCE plugin.
   */
  public function embed_hubspot_mce_plugin( $plugins ) {
    $plugins['embedHubspot'] = plugins_url( '/js/mce-plugin.js', EmbedHubspotForms_PluginName );
    return $plugins;
  }

  /**
   * Implements 'template_redirect'.
   * Load Hubspot form iframe preview.
   */
  public function page_rewrite_redirect() {
    global $wp;
    $template = $wp->query_vars;
    if ( array_key_exists( 'hubspot_embed_plugin', $template ) && !empty( $template['hubspot_embed_plugin'] ) ) {
      $data = $template['hubspot_embed_plugin'];
      $params = str_replace( '\"', '"', urldecode( $data ) );
      $output = '<!DOCTYPE html><html><head><title>' . __('Preview') . '</title><!--[if lte IE 8]><script type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script><![endif]--><script type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script></head><body>';
      $output .= do_shortcode( '[hubspotform ' . $params . ']' );
      $output .= '</body></html>';
      echo $output;
      exit();
    }
  }

  /**
   * Implements 'admin_init'.
   */
  public function admin_init() {
    // Initialize variables.
    register_setting( 'embed_hubspot_forms', 'embed_hubspot_api_key' );
    register_setting( 'embed_hubspot_forms', 'embed_hubspot_salesforce_support' );
    // Add admin assets.
    wp_register_style( 'embed_hubspot_forms_admin_css', plugins_url( '/css/admin.css', EmbedHubspotForms_PluginName ) );
    wp_register_script( 'embed_hubspot_forms_admin_js', plugins_url( '/js/integration-hubspot-forms.js', EmbedHubspotForms_PluginName ), array( 'jquery' ) );
    wp_localize_script( 'embed_hubspot_forms_admin_js', 'objectL10n', array(
      'no_form_selected' => __( 'Please choose a Hubspot form to insert', 'text-domain' ),
    ) );
    wp_enqueue_style( 'embed_hubspot_forms_admin_css' );
    wp_enqueue_script( 'embed_hubspot_forms_admin_js' );
  }

  /**
   * HubSpot library JS files.
   */
  public function loadAssets($load = false) {
    global $post;
    // HubSpot JS libraries.
    if ( !empty($post->post_content) && has_shortcode( $post->post_content, 'hubspotform') ) {
      $load = true;
    }
    if ($load) {
      wp_register_script( 'hubspot_legacy_v2_js', '//js.hsforms.net/forms/v2-legacy.js' );
      wp_register_script( 'hubspot_v2_js', '//js.hsforms.net/forms/v2.js' );
      wp_script_add_data( 'hubspot_legacy_v2_js', 'conditional', 'lte IE 8' );
      wp_enqueue_script( 'hubspot_legacy_v2_js' );
      wp_enqueue_script( 'hubspot_v2_js' );
    }
  }

  /**
   * Implements 'admin_head'.
   */
  public function admin_head() {
    echo '<script type="text/javascript">
      var _hspt_preview_base_url = \''  . get_site_url() . '\';
    </script>';
  }

  /**
   * Implements 'admin_menu'.
   */     
  public function admin_menu() {
    // Settings page.
    $settings_page = add_options_page(
      'Hubpost Forms Settings',
      'Hubspot Forms',
      'manage_options',
      'EmbedHubspotForms',
      array( &$this, '_settings_page' )
    );
  }

  /**
   * Implements 'plugin_action_links_[plugin-name]'.
   * Settings page link.
   */
  public function action_links( $links ) {
    $links[] = '<a href="options-general.php?page=EmbedHubspotForms">' . __('Settings', 'integration-hubspot-forms'). '</a>';
    return $links;
  }

  /**
   * Build hubspot embed form parameters.
   */
  public function hubspotform_parameters( $atts = array() ) {
    $params = array();
    // Add additional Hubspot form parameters.
    if ( !empty( $atts['sfdccampaignid'] ) && get_option( 'embed_hubspot_salesforce_support' ) ) {
      $params['sfdcCampaignId'] = $atts['sfdccampaignid'];
    }
    if ( isset( $atts['css'] ) && $atts['css'] != 'hide' ) {
      $params['css'] = $atts['css'];
    }
    // Add Tracking whenever it is possible by checking cookies.
    // This is a Hubspot supported variable.
    if ( isset( $_COOKIE['hubspotutk'] ) && !empty( $_COOKIE['hubspotutk'] ) ) {
      $params['hutk'] = $_COOKIE['hubspotutk'];
    }
    return $params;
  }

  /**
   * Hubspot form shortcode.
   */
  public function hubspotform_output( $atts ) {
    $atts = shortcode_atts( array(
      'portal_id' => '',
      'form_id' => '',
      'sfdccampaignid' => '',
      'css' => '',
    ), $atts );
    $portal_id = $atts['portal_id'];
    $form_id = $atts['form_id'];
    $params = $this->hubspotform_parameters( $atts );
    return $this->hubspot_embed_code( $portal_id, $form_id, $params );
  }

  /**
   * Embed code.
   */
  public function hubspot_embed_code( $portal_id, $form_id, $params = array() ) {
    $params_string = '';
    if ( !empty( $params ) ) {
      $params_array = array();
      foreach ( $params as $key => $value ) {
        $params_array[] = $key . ": '" . $value . "'";
      }
      $params_string = join( ', ', $params_array );
      $params_string .= ', ';
    }
    return '<script>hbspt.forms.create({' . $params_string . 'portalId: \'' . $portal_id . '\', formId: \'' . $form_id . '\'});</script>';
  }

  /**
   * Implements 'media_buttons'.
   * Add "Add Hubspot form" button next to Add Media button.
   */
  public function media_buttons() {
    echo '<a href="#TB_inline?width=600&height=350&inlineId=hubspot-form-popup" title="'
      . __('Embed Hubspot Form', 'integration-hubspot-forms')
      . '" class="button thickbox">'
      . __('Embed Hubspot Form', 'integration-hubspot-forms')
      . '</a>';
  }

  /**
   * Implements 'admin_notices'.
   * Add popup html to head.
   */
  public function admin_notices() {
    // Render the settings template
    $screen = get_current_screen();
    if ( $screen->parent_base == 'edit' ) {
      $forms = $this->get_hubspot_forms();
      require_once ( sprintf( "%s/templates/popup.php", EmbedHubspotForms_PATH ) );
    }
  }

  /**
   * Settings page.
   */
  public function _settings_page() {
    if ( !current_user_can( 'manage_options' ) ) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    else {
      // Render the settings template
      require_once ( sprintf( "%s/templates/settings.php", EmbedHubspotForms_PATH ) );
    }
  }

  /**
   * Get Hubspot forms.
   */
  public function get_hubspot_forms() {
    $forms = new \stdClass();
    if ( !$forms = wp_cache_get( 'embed_hubspot_forms' ) ) {
      $url = 'https://api.hubapi.com/forms/v2/forms?hapikey=' . get_option( 'embed_hubspot_api_key' );
      $response = wp_remote_get( $url );
      if (is_array($response)) {
        $forms = json_decode( $response['body'] );
        wp_cache_set( 'embed_hubspot_forms', $forms, 'embed_hubspot_forms', (int) get_option( 'embed_hubspot_cache' ) );
      }
    }
    return $forms;
  }

  /**
   * Activate the plugin.
   */
  public static function activate() {
    global $wp_rewrite;
    // Refresh path rules.
    $wp_rewrite->flush_rules();
  }

  /**
   * Deactivate the plugin.
   */     
  public static function deactivate() {
    delete_option( 'embed_hubspot_api_key' );
    delete_option( 'embed_hubspot_cache' );
    delete_option( 'embed_hubspot_salesforce_support' );
  }

}
