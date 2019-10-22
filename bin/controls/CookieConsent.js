/**
 * @module package/quiqqer/cookieconsent/bin/controls/CookieConsent
 * @author www.pcsg.de (Henning Leutz)
 */
define('package/quiqqer/cookieconsent/bin/controls/CookieConsent', [
    'qui/QUI',
    'qui/controls/Control'
], function (QUI, QUIControl) {
    "use strict";

    var lg = 'quiqqer/cookieconsent';

    return new Class({
        Extends: QUIControl,
        Type   : 'package/quiqqer/cookieconsent/bin/controls/CookieConsent',

        Binds: [
            'initialize',
            '$onInject',
            'hide',
            'hideImmediately',
            'show',
            'accept',
            'blockPageUsage',
            'allowPageUsage'
        ],


        initialize: function (options) {
            this.parent(options);

            this.$ButtonAccept = null;
            this.$Banner = null;

            this.addEvents({
                onImport: this.$onImport
            });
        },


        $onImport: function () {
            if (QUI.Storage.get('quiqqer-cookieconsent-accepted')) {
                return;
            }

            var Element = this.getElm();

            if (!Element) {
                return;
            }

            this.$Banner = Element.getElementById('quiqqer-cookieconsent-banner');

            if (!this.$Banner) {
                return;
            }

            if (this.getAttribute('position') === 'topSlide') {
                Element.inject(document.body, 'top');
            }

            if (this.getAttribute('blocksite')) {
                this.blockPageUsage();
            }

            this.$ButtonAccept = Element.getElementById('quiqqer-cookieconsent-accept');

            if (!this.$ButtonAccept) {
                return;
            }

            this.$ButtonAccept.addEventListener('click', this.accept);

            this.show();
        },

        accept: function () {
            QUI.Storage.set('quiqqer-cookieconsent-accepted', true);
            this.allowPageUsage();
            this.hideAnimated();

            require(['Ajax'], function (QUIAjax) {
                QUIAjax.post('package_quiqqer_cookieconsent_ajax_acceptCookies', null, {
                    'package': 'quiqqer/cookieconsent'
                });
            });
        },

        hideAnimated: function (callback, duration) {
            if (!this.$Banner) {
                return false;
            }

            var animate = {
                display: 'none'
            };

            var position = this.getAttribute('position');

            if (position === 'top') {
                animate.top = 0 - this.getElm().getHeight();
            }

            if (position === 'bottom') {
                animate.bottom = 0 - this.getElm().getHeight();
            }

            moofx(this.$Banner).animate(animate, {
                duration: duration || 300,
                callback: callback
            });

            return true;
        },

        hideImmediately: function () {
            if (!this.$Banner) {
                return false;
            }

            this.$Banner.setStyle('display', 'none');
            return true;
        },

        show: function (callback, duration) {
            if (!this.$Banner) {
                return;
            }

            var animate = {
                display: 'block'
            };

            var position = this.getAttribute('position');
            if (position === 'top') {
                animate.top = 0;
            }

            if (position === 'bottom') {
                animate.bottom = 0;
            }

            moofx(this.$Banner).animate(animate, {
                duration: duration || 300,
                callback: callback
            });
        },


        blockPageUsage: function () {
            document.body.classList.add('cookiebanner-blocks-page');
            this.$Banner.classList.add('page-blocked');
        },


        allowPageUsage: function () {
            document.body.classList.remove('cookiebanner-blocks-page');
            this.$Banner.classList.remove('page-blocked');
        }
    });
});
