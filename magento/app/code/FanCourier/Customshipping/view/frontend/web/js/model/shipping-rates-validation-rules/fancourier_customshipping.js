/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 //crearea regulilor de validare - specificarea campurilor necesare pentru a folosi FAN Courier shipping
/*global define*/
define(
    [],
    function () {
        "use strict";
        return {
            getRules: function() {
                return {
                    'firstname': {
                        'required': true
                    },
                    'lastname': {
                        'required': true
                    },
                    'street': {
                        'required': true
                    },
					'city': {
                        'required': true
                    },
					'region_id': {
                        'required': true
                    },
					'postcode': {
                        'required': true
                    },
					'country_id': {
                        'required': true
                    },
					'telephone': {
                        'required': true
                    }
					
                };
            }
        };
    }
);