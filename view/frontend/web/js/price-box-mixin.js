define(['jquery'], function ($) {

  return function (widget) {
    var globalOptions = {
      productId: null,
      priceConfig: null,
      prices: {},
      priceTemplate: '<span class="price"><%- data.formatted %>/each</span>'
    };

    $.widget('mage.priceBox', widget, {
      options: globalOptions
    });
    return $.mage.priceBox;
  }
});
