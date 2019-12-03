/**
 * @module package/quiqqer/gdpr/bin/controls/CookieConsent
 * @author www.pcsg.de (Henning Leutz)
 * @author www.pcsg.de (Jan Wennrich)
 */
define('package/quiqqer/gdpr/bin/controls/CookieConsent', [
    'qui/QUI',
    'qui/controls/Control'
], function (QUI, QUIControl) {
    "use strict";

    var lg = 'quiqqer/gdpr';

    return new Class({
        Extends: QUIControl,
        Type   : 'package/quiqqer/gdpr/bin/controls/CookieConsent',

        Binds: [
            'initialize',
            '$onInject',
            'hide',
            'hideImmediately',
            'show',
            'accept',
            'blockPageUsage',
            'allowPageUsage',
            'toggleInfoSection'
        ],


        initialize: function (options) {
            this.parent(options);

            this.$ButtonAccept = null;
            this.$ButtonShowInfo = null;
            this.$InfoSection = null;
            this.$Banner = null;

            this.addEvents({
                onImport: this.$onImport
            });
        },


        $onImport: function () {
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

            this.$ButtonShowInfo = Element.getElementById('quiqqer-cookieconsent-toggle-info');
            this.$InfoSection = Element.getElementById('quiqqer-cookieconsent-details');

            if (this.$ButtonShowInfo && this.$InfoSection) {
                this.$ButtonShowInfo.addEventListener('click', this.toggleInfoSection);
            }

            this.show();
        },


        toggleInfoSection: function () {
            this.$ButtonShowInfo.classList.toggle('open');
            this.$InfoSection.classList.toggle('hidden');
        },


        accept: function () {
            this.allowPageUsage();
            this.hideAnimated();

            require(['package/quiqqer/gdpr/bin/CookieManager'], function (CookieManager) {
                CookieManager.acceptCookieCategories(this.getSelectedCategories());
            }.bind(this));
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
                display: 'flex'
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
        },

        /**
         * Returns an array with the names of the selected cookie categories.
         *
         * @return {[]}
         */
        getSelectedCategories: function () {
            var categoryElements = this.getElm().getElementsByClassName('quiqqer-cookieconsent-category');

            var categories = [];

            for (var i = 0; i < categoryElements.length; i++) {
                var Category = categoryElements[i];
                var categoryName = Category.value;

                if (!Category.checked) {
                    continue;
                }

                categories.push(categoryName);
            }

            return categories;
        }
    });
});
