var ResourcesPage = ( function( window, $, undefined ) {

  var _oldCatalogUrl = 'https://api.door43.org/v3/catalog.json';
  var _newCatalogUrl = 'https://api.unfoldingword.org/uw/txt/2/catalog.json';

  var _getOldCatalog = function($, url) {
    $.get(url, function() {
      console.log('getting', url);
    })
    .done(function(a) {
      console.log('done', a);
    })
    .fail(function(a) {
      console.log('fail', a);
    })
    .always(function(a) {
      console.log('fail', a);
    });
  };

  function ResourcesPage() {

    var oldCatalog;
    var newCatalog;

    this.init = function() {
      _getOldCatalog($, _oldCatalogUrl);
    }

    this.test = function() {
      console.log('this is a test for ResourcesPages', window, $, _oldCatalogUrl, _newCatalogUrl);
    };

  }

  return ResourcesPage;

})( window, jQuery );
