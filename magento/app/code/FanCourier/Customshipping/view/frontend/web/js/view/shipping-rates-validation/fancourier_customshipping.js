/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/fancourier_customshipping',
        '../../model/shipping-rates-validation-rules/fancourier_customshipping'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        fancourier_customshippingRatesValidator,
        fancourier_customshippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('fancourier_customshipping', fancourier_customshippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('fancourier_customshipping', fancourier_customshippingRatesValidationRules);
        return Component;
    }
);