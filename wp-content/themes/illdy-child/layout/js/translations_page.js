var TranslationsPage = (function(window, $) {

  var create = function(type, className, id) {
    el = window.document.createElement(type);
    className && el.setAttribute('class', className);
    id && el.setAttribute('id', id);
    return el;
  };


  function TranslationsPage() {

    this.translationsData;
    this.sidebar;
    this.sidebarGutter;
    this.mainContent;
    this.mainContentRoot;
    this.dataUrl = '/wp-content/themes/illdy-child/data/translations.json';

    this.init = function(rootId) {
      // Get container
      var container = document.querySelector(rootId);
      if (!container) {
        console.error('container ' + rootId + ' for resources cannot be found');
        return;
      }

      // Bind methods that will be called elsewhere
      this.renderPage = this.renderPage.bind(this);
      this.handleFailedInit = this.handleFailedInit.bind(this);
      this.onLanguageChange = this.onLanguageChange.bind(this);
      this.onToggleClick = this.onToggleClick.bind(this);

      // Create and append sidebar to the container
      var sidebarContainer = create('section', 'sidebar', 'sidebar');
      container.appendChild(sidebarContainer);
      this.sidebar = new Sidebar(sidebarContainer);
      this.sidebar.onLanguageChange = this.onLanguageChange;

      // Create and append sidebar gutter to the container
      var sidebarGutterContainer = create('section', 'sidebar-gutter', 'sidebar-gutter');
      container.appendChild(sidebarGutterContainer);
      this.sidebarGutter = new SidebarGutter(sidebarGutterContainer);
      this.sidebarGutter.onToggleClick = this.onToggleClick;
      // var sidebarGutterContainer = this.createSidebarGutter();

      // Create and append main content panel to the container
      var mainContentRoot = create('section', 'main-content', 'main-content');
      container.appendChild(mainContentRoot);
      this.mainContent = new MainContent(mainContentRoot);

      // Get translation data and return a promise
      return $.get(this.dataUrl, this.setTranslationsData.bind(this));
    };

    this.setTranslationsData = function(data) {
      if (!data) {
        console.error('translations.json doesn\'t provide any data');
        return;
      }
      this.translationsData = typeof data === 'string' ? JSON.parse(data) : data;
    };

    this.onLanguageChange = function(prevLang, currentLang) {
      // When the selection in sidebar changes, update the resources displayed
      // in the main content.
      var selectedLanguage = this.translationsData.filter(function(lang) {
        return lang.code === currentLang;
      });
      this.mainContent.setContents(selectedLanguage);
      this.mainContent.render();
      $('#translations-container').removeClass('opened');
    };

    this.onToggleClick = function() {
      $('#translations-container').toggleClass('opened');
    }

    this.renderPage = function() {
      this.sidebarGutter.render();

      // Give sidebar the list of languges
      this.sidebar.setList(this.translationsData);

      // Pre-select a language if there's something in the query string
      // TODO: Look for the 'lang' parameter on the query string specifically
      var searchString = window.location.search;
      if (searchString) {
        var langSearch = searchString.split('=')[1];
        // Will trigger sidebar.render();
        this.sidebar.setSelected(langSearch);
        return;
      }

      // Default to English resources. Will trigger sidebar.render();
      this.sidebar.setSelected('en');
    };

    this.handleFailedInit = function(err) {
      console.error('Some error happened.', err);
    };

  }


  function SidebarGutter(rootEl) {

    this.rootEl = rootEl;
    this.onToggleClick;

    this.setOnToggleClick = function(handler) {
      this.onToggleClick = handler;
    };

    this.render = function() {
      var toggle = create('div', 'sidebar-toggle', 'sidebar-toggle');

      toggle.innerText = '. . .';
      toggle.addEventListener('click', this.onToggleClick);

      this.rootEl.appendChild(toggle);
    };
  }


  function Sidebar(rootEl) {

    this.rootEl = rootEl;
    this.list;
    this.selected;
    this.onLanguageChange;

    this.setList = function(list) {
      // Remove unneeded properties
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

      if (this.onLanguageChange && typeof this.onLanguageChange === 'function') {
        this.onLanguageChange(oldSelected, this.selected);
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
      return this.list.map((function(lang) {
        var item = create('li', 'lang-selector', 'lang-selector-' + lang.code);
        item.setAttribute('data-code', lang.code);
        item.setAttribute('data-direction', lang.direction);
        item.innerText = lang.englishName || lang.name;
        item.addEventListener('click', this.onLangClick.bind(this));

        var code = create('span', 'lang-code', 'lang-code-' + lang.code);
        code.innerText = lang.code;
        item.appendChild(code);

        return item;
      }).bind(this));
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
    this.contents;

    this.setContents = function(contents) {
      this.contents = contents;
    };

    this.createLangHeader = function(name, englishName) {
      var header = create('h3', 'lang-title');
      header.innerText = name || 'Unknown';
      if (englishName && englishName !== name) {
        header.innerText += ' - ' + englishName;
      }
      return header;
    };

    this.createContentList = function(contents) {
      var container = create('div', 'content-list');
      contents.forEach(function(content) {
        container.appendChild(this.createContent(content));
      }, this);
      return container;
    };

    this.createContent = function(content, langCode) {
      var container = create('div', 'content ' + content.code);
      container.appendChild(this.createContentHeader(content.name, content.checkingLevel));

      if (content.links) {
        container.appendChild(this.createContentLinks(content.links));
      }

      var subcontents = this.createSubcontents(content.subcontents);
      var contentSubject = content.subject && content.subject.toLowerCase();
      if (contentSubject && (contentSubject === 'bible' || 
                             contentSubject === 'bible stories' || 
                             contentSubject === "translator notes" || 
                             contentSubject === "bible translation comprehension questions" ||
                             contentSubject === "reference" )) {
        container.appendChild(this.createAccordion(subcontents));
      } else {
        subcontents.forEach(function(subcontent) {
          container.appendChild(subcontent);
        });
      }

      return container;
    };

    this.createContentHeader = function(title, checkingLevel) {
      var container = create('div', 'content-header');

      var header = create('h4', 'content-title');
      header.innerText = title || 'Unknown';

      container.appendChild(header);
      container.appendChild(this.createCheckingLevelIcon(checkingLevel));
      return container;
    };

    this.createCheckingLevelIcon = function(checkingLevel) {
      checkingLevel = checkingLevel || '';
      var checkingLevelIcon = create('img', 'content-checking-level');
      var imagePath = WPURL.siteurl + '/layout/images';
      var iconMapping = {
        '': imagePath + '/checking_level_unknown.png',
        '1': imagePath + '/checking_level_1.png',
        '2': imagePath + '/checking_level_2.png',
        '3': imagePath + '/checking_level_3.png',
      };
      var titleMapping = {
        '': 'Translation started, but the checking level is unknown',
        '1': 'Approved by local church translators',
        '2': 'Approved by the leaders of a local church',
        '3': 'Approved by the leaders of multiple local churches',
      };
      checkingLevelIcon.setAttribute('src', iconMapping[checkingLevel]);
      checkingLevelIcon.setAttribute('title', titleMapping[checkingLevel]);
      checkingLevelIcon.setAttribute('data-toggle', 'tooltip');
      checkingLevelIcon.setAttribute('data-placement', 'left');
      $(checkingLevelIcon).tooltip();
      return checkingLevelIcon;
    };

    this.createContentLinks = function(links) {
      var container = create('div', 'content-links');
      this.createLinkEls(links, 'content-link btn btn-primary card').forEach(function(link) {
        link && container.appendChild(link);
      });
      return container;
    }

    this.createLinkEls = function(links, className) {
      return links.filter(function(link) {
        // Filter out all PDF links of non-English resources because they lead
        // to a 404.
        // TODO: Check for each link and make sure they are ok. Maybe on the
        // backend.
        return link;
      })
      .map(function(link) {
          var linkEl = create('a', className);
          linkEl.innerText = link.format;
          if (link.format === 'zip') {
            linkEl.innerText += ' (' + link.zipContent + ')';
          }
          if (link.quality) {
            var qualitySpan = create('i', 'quality');
            qualitySpan.innerText = ' - ' + link.quality;
            linkEl.appendChild(qualitySpan);
          }
          linkEl.setAttribute('href', link.url);
          linkEl.setAttribute('target', '_blank');
          return linkEl;
      });
    };

    this.createSubcontents = function(subcontents, lang) {
      return subcontents.filter(function(subcontent) {
        return subcontent.links;
      })
      .map((function(subcontent) {
        var subcontentRow = create('div', 'subcontent');
        subcontentRow.setAttribute('data-category', subcontent.category);

        var subcontentTitle = create('p', 'subcontent-title');
        subcontentTitle.innerText = subcontent.name || subcontent.code || "Other";
        subcontentRow.appendChild(subcontentTitle);

        var linkContainer = create('div', 'subcontent-links');
        this.createLinkEls(subcontent.links, 'subcontent-link btn btn-default card', lang).forEach(function(link) {
          linkContainer.appendChild(link);
        });
        subcontentRow.appendChild(linkContainer);

        return subcontentRow;
      }).bind(this));
    };

    this.createAccordion = function(subcontents) {
      var accordion = create('div', 'container-wrapper subcontent-accordion');
      var containers = {
        'bible-ot': create('div', 'ot-subcontent-container'),
        'bible-nt': create('div', 'nt-subcontent-container'),
        'obs': create('div', 'obs-subcontent-container'),
        'other': create('div', 'other-subcontent-container'),
      };

      // Sort subcontents into the appropriate container
      subcontents.forEach(function(subcontent) {
        if (subcontent) {
          var category = subcontent.dataset.category.trim() || 'other';
          if (!(category in containers)) {
              category = 'other';
          }
          containers[category].appendChild(subcontent);
        }
      });

      // Only append non-empty containers to the accordion
      if (containers['bible-ot'].childNodes.length) {
        var otContainerTitle = create('h5', 'ot-subcontent-title');
        otContainerTitle.innerText = 'Old Testament';
        accordion.appendChild(otContainerTitle);
        accordion.appendChild(containers['bible-ot']);
      }
      if (containers['bible-nt'].childNodes.length) {
        var ntContainerTitle = create('h5', 'nt-subcontent-title');
        ntContainerTitle.innerText = 'New Testament';
        accordion.appendChild(ntContainerTitle);
        accordion.appendChild(containers['bible-nt']);
      }
      if (containers['obs'].childNodes.length) {
        var obsContainerTitle = create('h5', 'obs-subcontent-title');
        obsContainerTitle.innerText = 'Chapters';
        accordion.appendChild(obsContainerTitle);
        accordion.appendChild(containers['obs']);
      }
      if (containers['other'].childNodes.length) {
        var otherContainerTitle = create('h5', 'other-subcontent-title');
        otherContainerTitle.innerText = 'Other';
        accordion.appendChild(otherContainerTitle);
        accordion.appendChild(containers['other']);
      }

      $(accordion).accordion({
        collapsible: true,
        heightStyle: 'content',
        active: false,
        icons: {
          header: 'expand-resource',
          activeHeader: 'collapse-resource'
        }
      });

      return accordion;
    };

    this.render = function() {
      $(this.rootEl).empty();
      this.contents.forEach(function(lang) {
        this.rootEl.appendChild(this.createLangHeader(lang.name, lang.englishName));
        this.rootEl.appendChild(this.createContentList(lang.contents, lang.code));
      }, this);
    };

  }


  return TranslationsPage;

})(window, jQuery);

console.log(WPURL);
