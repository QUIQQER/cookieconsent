/**
 * Cookie panel
 *
 * @module package/quiqqer/gdpr/bin/controls/CookiePanel
 * @author www.pcsg.de (Jan Wennrich)
 *
 */
define('package/quiqqer/gdpr/bin/controls/CookiePanel', [

    'qui/QUI',
    'qui/controls/desktop/Panel',
    'qui/controls/buttons/Select',
    'qui/controls/buttons/Separator',
    'qui/controls/windows/Confirm',

    'package/quiqqer/gdpr/bin/CookieManager',
    'package/quiqqer/gdpr/bin/controls/CookieEditDialog',

    'controls/grid/Grid',
    'Locale'

], function (QUI, QUIPanel, QUISelect, QUIButtonSeparator, QUIConfirm, CookieManager, CookieEditDialog, Grid, QUILocale) {
    "use strict";

    var lg = 'quiqqer/gdpr';

    return new Class({

        Extends: QUIPanel,
        Type   : 'package/quiqqer/gdpr/bin/controls/CookiePanel',

        Binds: [
            'loadData',
            'openSearch',
            'openClear',
            '$onCreate',
            '$onInject',
            '$onResize',
            'deleteRedirect',
            'openEditDialog',
            'openAddDialog',
            'editRedirect',
            'getSelectedProjectData',
            'deleteCookie'
        ],

        $ProjectSelect: null,

        initialize: function (options) {
            this.setAttributes({
                icon : 'fa fa-circle',
                title: QUILocale.get(lg, 'panel.title')
            });

            this.parent(options);

            this.$Grid = null;

            this.addEvents({
                onCreate: this.$onCreate,
                onResize: this.$onResize,
                onInject: this.$onInject
            });
        },

        /**
         * event : on create
         */
        $onCreate: function () {
            var self = this;

            this.addButton({
                name     : 'cookie-add',
                text     : QUILocale.get(lg, 'panel.button.add'),
                textimage: 'fa fa-plus',
                disabled : false,
                events   : {
                    onClick: this.openAddDialog
                }
            });

            this.addButton(new QUIButtonSeparator());

            this.addButton({
                name     : 'cookie-edit',
                text     : QUILocale.get(lg, 'panel.button.edit'),
                textimage: 'fa fa-pencil',
                disabled : true,
                events   : {
                    onClick: this.openEditDialog
                }
            });

            this.addButton({
                name     : 'cookie-delete',
                text     : QUILocale.get(lg, 'panel.button.delete'),
                textimage: 'fa fa-trash',
                disabled : true,
                events   : {
                    onClick: this.deleteCookie
                }
            });

            this.addButton(new QUIButtonSeparator());

            // Grid
            var Container = new Element('div').inject(
                this.getContent()
            );

            this.$Grid = new Grid(Container, {
                columnModel: [
                    {
                        dataIndex: 'id',
                        dataType : 'integer',
                        hidden   : true
                    }, {
                        header   : QUILocale.get(lg, 'cookie.name'),
                        dataIndex: 'name',
                        dataType : 'string',
                        width    : 150
                    }, {
                        header   : QUILocale.get(lg, 'cookie.origin'),
                        dataIndex: 'origin',
                        dataType : 'string',
                        width    : 150
                    }, {
                        header   : QUILocale.get(lg, 'cookie.purpose'),
                        dataIndex: 'purpose',
                        dataType : 'string',
                        width    : 300
                    }, {
                        header   : QUILocale.get(lg, 'cookie.lifetime'),
                        dataIndex: 'lifetime',
                        dataType : 'string',
                        width    : 100
                    }, {
                        header   : QUILocale.get(lg, 'cookie.category'),
                        dataIndex: 'category',
                        dataType : 'string',
                        width    : 100
                    }],
                perPage    : 20,
                sortOn     : 'name',
                sortBy     : 'ASC',
                onrefresh  : self.loadData,
                pagination : true
            });

            this.$Grid.addEvents({
                onClick: function () {
                    if (self.$Grid.getSelectedIndices().length === 1) {
                        self.getButtons('cookie-edit').enable();
                        self.getButtons('cookie-delete').enable();
                    } else {
                        self.getButtons('cookie-edit').disable();
                        self.getButtons('cookie-delete').disable();
                    }
                },

                onDblClick: self.openEditDialog
            });
        },


        /**
         * event : on inject
         */
        $onInject: function () {
            var self = this;

            require(['controls/projects/Select'], function (ProjectSelect) {
                self.$ProjectSelect = new ProjectSelect({
                    emptyselect: false,
                    langSelect : false,
                    events     : {
                        onChange: self.loadData
                    }
                });

                self.addButton(self.$ProjectSelect);
            });
        },


        /**
         * event : on resize
         */
        $onResize: function () {
            if (!this.$Grid) {
                return;
            }

            var Content = this.getContent();

            if (!Content) {
                return;
            }

            var size = Content.getSize();

            this.$Grid.setHeight(size.y - 40);

            this.$Grid.setWidth(size.x - 40);
        },

        /**
         * Load the grid data for the currently selected calendar
         */
        loadData: function () {
            if (!this.$Grid) {
                return;
            }

            this.Loader.show();

            var self = this;

            var selectedProjectName = this.$ProjectSelect.getValue();

            CookieManager.getCookiesForGrid(
                selectedProjectName,
                this.$Grid.getAttribute('page'),
                this.$Grid.getAttribute('perPage'),
                this.$Grid.getAttribute('sortOn'),
                this.$Grid.getAttribute('sortBy')
            ).then(function (result) {
                self.$Grid.setData({
                    data : result.data,
                    page : result.page,
                    total: result.total
                });
            }).catch(function (error) {
                console.error(error);
            }).finally(function () {
                self.Loader.hide();
            });
        },

        /**
         * Opens the add-redirect-dialog-popup
         */
        openEditDialog: function () {
            var self = this,
                data = this.$Grid.getSelectedData()[0];

            new CookieEditDialog({
                title : QUILocale.get(lg, 'dialog.edit.title'),
                data  : data,
                events: {
                    'onSubmit': function (Dialog) {
                        if (!Dialog.reportValidity()) {
                            return;
                        }

                        var data = Dialog.getValues();

                        Dialog.close();

                        CookieManager.editCookie(data, self.$ProjectSelect.getValue()).then(self.loadData);
                    }
                }
            }).open();
        },

        openAddDialog: function () {
            var self = this;

            new CookieEditDialog({
                title : QUILocale.get(lg, 'dialog.add.title'),
                events: {
                    'onSubmit': function (Dialog) {
                        if (!Dialog.reportValidity()) {
                            return;
                        }

                        var data = Dialog.getValues();

                        Dialog.close();

                        CookieManager.editCookie(data, self.$ProjectSelect.getValue()).then(self.loadData);
                    }
                }
            }).open();
        },


        deleteCookie: function () {
            var selectedData = this.$Grid.getSelectedData()[0];

            CookieManager.deleteCookie(selectedData.id, this.$ProjectSelect.getValue()).then(this.loadData);
        }
    });
});
