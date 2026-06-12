/*!
 * Панель инструментов.
 * Расширение "Темы оформления".
 * Модуль "Маркетплейс".
 * Copyright 2015 RosGear. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://rosgear.ru/license/
 */

/**
 * @class Rg.be.mp.themes.ButtonCreate
 * @extends Ge.view.grid.button.Button
 * Кнопка "Создать" на панели инструментов сетки.
 * Создание темы.
 */
Ext.define('Rg.be.mp.themes.ButtonCreate', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-themes-button-create',
    selectRecords: true,
    minWidth: 76,
    confirm: false,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                if (Ext.isDefined(selectedRecord[0]))
                    me.setDisabled(selectedRecord[0].data.status != 1);
                else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
        click: function (me, e, eOpts) {
            let row = me.selectorCmp.getStore().getOneSelected();
            Ge.app.widget.load('@backend/marketplace/themes/create/view', {id: row.id});
        }
    }
});


/**
 * @class Rg.be.mp.themes.ButtonInstal
 * @extends Ge.view.grid.button.Button
 * Кнопка "Установить" на панели инструментов сетки.
 * Установка темы.
 */
 Ext.define('Rg.be.mp.themes.ButtonInstall', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-themes-button-install',
    selectRecords: true,
    minWidth: 76,
    confirm: false,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                if (Ext.isDefined(selectedRecord[0]))
                    me.setDisabled(selectedRecord[0].data.status != 0);
                else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
        click: function (me, e, eOpts) {
            let row = me.selectorCmp.getStore().getOneSelected();
            Ge.app.widget.load('@backend/marketplace/themes/install/view', {id: row.id});
        }
    }
});


/**
 * @class Rg.be.mp.themes.ButtonUninstall
 * @extends Ge.view.grid.button.Button
 * Кнопка "Удаление" на панели инструментов сетки.
 * Полностью удаление установленной темы.
 */
 Ext.define('Rg.be.mp.themes.ButtonUninstall', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-themes-button-uninstall',
    selectRecords: true,
    minWidth: 72,
    confirm: true,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                let row = selectedRecord[0];
                // status = 1 (установлен), 2 (ошибка), 0 (не установлен)
                if (Ext.isDefined(row)) {
                    me.setDisabled(row.data.status == 0 || row.data.lockRow == 1);
                } else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
         click: function (me, e, eOpts) {
            let count = me.selectorCmp.getSelectionModel().getCount();
            if (count == 0) { Ext.Msg.warning(me.msgMustSelect); return; }
            Ext.Msg.confirm(
                Ext.Txt.confirmation,
                me.msgConfirm,
                function(btn, text) {
                    if (btn == 'yes') {
                        let row = me.selectorCmp.getStore().getOneSelected();
                        Ge.app.widget.load('@backend/marketplace/themes/uninstall/complete', {id: row.id});                                    
                    }
                },
                this
            );
        }
    }
});


/**
 * @class Rg.be.mp.themes.ButtonUnmount
 * @extends Ge.view.grid.button.Button
 * Кнопка "Демонтаж" на панели инструментов сетки.
 * Удаление установленной темы без удаления его из репозитория.
 */
 Ext.define('Rg.be.mp.themes.ButtonUnmount', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-themes-button-unmount',
    selectRecords: true,
    minWidth: 72,
    confirm: true,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                let row = selectedRecord[0];
                // status = 1 (установлен), 2 (ошибка), 0 (не установлен)
                if (Ext.isDefined(row)) {
                    me.setDisabled(row.data.status == 0 || row.data.lockRow == 1);
                } else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
         click: function (me, e, eOpts) {
            let count = me.selectorCmp.getSelectionModel().getCount();
            if (count == 0) { Ext.Msg.warning(me.msgMustSelect); return; }
            Ext.Msg.confirm(
                Ext.Txt.confirmation,
                me.msgConfirm,
                function(btn, text) {
                    if (btn == 'yes') {
                        let row = me.selectorCmp.getStore().getOneSelected();
                        Ge.app.widget.load('@backend/marketplace/themes/unmount/complete', {id: row.id});                                    
                    }
                },
                this
            );
        }
    }
});


/**
 * @class Rg.be.mp.themes.ButtonUnmount
 * @extends Ge.view.grid.button.Button
 * Кнопка "Удалить" на панели инструментов сетки.
 * Удаление не установленной темы из репозитория.
 */
 Ext.define('Rg.be.mp.themes.ButtonDelete', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-themes-button-delete',
    selectRecords: true,
    minWidth: 72,
    confirm: true,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                let row = selectedRecord[0];
                // status = 1 (установлен), 2 (ошибка), 0 (не установлен)
                if (Ext.isDefined(row)) {
                    me.setDisabled(row.data.status != 0);
                } else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
         click: function (me, e, eOpts) {
            let count = me.selectorCmp.getSelectionModel().getCount();
            if (count == 0) { Ext.Msg.warning(me.msgMustSelect); return; }
            Ext.Msg.confirm(
                Ext.Txt.confirmation,
                me.msgConfirm,
                function(btn, text) {
                    if (btn == 'yes') {
                        let row = me.selectorCmp.getStore().getOneSelected();
                        Ge.app.widget.load('@backend/marketplace/themes/grid/delete', {id: row.id});                                    
                    }
                },
                this
            );
        }
    }
});