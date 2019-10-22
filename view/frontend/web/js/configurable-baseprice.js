require([
    'jquery',
    'jquery/ui',
    'Magento_ConfigurableProduct/js/configurable',
    'Magento_Swatches/js/swatch-renderer',
    'domReady!'
], function($) {
    $('body').on('updatePrice', function (e, data) {

        var swatchWidget = $('.swatch-opt').data('mage-SwatchRenderer');
        if(swatchWidget) {
            var options = _.object(_.keys(swatchWidget.optionsMap), {});

            swatchWidget.element.find('.' + swatchWidget.options.classes.attributeClass + '[option-selected]').each(function () {
                options[$(this).attr('attribute-id')] = $(this).attr('option-selected');
            });
            result = swatchWidget.options.jsonConfig.optionPrices[_.findKey(swatchWidget.options.jsonConfig.index, options)];
        } else {
            var configurableWidget = $('#product_addtocart_form').data('mage-configurable');

            var options = {};

            configurableWidget.element.find(configurableWidget.options.superSelector + ' option:selected').each(function () {
                if($(this).val()) {
                    options[Number($(this).parent().prop('id').replace('attribute', ''))] = $(this).val();
                }

            });

            var config = configurableWidget.option('spConfig');
            result = config.optionPrices[_.findKey(config.index, options)];
        }

        var basePriceField = $('.product-info-price .baseprice');
        if (typeof result != 'undefined' && basePriceField.length) {
            basePriceField.html(result.magenerds_baseprice_text);
        }
    });
});