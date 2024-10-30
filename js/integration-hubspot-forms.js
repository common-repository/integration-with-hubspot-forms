/**
 * @package WP_HubspotForms
 * Admin JS.
 */

jQuery(function ($) {

  $( '#hubspot_forms_embed_insert' ).click( insert_hubspot_form );

  // Insert hubspot form data from a popup.
  function insert_hubspot_form() {
    var _form_id = $( '#hubspot_forms_embed' );
    var _sfdcCampaignId = $( '#sfdcCampaignId' );
    var _css = $( '#css' );
    if ( _form_id.val() !== '' ) {
      var components = _form_id.val().split( "::" );
      var params = '';
      if ( _sfdcCampaignId.val() !== '' && _sfdcCampaignId.val() !== undefined ) {
        params += ' sfdcCampaignId="' + _sfdcCampaignId.val() + '"';
      }
      if ( _css.is( ':checked' ) ) {
        params += ' css=""';
      }
      window.send_to_editor( '[hubspotform portal_id="' + components[0] + '" form_id="' + components[1] + '"' + params + ']' );
      $( "#TB_closeWindowButton" ).trigger( 'click' );
      _form_id.val( '' );
      _sfdcCampaignId.val( '' );
    }
    else {
      alert( objectL10n.no_form_selected );
    }
  }

  // Close Hubspot Embed form.
  $( '#hubspot_forms_embed_cancel' ).click(function( e ) {
    $( "#TB_closeWindowButton" ).trigger( 'click' );
  });

});
