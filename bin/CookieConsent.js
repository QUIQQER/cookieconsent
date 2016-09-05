/**
 * @module package/quiqqer/cookieconsent/bin/CookieConsent
 * @author www.pcsg.de (Henning Leutz)
 *
 * @require qui/QUI
 * @require Locale
 */
define('package/quiqqer/cookieconsent/bin/CookieConsent', [
    'qui/QUI',
    'Locale'
], function (QUI, QUILocale) {
    "use strict";

    if (QUI.Storage.get('quiqqer-cookieconsent-accept')) {
        return;
    }

    var lg = 'quiqqer/cookieconsent';

    var accept = function () {
        var Message = document.getElement('.quiqqer-cookieconsent');
        var animate = {
            opacity: 0
        };

        if (!Message) {
            return;
        }

        if (Message.hasClass('quiqqer-cookieconsent__top')) {
            animate.top = -30;
        } else {
            animate.bottom = -30;
        }

        moofx(Message).animate(animate, {
            duration: 300,
            callback: function () {
                QUI.Storage.set('quiqqer-cookieconsent-accept', 1);
                Message.destroy();
            }
        });
    };

    window.addEvent('load', function () {
        require([
            'css!package/quiqqer/cookieconsent/bin/CookieConsent.css'
        ], function () {
            var Message = new Element('div', {
                'class': 'quiqqer-cookieconsent',
                html   : '<div class="quiqqer-cookieconsent-message">' +
                         QUILocale.get(lg, 'text') +
                         '</div>' +
                         '<button class="quiqqer-cookieconsent-accept">' +
                         QUILocale.get(lg, 'button') +
                         '</button>',
                styles : {
                    opacity: 0
                }
            }).inject(document.body);

            if (typeof window.QUIQQER_CC_LINK !== 'undefined') {
                new Element('a', {
                    href  : window.QUIQQER_CC_LINK,
                    html  : QUILocale.get(lg, 'PrivacyPolicy.link.text'),
                    styles: {
                        marginLeft: 10
                    }
                }).inject(Message.getElement('.quiqqer-cookieconsent-message'));
            }


            switch (window.QUIQQER_CC_POS) {
                case 'bottom':
                    Message.addClass('quiqqer-cookieconsent__bottom');
                    break;

                default:
                case 'top':
                    Message.addClass('quiqqer-cookieconsent__top');
            }


            Message.getElement('.quiqqer-cookieconsent-accept').addEvent('click', accept);

            moofx(Message).animate({
                opacity: 1
            });
        });
    });
});