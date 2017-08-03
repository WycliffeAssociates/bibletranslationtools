var ResourcesPage = ( function( window, $, undefined ) {

  var create = function(type, className, id) {
    el = window.document.createElement(type);
    className && el.setAttribute('class', className);
    id && el.setAttribute('id', id);
    return el;
  };

  function ResourcesPage() {

    this.resourcesData;
    this.sidebar;
    this.mainContent;
    this.mainContentRoot;
    this.dataUrl = '/wp-content/themes/illdy-child/data/resources.json';


    this.init = function(rootId) {

      // Get container
      var container = document.querySelector(rootId);
      if (!container) {
        console.error('container ' + rootId + ' for resources cannot be found');
        return;
      }

      // Create and append main sections to the container
      var sidebarRoot = create('section', 'sidebar', 'sidebar');
      container.appendChild(sidebarRoot);
      var mainContentRoot = create('section', 'main-content', 'main-content');
      container.appendChild(mainContentRoot);

      // Mount sidebar and main content components
      this.sidebar = new Sidebar(sidebarRoot);
      this.sidebar.onSelectedChange = this.onSelectedChange.bind(this);
      this.mainContent = new MainContent(mainContentRoot);

      // Bind methods
      this.renderPage = this.renderPage.bind(this);

      // Get json data and return a promise
      var _this = this;
      return $.get(this.dataUrl, function(data) {
        if (!data) {
          console.error('resources.json doesn\'t provide any data');
          return;
        }
        _this.resourcesData = typeof data === 'string' ? JSON.parse(data) : data;
      });
    };


    this.onSelectedChange = function(oldVal, newVal) {
      // When the selection in sidebar changes, update the resources displayed
      // in the main content.
      this.mainContent.setResources(this.resourcesData.filter(function(lang) {
        return lang.code === newVal;
      }));
    };


    this.renderPage = function() {
      // Give sidebar the list of languges
      this.sidebar.setList(this.resourcesData);

      // Pre-select a language if there's something in the query string
      // TODO: Look for the 'lang' parameter specifically
      var searchString = window.location.search;
      if (searchString) {
        var langSearch = searchString.split('=')[1];
        this.sidebar.setSelected(langSearch);
        return;
      }

      // debugger;
      // Default to English resources
      this.sidebar.setSelected('en');
    };


    this.renderResources = function(data) {
      // // Safe guard to make sure we don't parse the already-parsed data, which
      // // will throw an error.
      // var jsonData = typeof data === 'string' ? JSON.parse(data) : data;
      //
      // // Accordion root
      // $(container).append('<div id="accordion"></div>');
      // var accordion = $(container).find('#accordion');
      //
      // jsonData.forEach(function(lang) {
      //   // Section title (Language name)
      //   var langName = lang.name;
      //   if (lang.englishName) {
      //     langName += '<span class="english-name">' + lang.englishName + '</span>';
      //   }
      //   langName += '<span class="lang-code">' + lang.code + '</span>';
      //   accordion.append('<h3 class="lang-section">' + langName + '</h3>');
      //
      //   // Section body
      //   accordion.append('<div id="' + lang.code + '-container" class="resource-container"></div>');
      //   var langResourceContainer = accordion.find('#' + lang.code + '-container');
      //
      //   lang.resources.forEach(function(res) {
      //     langResourceContainer.append('<h4 class="resource-title">' + res.name + '</h4>');
      //
      //     res.links && res.links.forEach(function(l) {
      //       if (l) {
      //         langResourceContainer.append('<a class="resource-link" href="' + l.url + '">' + l.format + '</a>');
      //       }
      //     });
      //
      //     res.contents && res.contents.forEach(function(c, index) {
      //       if (c.links) {
      //         var title = '<span class="content-title">' + c.title + '</span>';
      //         langResourceContainer.append('<p id="' + lang.code + '-' + res.slug + '-' + c.slug + '" class="resource-content">' + title + '</p>');
      //
      //         c.links.forEach(function(l) {
      //           langResourceContainer.find('#' + lang.code + '-' + res.slug + '-' + c.slug).append('<span class="resource-link"><a href="' + l.url + '">' + l.format + '</a>');
      //         });
      //       }
      //     });
      //
      //   });
      //
      // });
      //
      // // Initialize jQuery accordion
      // accordion.accordion({
      //   collapsible: true,
      //   heightStyle: 'content'
      // });
    };


    this.handleFailedInit = function(err) {
      console.error('Some error happened.', err);
    };

  }


  function Sidebar(rootEl) {

    this.rootEl = rootEl;
    this.list;
    this.selected;
    this.onSelectedChange;

    this.setList = function(list) {
      this.list = list.map(function(lang) {
        return {
          code: lang.code,
          name: lang.name,
          englishName: lang.englishName,
          direction: lang.direction
        };
      });
      this.render();
    };


    this.setSelected = function(selected) {
      var oldSelected = this.selected;
      this.selected = selected;

      if (this.onSelectedChange && typeof this.onSelectedChange === 'function') {
        this.onSelectedChange(oldSelected, this.selected);
      }

      if ($) {
        $('.lang-selector.active').removeClass('active');
        $('#lang-selector-' + selected).addClass('active');
      }
    };


    this.createListEl = function() {
      var title = create('h5', 'lang-list-title');
      title.innerText = 'Languages';

      var list = create('ul', 'lang-list');
      list.appendChild(title);

      this.createListItemEls().forEach(function(item) {
        list.appendChild(item);
      });

      return list;
    };


    this.createListItemEls = function() {
      var _this = this;
      return _this.list.map(function(lang) {
        var item = create('li', 'lang-selector', 'lang-selector-' + lang.code);
        item.setAttribute('data-code', lang.code);
        item.setAttribute('data-direction', lang.direction);
        item.innerText = lang.englishName || lang.name;
        item.addEventListener('click', _this.onLangClick.bind(_this));

        var code = create('span', 'lang-code', 'lang-code-' + lang.code);
        code.innerText = lang.code;
        item.appendChild(code);

        return item;
      });
    };


    this.onLangClick = function(e) {
      this.setSelected(e.target.dataset.code);
    };


    this.render = function() {
      $(this.rootEl).empty();
      this.rootEl.appendChild(this.createListEl());
    };

  }


  function MainContent(rootEl) {

    this.rootEl = rootEl;
    this.resources;


    this.setResources = function(resources) {
      this.resources = resources;
      this.render();
    };


    this.createLinkEls = function(links, className) {
      return links.filter(function(link) {
        return link;
      })
      .map(function(link) {
          var linkEl = create('a', className);
          linkEl.innerText = link.format;
          linkEl.setAttribute('href', link.url);
          linkEl.setAttribute('target', '_blank');
          return linkEl;
      });
    };


    this.createContentEls = function(contents) {
      var _this = this;
      return contents.filter(function(content) {
        return content.links;
      })
      .map(function(content) {
        var contentRow = create('div', 'content');

        var contentTitle = create('p', 'content-title');
        contentTitle.innerText = content.title;
        contentRow.appendChild(contentTitle);

        var linkContainer = create('div', 'content-links');
        _this.createLinkEls(content.links, 'content-link').forEach(function(link) {
          linkContainer.appendChild(link);
        });
        contentRow.appendChild(linkContainer);

        return contentRow;
      });
    };


    this.render = function() {
      var _this = this;
      $(this.rootEl).empty();

      this.resources.forEach(function(lang) {

        // Language/Header
        var header = create('h3', 'lang-title');
        header.innerText = lang.englishName || lang.name;
        _this.rootEl.appendChild(header);

        lang.resources.forEach(function(resource) {

          // Resource
          var resourceName = create('h4', 'resource-title');
          resourceName.innerText = resource.name;
          _this.rootEl.appendChild(resourceName);

          // Resource links
          if (resource.links) {
            var linkContainer = create('div', 'resource-links');
            _this.createLinkEls(resource.links, 'resource-link').forEach(function(link) {
              link && linkContainer.appendChild(link);
            });
            _this.rootEl.appendChild(linkContainer);
          }

          // Content and content links
          _this.createContentEls(resource.contents).forEach(function(content) {
            content && _this.rootEl.appendChild(content);
          });

        });

      });
    };

  }


  return ResourcesPage;

})( window, jQuery );
