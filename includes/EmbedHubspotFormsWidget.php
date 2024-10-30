<?php
/**
 * @package EmbedHubspotForms
 */

namespace EmbedHubspotForms;

class EmbedHubspotFormsWidget extends \WP_Widget {

  private $EmbedHubspotForms;
  protected static $js_loaded = false;

  /**
   * Implements __construct().
   * Register widget with WordPress.
   */
  public function __construct() {
    parent::__construct(
      'WP_IntegrationHubspotFormsWidget',
      __('Embed Hubspot Form', 'integration-hubspot-forms'),
      array( 'description' => __( 'Display Hubspot Form', 'integration-hubspot-forms' ), )
    );
    // Call the main plugin. We will need this to access some of the methods
    // in this widget class, but we don't want double initialize and load
    // files/hooks.
    $this->EmbedHubspotForms = new \EmbedHubspotForms\EmbedHubspotForms( false );
    add_action('wp_enqueue_scripts', array( $this, 'assets' ) );
  }

  /**
   * Implements widget().
   * Display widget.
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( !empty( $instance['hubspot_forms_embed'] ) ) {
      list ($portal_id, $form_id) = explode( '::', $instance['hubspot_forms_embed'] );
      $atts = array(
        'sfdccampaignid' => !empty($instance['sfdcCampaignId']) ? $instance['sfdcCampaignId'] : '',
        'css' => ( isset($instance[ 'css' ] ) && $instance[ 'css' ] == 'on' ) ? '' : 'hide',
      );
      $params = $this->EmbedHubspotForms->hubspotform_parameters( $atts );
      
      // Check if `Beaver Builder Plugin` plugin is enabled.
      if ( !$this::$js_loaded && class_exists( 'FLBuilderLoader' ) ) {
        echo '<!--[if lte IE 8]><script type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script><![endif]--><script type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>';
        $this::$js_loaded = true;
      }
      echo $this->EmbedHubspotForms->hubspot_embed_code( $portal_id, $form_id, $params );
    }
    echo $args['after_widget'];
  }

  /**
   * Implements form().
   * Widget configuration form.
   */
  public function form( $instance ) {
    $forms = $this->EmbedHubspotForms->get_hubspot_forms();
    $hubspot_current_form  = !empty( $instance[ 'hubspot_forms_embed' ] ) ? $instance[ 'hubspot_forms_embed' ] : '';
    $sfdcCampaignId  = !empty( $instance[ 'sfdcCampaignId' ] ) ? $instance[ 'sfdcCampaignId' ] : '';
    $css = !empty($instance[ 'css' ]) ? strip_tags( $instance[ 'css' ] ) : '';
    // Render the settings template
    include ( sprintf( "%s/templates/widget-form.php", EmbedHubspotForms_PATH ) );
  }

  /**
   * Implements update().
   * Save widget configuration options.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['hubspot_forms_embed'] = ( ! empty( $new_instance['hubspot_forms_embed'] ) ) ? strip_tags( $new_instance['hubspot_forms_embed'] ) : '';
    $instance['sfdcCampaignId'] = ( ! empty( $new_instance['sfdcCampaignId'] ) ) ? strip_tags( $new_instance['sfdcCampaignId'] ) : '';
    $instance['css'] = strip_tags( $new_instance[ 'css' ] );
    return $instance;
  }
  
  /**
   * Load HubSpot JS only when widget is present on the page.
   */
  public function assets() {
    if ( is_active_widget( false, false, $this->id_base ) && !class_exists( 'FLBuilderLoader' ) ) {
      $this->EmbedHubspotForms->loadAssets( true );
    }
  }

}

// Register Hubspot Forms widget.
add_action( 'widgets_init', function(){
  register_widget( 'EmbedHubspotForms\EmbedHubspotFormsWidget' );
});
