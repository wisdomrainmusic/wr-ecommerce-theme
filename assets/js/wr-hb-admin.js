/* global jQuery, Sortable, wrHbData, wp */

(function ($) {
    'use strict';

    // Global sanity check – drag çalışmıyorsa ilk bakılacak yer burası.
    if (typeof Sortable === 'undefined') {
        console.error('[WR HB] SortableJS not found. Drag & drop disabled.');
        return;
    }


    const WRHB = {
        init() {
            this.cache();

            if (!this.$root.length) {
                return;
            }

            this.layoutId = this.$root.data('layout-id') || (wrHbData && wrHbData.layoutId) || 'default-header';
            this.nonce    = this.$root.data('nonce') || (wrHbData && wrHbData.nonce) || '';
            this.ajaxUrl  = (wrHbData && wrHbData.ajaxUrl) || (window.ajaxurl || '');

            this.layout   = (wrHbData && wrHbData.layout && wrHbData.layout.rows) ? wrHbData.layout : this.createEmptyLayout();

            this.buildInitialLayout();
            this.initWidgetPool();
            this.bind();

            console.log('[WR HB] Admin initialized', this.layout);
        },

        cache() {
            this.$root        = $('#wr-hb-app');
            this.$rowsWrapper = $('#wr-hb-rows');
            this.$widgetList  = $('#wr-hb-widget-list');
            this.$addRowBtn   = $('.wr-hb-add-row');
            this.$saveBtn     = $('#wr-hb-save-layout');
            this.$resetBtn    = $('#wr-hb-reset-layout');
        },

        createEmptyLayout() {
            return {
                id: 'default-header',
                name: 'Default Header',
                device: 'desktop',
                rows: []
            };
        },

        uuid(prefix) {
            prefix = prefix || 'id';
            return prefix + '_' + Math.random().toString(36).substr(2, 9);
        },

        bind() {
            const self = this;

            this.$addRowBtn.on('click', function (e) {
                e.preventDefault();
                self.addRow();
            });

            this.$rowsWrapper.on('click', '.wr-hb-remove-row', function (e) {
                e.preventDefault();
                $(this).closest('.wr-hb-row').remove();
                self.syncStateFromDOM();
            });

            this.$rowsWrapper.on('click', '.wr-hb-remove-widget', function (e) {
                e.preventDefault();
                $(this).closest('.wr-hb-widget').remove();
                self.syncStateFromDOM();
            });

            this.$saveBtn.on('click', function (e) {
                e.preventDefault();
                self.saveLayout();
            });

            this.$resetBtn.on('click', function (e) {
                e.preventDefault();
                if (window.confirm('Reset header layout to empty?')) {
                    self.layout.rows = [];
                    self.$rowsWrapper.empty();
                    self.addRow(); // En az bir satır oluştur.
                    self.syncStateFromDOM();
                }
            });
        },

        buildInitialLayout() {
            const rows = this.layout.rows || [];

            if (!rows.length) {
                this.addRow();
                return;
            }

            for (let i = 0; i < rows.length; i++) {
                this.renderRow(rows[i]);
            }
        },

        initWidgetPool() {
            if (!this.$widgetList.length || typeof Sortable === 'undefined') {
                return;
            }

            Sortable.create(this.$widgetList[0], {
                group: {
                    name: 'wr-hb',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                animation: 150
            });

            this.$widgetList.find('.wr-hb-widget-card').css('cursor', 'move');
        },

        addRow(rowData) {
            const row = rowData || {
                id: this.uuid('row'),
                order: (this.layout.rows.length || 0) + 1,
                settings: {},
                columns: [
                    {
                        id: this.uuid('col'),
                        order: 1,
                        width: '1/1',
                        settings: {},
                        widgets: []
                    }
                ]
            };

            this.layout.rows.push(row);
            this.renderRow(row);
            this.syncStateFromDOM();
        },

        renderRow(row) {
            const rowId = row.id || this.uuid('row');

            const $row = $(`
                <div class="wr-hb-row" data-row-id="${rowId}">
                    <div class="wr-hb-row-inner">
                    </div>
                    <button type="button" class="button-link wr-hb-remove-row">×</button>
                </div>
            `);

            const $rowInner = $row.find('.wr-hb-row-inner');

            const columns = row.columns && row.columns.length ? row.columns : [
                {
                    id: this.uuid('col'),
                    order: 1,
                    width: '1/1',
                    settings: {},
                    widgets: []
                }
            ];

            for (let i = 0; i < columns.length; i++) {
                const col = columns[i];
                const colId = col.id || this.uuid('col');

                const $col = $(`
                    <div class="wr-hb-col" data-col-id="${colId}" data-width="${col.width || '1/1'}">
                        <div class="wr-hb-dropzone"></div>
                    </div>
                `);

                const $zone = $col.find('.wr-hb-dropzone').first();

                // Hydrate widgets for this column.
                if (col.widgets && col.widgets.length) {
                    for (let j = 0; j < col.widgets.length; j++) {
                        const widget = col.widgets[j];
                        const $widgetEl = this.createWidgetElement(widget);
                        $zone.append($widgetEl);
                    }
                }

                $rowInner.append($col);
                this.registerDropzone($zone[0]);
            }

            this.$rowsWrapper.append($row);
        },

        createWidgetElement(widget) {
            const id    = widget.id || this.uuid('w');
            const type  = widget.type || 'unknown';
            const label = (widget.settings && widget.settings.label) || widget.label || type;

            const $el = $(`
                <div class="wr-hb-widget" data-widget-id="${id}" data-widget-type="${type}">
                    <span class="wr-hb-widget-label"></span>
                    <button type="button" class="button-link wr-hb-remove-widget">×</button>
                </div>
            `);

            $el.find('.wr-hb-widget-label').text(label);

            return $el;
        },

        registerDropzone(el) {
            const self = this;
            const $zone = $(el);

            if ($zone.data('wrHbSortableInit')) {
                return;
            }

            if (typeof Sortable === 'undefined') {
                return;
            }

            Sortable.create(el, {
                group: {
                    name: 'wr-hb',
                    pull: false,
                    put: true
                },
                animation: 150,
                ghostClass: 'wr-hb-ghost',
                onAdd(evt) {
                    self.handleDrop(evt);
                },
                onUpdate() {
                    self.syncStateFromDOM();
                }
            });

            $zone.data('wrHbSortableInit', true);
        },

        handleDrop(evt) {
            const $item = $(evt.item);
            const fromPool = $(evt.from).is('#wr-hb-widget-list');

            if (fromPool) {
                const type  = $item.data('widget-type') || $item.data('widgetType');
                const label = $item.data('widget-label') || $item.data('widgetLabel') || $item.text().trim();

                const widget = {
                    id: this.uuid('w'),
                    type: type,
                    settings: { label: label }
                };

                const $newEl = this.createWidgetElement(widget);
                $item.replaceWith($newEl);
            }

            this.syncStateFromDOM();
        },

        syncStateFromDOM() {
            const rows = [];
            const self = this;

            this.$rowsWrapper.find('.wr-hb-row').each(function (rowIndex) {
                const $row = $(this);
                let rowId = $row.data('row-id');
                if (!rowId) {
                    rowId = self.uuid('row');
                    $row.attr('data-row-id', rowId);
                }

                const row = {
                    id: rowId,
                    order: rowIndex + 1,
                    settings: {},
                    columns: []
                };

                $row.find('.wr-hb-col').each(function (colIndex) {
                    const $col = $(this);
                    let colId = $col.data('col-id');
                    if (!colId) {
                        colId = self.uuid('col');
                        $col.attr('data-col-id', colId);
                    }

                    const col = {
                        id: colId,
                        order: colIndex + 1,
                        width: $col.data('width') || '1/1',
                        settings: {},
                        widgets: []
                    };

                    $col.find('.wr-hb-dropzone').first().find('.wr-hb-widget').each(function (widgetIndex) {
                        const $w  = $(this);
                        let wid   = $w.data('widget-id');
                        const type = $w.data('widget-type');
                        const label = $w.find('.wr-hb-widget-label').text().trim();

                        if (!wid) {
                            wid = self.uuid('w');
                            $w.attr('data-widget-id', wid);
                        }

                        col.widgets.push({
                            id: wid,
                            type: type,
                            order: widgetIndex + 1,
                            settings: { label: label }
                        });
                    });

                    row.columns.push(col);
                });

                rows.push(row);
            });

            this.layout.rows = rows;
        },

        saveLayout() {
            this.syncStateFromDOM();

            const payload = {
                action: 'wr_hb_save_layout',
                nonce: this.nonce,
                layout_id: this.layoutId,
                data: JSON.stringify(this.layout)
            };

            const done = function () {
                window.alert('Header layout saved.');
            };

            const fail = function (message) {
                window.alert(message || 'Error while saving header layout.');
            };

            if (window.wp && wp.ajax && typeof wp.ajax.post === 'function') {
                wp.ajax.post('wr_hb_save_layout', payload)
                    .done(done)
                    .fail(function (resp) {
                        fail(resp && resp.message ? resp.message : null);
                    });
            } else {
                $.post(this.ajaxUrl, payload)
                    .done(function (resp) {
                        if (resp && resp.success) {
                            done();
                        } else {
                            fail(resp && resp.data && resp.data.message ? resp.data.message : null);
                        }
                    })
                    .fail(function () {
                        fail();
                    });
            }
        }
    };

    $(function () {
        WRHB.init();
    });

})(jQuery);
