var ResourcesPage = ( function( window, $, undefined ) {

  function ResourcesPage() {

    var container = undefined;
    var resourcesData = undefined;

    this.init = function(containerElId) {

      // Get container
      container = document.querySelector(containerElId);
      if (!container) {
        console.error('container ' + containerElId + ' for resources cannot be found');
        return;
      }

      // Get json data and return a promise
      return $.get('/wp-content/themes/illdy-child/data/resources.json', function(data) {
        if (!data) {
          console.error('resources.json doesn\'t provide any data');
          return;
        }
        resourcesData = data;
      });
    }

    this.renderResources = function(data) {
      // Accordion root
      $(container).append('<div id="accordion"></div>');
      var accordion = $(container).find('#accordion');

      JSON.parse(data).forEach(function(lang) {
        // Section title
        accordion.append('<h3 class="lang-code">' + lang.code + '</h3>');

        accordion.append('<div id="' + lang.code + '-container" class="resource-container"></div>');
        var langResourceContainer = accordion.find('#' + lang.code + '-container');

        lang.resources.forEach(function(res) {
          langResourceContainer.append('<h4 class="resource-title">' + res.name + '</h4>');

          res.links && res.links.forEach(function(l) {
            langResourceContainer.append('<a href="' + l.url + '">' + l.url + '</a>');
          });

          res.content.forEach(function(c, index) {
            if (c.links) {
              langResourceContainer.append('<p id="' + lang.code + '-' + res.slug + '-' + c.slug + '" class="resource-content">' + c.title + '</p>');

              c.links.forEach(function(l) {
                langResourceContainer.find('#' + lang.code + '-' + res.slug + '-' + c.slug).append('<span class="resource-link"><a href="' + l.url + '">' + l.format + '</a>');
              });
            }
          });

        });

      });

      // Initialize jQuery accordion
      accordion.accordion({
        collapsible: true,
        heightStyle: 'content'
      });
    };

    this.handleFailedInit = function(err) {
      console.error('Some error happened.', err);
    };

  }

  return ResourcesPage;

})( window, jQuery );
