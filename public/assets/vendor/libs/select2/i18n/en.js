/*! Select2 4.0.13 | https://github.com/select2/select2/blob/master/LICENSE.md */

(function () {
  if (jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd) {
    var e = jQuery.fn.select2.amd;
    e.define("select2/i18n/en", [], function () {
      return {
        errorLoading: function () {
          return "The results could not be loaded.";
        },
        inputTooLong: function (e) {
          var t = e.input.length - e.maximum;
          return "Please delete " + t + " character" + (t == 1 ? "" : "s");
        },
        inputTooShort: function (e) {
          var t = e.minimum - e.input.length;
          return "Please enter " + t + " or more characters";
        },
        loadingMore: function () {
          return "Loading more results…";
        },
        maximumSelected: function (e) {
          return "You can only select " + e.maximum + " item" + (e.maximum != 1 ? "s" : "");
        },
        noResults: function () {
          return "No results found";
        },
        searching: function () {
          return "Searching…";
        },
        removeAllItems: function () {
          return "Remove all items";
        },
        removeItem: function () {
          return "Remove item";
        },
        search: function () {
          return "Search";
        },
      };
    });
  }
})();

