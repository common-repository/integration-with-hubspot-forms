<?php
/**
 * EmbedHubspotForms settings administration panel.
 *
 * @package EmbedHubspotForms
 */

if ( ! current_user_can( 'manage_options' ) )
  wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('Integration with Hubspot Forms', 'integration-hubspot-forms');

?>
<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<p><?php _e('Please note that you have to configure this plugin first before you can start embedding forms into your content.', 'integration-hubspot-forms'); ?></p>

<form action="options.php" method="post">
<?php @settings_fields( 'embed_hubspot_forms' ); ?>

<div id="wp-hubspot-forms-settings-tab-contents">
  <div class="content">
    <table class="form-table">
      <tr>
        <th scope="row"><label for="embed_hubspot_api_key"><?php _e('Hubspot Forms API Key', 'integration-hubspot-forms') ?></label></th>
      <td>
        <input name="embed_hubspot_api_key" type="text" id="embed_hubspot_api_key" value="<?php form_option( 'embed_hubspot_api_key' ); ?>" placeholder="demo" class="regular-text" />
        <p class="description"><?php _e('Please use <strong>demo</strong> to load example forms. Generate my own <a href="https://app.hubspot.com/keys/get" target="_blank">new key</a>.', 'integration-hubspot-forms'); ?></p>
      </td>
      </tr>
      <tr>
      <th scope="row"></th>
        <td>
        <input name="embed_hubspot_salesforce_support" type="checkbox" id="embed_hubspot_salesforce_support" value="1" <?php checked( '1', get_option( 'embed_hubspot_salesforce_support' ) ); ?>/>
        <label for="embed_hubspot_salesforce_support" class="enable"><?php _e('I have Hubspot Salesforce integration.', 'integration-hubspot-forms'); ?></label>
        <p class="description"><?php _e('If you have Hubspot Salesforce integration the plugin will add additional option for the Salesforce Campaing ID to each widget. <a href="http://www.hubspot.com/products/salesforce" target="_blank">Read more about Salesforce integration</a>.', 'integration-hubspot-forms'); ?></p>
        </td>
      </tr>
      <tr>
          <th scope="row"><label for="embed_hubspot_cache"><?php _e('Cache Expiration', 'integration-hubspot-forms') ?></label></th>
        <td>
          <input type="number" id="embed_hubspot_cache" name="embed_hubspot_cache" value="<?php form_option( 'embed_hubspot_cache' ); ?>" placeholder="0" class="small-text">
          <p class="description"><?php _e('This option defines how many seconds to keep the HubSpot cache for. Defaults to 0 (as long as possible).', 'integration-hubspot-forms'); ?></p>
        </td>
      </tr>
    </table>
  </div>
</div>

<?php @do_settings_sections( 'embed_hubspot_forms' ); ?>

<?php submit_button(); ?>

</form>

</div>
