define(
    [
        "jquery"
    ],
    function() {
        "use strict";

        // SEARCH BOX
        var $box = jQuery('#search, #search-submit');
        var $search = jQuery('#identifying-glass');

        $box.hide();
        $search.click(function() {
            $box.show();
            $search.hide();
        });

        jQuery(document).mouseup(function(e) {
            if (!$box.is(e.target) && $box.has(e.target).length === 0) {
                $box.hide();
                $search.show();
            }
        });
    }
);
