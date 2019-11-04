/**
 * @module package/quiqqer/gdpr/bin/controls/RevokeCookiesButton
 * @author www.pcsg.de (Jan Wennrich)
 */
define('package/quiqqer/gdpr/bin/controls/RevokeCookiesButton', [
    'qui/QUI',
    'qui/controls/buttons/Button',

    'Locale'
], function (QUI, QUIButton, QUILocale) {
    "use strict";

    var lg = 'quiqqer/gdpr';

    return new Class({
        Extends: QUIButton,
        Type   : 'package/quiqqer/gdpr/bin/controls/RevokeCookiesButton',

        Binds: [
            'initialize'
        ],

        options: {
            text: QUILocale.get(lg, 'control.revoke.button.text'),
            name: 'cookieconsent-button-revoke'
        },

        initialize: function (options) {
            this.parent(options);

            this.addEvents({
                onClick : this.$onClick,
                onImport: this.$onImport
            });
        },

        $onClick: function () {
            require(['package/quiqqer/gdpr/bin/CookieManager'], function (CookieManager) {
                CookieManager.revokeCookies(true);
            });
        },

        $onImport: function () {
            this.$Input = this.getElm();
            this.create().inject(this.$Input.parentNode);
        }
    });
});
