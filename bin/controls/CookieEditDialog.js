/**
 * @module package/quiqqer/gdpr/bin/controls/CookieEditDialog
 * @author www.pcsg.de (Jan Wennrich)
 */
define('package/quiqqer/gdpr/bin/controls/CookieEditDialog', [
    'qui/QUI',

    'qui/controls/windows/Confirm',

    'Locale',
    'Mustache',

    'text!package/quiqqer/gdpr/bin/controls/CookieEditDialog.html',
    'css!package/quiqqer/gdpr/bin/controls/CookieEditDialog.css'
], function (QUI, QUIConfirm, QUILocale, Mustache, template) {
    "use strict";

    var lg = 'quiqqer/gdpr';

    return new Class({

        Extends: QUIConfirm,
        Type   : 'package/quiqqer/gdpr/bin/controls/CookieEditDialog',

        options: {
            maxWidth : 600,
            maxHeight: 400,
            title    : QUILocale.get(lg, 'dialog.edit.title'),
            autoclose: false,
            texticon : false,
            icon     : 'fa fa-pencil',
            data     : null
        },


        $TeaserTextInput: false,

        // span displaying the length of the current teaser-text
        $TeaserTextLengthValue: false,

        initialize: function (options) {
            this.parent(options);

            this.setAttribute('autoclose', false);

            this.addEvents({
                'onOpen'  : this.$onOpen
            });
        },


        $onOpen: function () {
            var self    = this,
                Content = self.getContent(),
                data    = this.getAttribute('data');

            Content.set('html', Mustache.render(template, {
                data         : data,
                nameLabel    : QUILocale.get(lg, 'cookie.name'),
                originLabel  : QUILocale.get(lg, 'cookie.origin'),
                purposeLabel : QUILocale.get(lg, 'cookie.purpose'),
                lifetimeLabel: QUILocale.get(lg, 'cookie.lifetime'),
                categoryLabel: QUILocale.get(lg, 'cookie.category'),
                category     : {
                    essential  : QUILocale.get(lg, 'cookie.category.essential'),
                    preferences: QUILocale.get(lg, 'cookie.category.preferences'),
                    statistics : QUILocale.get(lg, 'cookie.category.statistics'),
                    marketing  : QUILocale.get(lg, 'cookie.category.marketing')
                }
            }))
            ;
        },


        reportValidity: function () {
            return this.getContent().getElementById('quiqqer-cookie-edit-dialog-content').reportValidity();
        },


        getValues: function () {
            var InputDiv = this.getContent().getElementById('quiqqer-cookie-edit-dialog-content');

            return {
                id      : InputDiv.getElementById('quiqqer-cookie-edit-dialog-id').value,
                name    : InputDiv.getElementById('quiqqer-cookie-edit-dialog-name').value,
                origin  : InputDiv.getElementById('quiqqer-cookie-edit-dialog-origin').value,
                purpose : InputDiv.getElementById('quiqqer-cookie-edit-dialog-purpose').value,
                lifetime: InputDiv.getElementById('quiqqer-cookie-edit-dialog-lifetime').value,
                category: InputDiv.getElementById('quiqqer-cookie-edit-dialog-category').value
            };
        }
    });
});
