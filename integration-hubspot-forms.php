<?php

/*
  Plugin Name: Integration with Hubspot Forms
  Description: Embed Hubspot forms into your content.
  Version: 1.2.2
  Author: Minnur Yunusov
  License: GPLv2 or later
  Author URI: http://www.minnur.com/
  Text Domain: integration-hubspot-forms
*/

define( 'EmbedHubspotForms_PATH', dirname( __FILE__ ) );
define( 'EmbedHubspotForms_PluginName', plugin_basename( __FILE__ ) );

if ( !class_exists( 'EmbedHubspotForms' ) ) {

  // List classes in their load order.
  $classes = array(
    'EmbedHubspotForms',
    'EmbedHubspotFormsWidget',
  );

  foreach ( $classes as $class ) {
    require_once ( sprintf( "%s/includes/%s.php", EmbedHubspotForms_PATH, $class ) );
  }

  // Instantiate the plugin class.
  $EmbedHubspotForms = new EmbedHubspotForms\EmbedHubspotForms();

  // Install and uninstall hooks.
  register_activation_hook( EmbedHubspotForms_PluginName, array( $EmbedHubspotForms, 'activate' ) );
  register_deactivation_hook( EmbedHubspotForms_PluginName, array( $EmbedHubspotForms, 'deactivate' ) );

}
