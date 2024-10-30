<?php
/**
 * EmbedHubspotForms popup form.
 *
 * @package EmbedHubspotForms
 */

if ( ! current_user_can( 'manage_options' ) )
  wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

?>
<?php add_thickbox(); ?>
<div id="hubspot-form-popup" style="display:none;">
  <table class="form-table">
      <tr>
        <th scope="row"><label for="hubspot_forms_embed"><?php _e('Hubspot Form', 'integration-hubspot-forms') ?></label></th>
      <td>
        <?php if (empty($forms[0]->portalId)) : ?>
          <?php _e('Forms are getting cached in Wordpress for 1 hour. Please try refresh the page to load forms.', 'integration-hubspot-forms') ?>
        <?php else : ?>
          <select id="hubspot_forms_embed" name="hubspot_forms_embed" aria-describedby="timezone-description" class="regular-text">
            <option value=""><?php _e('Choose a form to embed', 'integration-hubspot-forms'); ?></option>
              <?php foreach ($forms as $form) : ?>
                <option value="<?php echo $form->portalId . '::' . $form->guid ?>"><?php echo $form->name; ?></option>
              <?php endforeach; ?>
          </select>
        <?php endif; ?>
      </td>
    </tr>
    <?php if ( get_option('embed_hubspot_salesforce_support') ) : ?>
    <tr>
        <th scope="row"><label for="sfdcCampaignId"><?php _e('Salesforce Campaign ID', 'integration-hubspot-forms') ?></label></th>
      <td>
        <input type="text" id="sfdcCampaignId" name="sfdcCampaignId" class="regular-text">
        <p class="description"><?php _e('The record ID of the Salesforce Campaign that you want to assign to contacts filling out this form.', 'integration-hubspot-forms'); ?></p>
      </td>
    </tr>
    <?php endif; ?>
    <tr>
        <th scope="row"></th>
      <td>
        <input type="checkbox" id="css" name="css" value="1" checked>
        <label for="css"><?php _e('Remove HubSpot default styling', 'integration-hubspot-forms') ?></label>
      </td>
    </tr>
    <tr>
      <th scope="row"></th>
      <td>
        <input type="button" name="insert" id="hubspot_forms_embed_insert" class="button button-primary" value="<?php _e('Insert Form', 'integration-hubspot-forms'); ?>">
        <input type="button" name="insert" id="hubspot_forms_embed_cancel" class="button" value="<?php _e('Cancel'); ?>">
      </td>
  </table>
</div>
