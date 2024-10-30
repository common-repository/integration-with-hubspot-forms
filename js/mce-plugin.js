/**
 * @package WP_HubspotForms
 * Hubspot TinyMCE plugin.
 */

(function() {

  tinymce.create( 'tinymce.plugins.embedHubspot', {

    init : function( ed, url ) {

      var t = this;

      ed.on('BeforeSetcontent', function( o ) {
        o.content = t._do_render_form( o.content );
      });

      ed.on('PostProcess', function( o ) {
        if ( o.get )
          o.content = t._get_form( o.content );
      });

    },

    _do_render_form : function( co ) {
      return co.replace(/\[hubspotform ([^\]]*)\]/g, function( a, b ) {
        var baseUrl = ( _hspt_preview_base_url !== undefined ) ? _hspt_preview_base_url + '/' : '/';
        return '<p><iframe class="hubspot-embed" title="' + tinymce.DOM.encode(b) + '" style="border:none" width="560" height="200" src="' + baseUrl + 'index.php?hubspot_embed_plugin=' + encodeURIComponent(b) + '" frameborder="0"></iframe></p>';
      });
    },

    _get_form : function( co ) {

      function getAttr( s, n ) {
        n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec( s );
        return n ? tinymce.DOM.decode( n[1] ) : '';
      };
  
      return co.replace(/(?:<p[^>]*>)*(<iframe[^>]+>)(?:<\/iframe><\/p>)*/g, function( a, iframe ) {
        var cls = getAttr( iframe, 'class' );

        if ( cls.indexOf('hubspot-embed') != -1 ) {
          return '<p>[hubspotform ' + tinymce.trim( getAttr( iframe, 'title' ) ) + ']</p>';
        }

        return a;
      });

    },

  });

  // Register plugin
  tinymce.PluginManager.add( 'embedHubspot', tinymce.plugins.embedHubspot );

})();
