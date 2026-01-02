/**
 * Selects & Tags
 */

'use strict';

$(function () {
  const selectPicker = $('.selectpicker'),
    select2 = $('.select2'),
    select2Icons = $('.select2-icons');

  // Bootstrap Select
  // --------------------------------------------------------------------
  if (selectPicker.length) {
    selectPicker.selectpicker();
  }

  // Select2
  // --------------------------------------------------------------------

  // Default
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      var placeholder = $this.data('placeholder') || window.select2DefaultPlaceholder || 'Select value';
      var select2Config = {
        placeholder: placeholder,
        dropdownParent: $this.parent(),
        language: window.select2Language || 'en'
      };
      $this.wrap('<div class="position-relative"></div>').select2(select2Config);
    });
  }

  // Select2 Icons
  if (select2Icons.length) {
    // custom template to render icons
    function renderIcons(option) {
      if (!option.id) {
        return option.text;
      }
      var $icon = "<i class='" + $(option.element).data('icon') + " me-2'></i>" + option.text;

      return $icon;
    }
    var iconsConfig = {
      templateResult: renderIcons,
      templateSelection: renderIcons,
      escapeMarkup: function (es) {
        return es;
      }
    };
    // Add language if available
    if (window.select2Language && typeof $.fn.select2 !== 'undefined' && $.fn.select2.defaults.get('language')) {
      iconsConfig.language = $.fn.select2.defaults.get('language');
    }
    select2Icons.wrap('<div class="position-relative"></div>').select2(iconsConfig);
  }
});
