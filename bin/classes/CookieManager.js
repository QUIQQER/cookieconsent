/**
 * @module package/quiqqer/cookieconsent/bin/classes/CookieManager
 * @author www.pcsg.de (Jan Wennrich)
 */
define('package/quiqqer/cookieconsent/bin/classes/CookieManager', [
    'qui/controls/Control',
    'Ajax'
], function (QUIControl, QUIAjax) {
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

            require(['qui/QUI'], function (QUI) {
                QUI.Storage.set('quiqqer-cookies-accepted-timestamp', Date.now());

                QUIAjax.post('package_quiqqer_cookieconsent_ajax_acceptCookies', callback, {
                    'package'   : 'quiqqer/cookieconsent',
                    'categories': JSON.encode(categories)
                });
            });
        },

        /**
         * Checks if the cookie with the given name was accepted.
         * Returns a promise, which resolves with a boolean containing the result.
         *
         * The cookie name should be the name of the cookie's PHP-class.
         * Keep in mind that you need to escape backslashes (see the valid example below)!
         *
         * @example isCookieAccepted('QUI\\CookieConsent\\Cookies\\QuiqqerSessionCookie')
         *
         * @param {string} cookieName
         *
         * @return {Promise<boolean>}
         */
        isCookieAccepted: function (cookieName) {
            return new Promise(function (resolve, reject) {
                QUIAjax.get('package_quiqqer_cookieconsent_ajax_isCookieAccepted', function (result) {
                    resolve(result);
                }, {
                    'package'   : 'quiqqer/cookieconsent',
                    'cookieName': cookieName,
                    onError     : reject
                });
            });
        },

        /**
         * Revokes all of the user's cookie settings.
         *
         * Set the first parameter to true to reload the page afterwards.
         *
         * @fires 'onCookiesRevoked'
         */
        revokeCookies: function (reloadPage) {
            var self = this;

            if (reloadPage === undefined) {
                reloadPage = false;
            }

            QUIAjax.post('package_quiqqer_cookieconsent_ajax_revokeCookies', function (result) {
                self.fireEvent('cookiesRevoked');

                if (reloadPage) {
                    location.reload();
                }
            }, {
                'package': 'quiqqer/cookieconsent'
            });
        }
    });
});
