/**
 * @module package/quiqqer/gdpr/bin/classes/CookieManager
 * @author www.pcsg.de (Jan Wennrich)
 */
define('package/quiqqer/gdpr/bin/classes/CookieManager', [
    'qui/controls/Control',
    'Ajax'
], function (QUIControl, QUIAjax) {
    "use strict";

    return new Class({
        Type: 'package/quiqqer/gdpr/bin/classes/CookieManager',

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

                QUIAjax.post('package_quiqqer_gdpr_ajax_acceptCookies', callback, {
                    'package'   : 'quiqqer/gdpr',
                    'categories': JSON.encode(categories)
                });
            });
        },

        /**
         * Checks if the cookie with the given name was accepted.
         * Returns a promise, which resolves with a boolean containing the result.
         *
         * The cookie name should be the name of the cookie's PHP-class or the cookie's ID for manual cookies.
         * Keep in mind that you need to escape backslashes (see the valid example below)!
         *
         * @example isCookieAccepted('QUI\\GDPR\\Cookies\\QuiqqerSessionCookie')
         * @example isCookieAccepted(12)
         *
         * @param {string} cookieName
         *
         * @return {Promise<boolean>}
         */
        isCookieAccepted: function (cookieName) {
            return new Promise(function (resolve, reject) {
                QUIAjax.get('package_quiqqer_gdpr_ajax_isCookieAccepted', function (result) {
                    resolve(result);
                }, {
                    'package'   : 'quiqqer/gdpr',
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

            QUIAjax.post('package_quiqqer_gdpr_ajax_revokeCookies', function (result) {
                self.fireEvent('cookiesRevoked');

                if (reloadPage) {
                    location.reload();
                }
            }, {
                'package': 'quiqqer/gdpr'
            });
        },


        /**
         * Returns all of the given project's cookies formatted for a Grid-control.
         *
         * @param projectName
         * @param page
         * @param perPage
         * @param sortOn
         * @param sortBy
         */
        getCookiesForGrid: function (projectName, page, perPage, sortOn, sortBy) {
            return new Promise(function (resolve, reject) {
                QUIAjax.get('package_quiqqer_gdpr_ajax_getCookiesForGrid', resolve, {
                    'package'  : 'quiqqer/gdpr',
                    onError    : reject,
                    projectName: projectName,
                    page       : page,
                    perPage    : perPage,
                    sortOn     : sortOn,
                    sortBy     : sortBy
                });
            });
        },


        /**
         * Edit's the cookie with the given ID to take the given arguments.
         *
         * @param data
         * @param projectName
         */
        editCookie: function (data, projectName) {
            return new Promise(function (resolve, reject) {
                QUIAjax.post('package_quiqqer_gdpr_ajax_editCookie', resolve, {
                    'package'  : 'quiqqer/gdpr',
                    onError    : reject,
                    projectName: projectName,
                    data       : JSON.stringify(data)
                });
            });
        },


        /**
         * Deletes the manually added cookie with the given ID
         *
         * @param id
         */
        deleteCookie: function (id, projectName) {
            return new Promise(function (resolve, reject) {
                QUIAjax.post('package_quiqqer_gdpr_ajax_deleteCookie', resolve, {
                    'package'  : 'quiqqer/gdpr',
                    onError    : reject,
                    projectName: projectName,
                    id         : id
                });
            });
        }
    });
});
