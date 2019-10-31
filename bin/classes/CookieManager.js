/**
 * @module package/quiqqer/cookieconsent/bin/classes/CookieManager
 * @author www.pcsg.de (Jan Wennrich)
 */
define('package/quiqqer/cookieconsent/bin/classes/CookieManager', [
    'qui/controls/Control'
], function (QUIControl) {
    "use strict";

    return new Class({
        Type: 'package/quiqqer/cookieconsent/bin/classes/CookieManager',

        Extends: QUIControl,

        /**
         * Sets the given cookie-categories as accepted.
         * Sends the categories to the server to process them and mark them as accepted.
         * Once the server-request is complete, the callback passed as the second parameter will be called.
         *
         * Fires the 'onCookieCategoriesAccepted' with the accepted categories.
         *
         * @param categories String[]
         * @param callback function
         *
         * @fires 'onCookieCategoriesAccepted'
         */
        acceptCookieCategories: function (categories, callback) {
            this.fireEvent('cookieCategoriesAccepted', categories);

            require(['qui/QUI', 'Ajax'], function (QUI, QUIAjax) {
                QUI.Storage.set('quiqqer-cookies-accepted-timestamp', Date.now());

                QUIAjax.post('package_quiqqer_cookieconsent_ajax_acceptCookies', callback, {
                    'package'   : 'quiqqer/cookieconsent',
                    'categories': JSON.encode(categories)
                });
            });
        }
    });
});
