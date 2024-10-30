<?php
/**
 * EmbedHubspotForms widget form.
 *
 * @package EmbedHubspotForms
 */
?>
<p>
  <label for="<?php echo $this->get_field_id( 'hubspot_forms_embed' ); ?>"><?php _e( 'Choose Hubspot Form to embed', 'integration-hubspot-forms' ); ?>:</label>
  <select id="<?php echo $this->get_field_id( 'hubspot_forms_embed' ); ?>" name="<?php echo $this->get_field_name( 'hubspot_forms_embed' ); ?>" style="max-width: 100%" class="postform">
    <option value=""><?php _e( 'Choose a form to embed', 'integration-hubspot-forms' ); ?></option>
    <?php foreach ( $forms as $form ) : ?>
      <?php $form_id = $form->portalId . '::' . $form->guid; ?>
      <option value="<?php echo $form_id ?>"<?php if ( $hubspot_current_form == $form_id ) : ?> selected<?php endif; ?>><?php echo $form->name; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p>
  <?php if ( get_option( 'embed_hubspot_salesforce_support' ) ) : ?>
    <label for="<?php echo $this->get_field_id( 'sfdcCampaignId' ); ?>"><?php _e( 'Salesforce Campaign ID', 'integration-hubspot-forms' ) ?>:</label>
    <input type="text" id="<?php echo $this->get_field_id( 'sfdcCampaignId' ); ?>" name="<?php echo $this->get_field_name( 'sfdcCampaignId' ); ?>" value="<?php echo $sfdcCampaignId; ?>" style="width: 100%" class="regular-text">
    <p class="description"><?php _e('The record ID of the Salesforce Campaign that you want to assign to contacts filling out this form.', 'integration-hubspot-forms'); ?></p>
  <?php endif; ?>

  <input type="checkbox" id="<?php echo $this->get_field_id( 'css' ); ?>" name="<?php echo $this->get_field_name( 'css' ); ?>" <?php if ( $css ) : ?> checked<?php endif; ?>>
  <label for="<?php echo $this->get_field_id( 'css' ); ?>"><?php _e( 'Remove HubSpot default styling', 'integration-hubspot-forms' ) ?></label>
</p>
