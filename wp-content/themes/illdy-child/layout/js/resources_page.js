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
      var sidebarGutter = this.createSidebarGutter();
      container.appendChild(sidebarGutter);
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
      $('#resources-container').removeClass('opened');
    };


    this.createSidebarGutter = function() {
      var sidebarGutter = create('section', 'sidebar-gutter', 'sidebar-gutter');
      var sidebarToggle = create('div', 'sidebar-toggle', 'sidebar-toggle');
      sidebarToggle.innerText = '. . .';
      sidebarToggle.addEventListener('click', function(e) {
        $('#resources-container').toggleClass('opened');
      });
      sidebarGutter.appendChild(sidebarToggle);
      return sidebarGutter;
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


      // Default to English resources
      this.sidebar.setSelected('en');
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

      var container = create('div', 'lang-list-container sticky');
      container.appendChild(title);
      container.appendChild(list);

      this.createListItemEls().forEach(function(item) {
        list.appendChild(item);
      });

      return container;
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
      var listEl = this.createListEl();
      this.rootEl.appendChild(listEl);
      $('.lang-list-container').Stickyfill();
    };

  }


  function MainContent(rootEl) {

    this.rootEl = rootEl;
    this.resources;


    this.setResources = function(resources) {
      this.resources = resources;
      this.render();
    };


    this.createLinkEls = function(links, className, lang) {
      return links.filter(function(link) {
        // Filter out all PDF links of non-English resources because they lead
        // to a 404.
        // TODO: Check for each link and make sure they are ok. Maybe on the
        // backend.
        return link && !(lang !== 'en' && link.format === 'pdf');
      })
      .map(function(link) {
          var linkEl = create('a', className);
          linkEl.innerText = link.format;
          linkEl.setAttribute('href', link.url);
          linkEl.setAttribute('target', '_blank');
          return linkEl;
      });
    };


    this.createContentEls = function(contents, lang) {
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
        _this.createLinkEls(content.links, 'content-link', lang).forEach(function(link) {
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
          _this.createContentEls(resource.contents, lang.code).forEach(function(content) {
            content && _this.rootEl.appendChild(content);
          });

        });

      });
    };

  }


  return ResourcesPage;

})( window, jQuery );
