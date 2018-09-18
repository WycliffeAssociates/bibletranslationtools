(function(window, $) {
  document.addEventListener('DOMContentLoaded', function() {
    changeServiceSectionLayout();
  });

  function changeServiceSectionLayout() {
    var serviceWidgets = $('#services .widget_illdy_service');
    serviceWidgets.removeClass('col-sm-4');
    serviceWidgets.addClass('col-sm-3');
  }
})(window, jQuery);
