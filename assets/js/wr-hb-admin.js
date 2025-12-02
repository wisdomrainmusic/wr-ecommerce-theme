(function ($) {
    const WRHB = {
        state: {
            rows: [],
        },

        init() {
            this.cache();

            if (!this.$app.length) {
                return;
            }

            this.bind();
            this.initWidgetPool();
            this.initExistingLayout();
        },

        cache() {
            this.$app = $('#wr-hb-app');
            this.$rows = $('#wr-hb-rows');
            this.$addRow = $('#wr-hb-add-row');
            this.$save = $('#wr-hb-save-layout');
            this.$reset = $('#wr-hb-reset-layout');
            this.$widgetList = $('#wr-hb-widget-list');

            const data = window.wrHbAdminData || {};

            this.layoutId = this.$app.data('layout-id') || data.layoutId || 'layout-desktop';
            this.nonce = this.$app.data('nonce') || data.nonce || '';
            this.initialLayout = this.$app.data('layout') || data.layout || {};
            this.ajaxUrl = data.ajaxUrl || '';
        },

        bind() {
            this.$addRow.on('click', () => this.addRow());
            this.$save.on('click', () => this.saveLayout());
            this.$reset.on('click', () => this.initExistingLayout());
        },

        uid(prefix) {
            return `${prefix}-${Math.random().toString(16).slice(2, 10)}`;
        },

        initWidgetPool() {
            const pool = document.getElementById('wr-hb-widget-list');

            if (!pool || pool.dataset.sortableBound || typeof Sortable === 'undefined') {
                return;
            }

            pool.dataset.sortableBound = '1';

            Sortable.create(pool, {
                group: { name: 'wr-hb', pull: 'clone', put: false },
                sort: false,
                animation: 150,
            });
        },

        createWidgetChip(type, id, settings = {}) {
            const label = this.$widgetList
                .find(`[data-widget-type="${type}"] .wr-hb-widget-card__label`)
                .text() || type;

            const $chip = $('<div class="wr-hb-widget-chip" />')
                .attr('data-widget-type', type)
                .attr('data-widget-id', id || this.uid('wg'))
                .data('settings', settings);

            const $label = $('<span class="wr-hb-widget-chip__label" />').text(label);
            const $remove = $('<button type="button" class="button-link-delete wr-hb-widget-remove" aria-label="Remove widget">&times;</button>');

            $remove.on('click', () => {
                $chip.remove();
                this.serializeState();
            });

            $chip.append($label, $remove);

            return $chip;
        },

        registerDropzone(el) {
            if (!el || el.dataset.sortableBound || typeof Sortable === 'undefined') {
                return;
            }

            el.dataset.sortableBound = '1';

            Sortable.create(el, {
                group: { name: 'wr-hb', pull: false, put: true },
                animation: 150,
                onAdd: (evt) => {
                    const fromWidgetPool = evt.from && evt.from.id === 'wr-hb-widget-list';
                    const $item = $(evt.item);

                    if (fromWidgetPool) {
                        const type = $item.data('widget-type');
                        const defaults = $item.data('widget-default-settings');
                        let parsedDefaults = defaults || {};

                        if (typeof defaults === 'string' && defaults.length) {
                            try {
                                parsedDefaults = JSON.parse(defaults);
                            } catch (e) {
                                parsedDefaults = {};
                            }
                        }

                        const $chip = this.createWidgetChip(type, this.uid('wg'), parsedDefaults);
                        $item.replaceWith($chip[0]);
                    }

                    this.serializeState();
                },
                onUpdate: () => {
                    this.serializeState();
                },
            });
        },

        buildDropzone(rowId, column = {}) {
            const columnId = column.id || this.uid('col');
            const width = column.width || '100%';
            const $dropzone = $('<div class="wr-hb-dropzone" />')
                .attr('data-row-id', rowId)
                .attr('data-column-id', columnId)
                .attr('data-width', width);

            (column.widgets || [])
                .sort((a, b) => (a.order || 0) - (b.order || 0))
                .forEach((widget) => {
                    const widgetId = widget.id || this.uid('wg');
                    $dropzone.append(this.createWidgetChip(widget.type, widgetId, widget.settings || {}));
                });

            this.registerDropzone($dropzone[0]);

            return $dropzone;
        },

        addRow() {
            const rowId = this.uid('row');
            const $row = $('<div class="wr-hb-row" />').attr('data-row-id', rowId);
            const $columns = $('<div class="wr-hb-row-columns" />');
            $columns.append(this.buildDropzone(rowId));
            $row.append($columns);
            this.$rows.append($row);
            this.serializeState();
            return $row;
        },

        hydrateFromData(data) {
            const rows = (data && Array.isArray(data.rows)) ? data.rows : [];

            if (!rows.length) {
                this.addRow();
                return;
            }

            rows
                .sort((a, b) => (a.order || 0) - (b.order || 0))
                .forEach((row) => {
                    const rowId = row.id || this.uid('row');
                    const $row = $('<div class="wr-hb-row" />').attr('data-row-id', rowId);
                    const $columns = $('<div class="wr-hb-row-columns" />');

                    (row.columns || [])
                        .sort((a, b) => (a.order || 0) - (b.order || 0))
                        .forEach((column) => {
                            $columns.append(this.buildDropzone(rowId, column));
                        });

                    if (!$columns.children().length) {
                        $columns.append(this.buildDropzone(rowId));
                    }

                    $row.append($columns);
                    this.$rows.append($row);
                });

            this.serializeState();
        },

        initExistingLayout() {
            this.state.rows = [];
            this.$rows.empty();
            this.hydrateFromData(this.initialLayout);
        },

        serializeState() {
            const layoutMeta = this.initialLayout || {};
            const rows = [];

            this.$rows.find('.wr-hb-row').each((rIndex, rowEl) => {
                const $row = $(rowEl);
                const rowId = $row.data('row-id') || this.uid('row');
                const columns = [];

                $row.find('.wr-hb-dropzone').each((cIndex, colEl) => {
                    const $col = $(colEl);
                    const widgets = [];

                    $col.find('.wr-hb-widget-chip').each((wIndex, chipEl) => {
                        const $chip = $(chipEl);
                        widgets.push({
                            id: $chip.data('widget-id') || this.uid('wg'),
                            type: $chip.data('widget-type'),
                            order: wIndex + 1,
                            settings: $chip.data('settings') || {},
                        });
                    });

                    columns.push({
                        id: $col.data('column-id') || this.uid('col'),
                        order: cIndex + 1,
                        width: $col.data('width') || '100%',
                        settings: {},
                        widgets,
                    });
                });

                rows.push({
                    id: rowId,
                    order: rIndex + 1,
                    settings: {},
                    columns,
                });
            });

            this.state.rows = rows;

            return {
                id: this.layoutId || layoutMeta.id || 'layout-desktop',
                name: layoutMeta.name || 'Header Layout',
                device: layoutMeta.device || 'desktop',
                rows,
            };
        },

        saveLayout() {
            const payload = this.serializeState();

            return wp.ajax
                .post('wr_hb_save_layout', {
                    nonce: this.nonce,
                    layout_id: this.layoutId,
                    data: JSON.stringify(payload),
                })
                .done((response) => {
                    if (response && response.layout) {
                        this.initialLayout = response.layout;
                    }

                    if (wp.data && wp.data.dispatch) {
                        const successMessage = response && response.message ? response.message : 'Saved';

                        wp.data.dispatch('core/notices').createNotice('success', successMessage, {
                            isDismissible: true,
                        });
                    }

                    console.log('WR HB save success', response);
                })
                .fail((error) => {
                    const errorMessage = (error && error.responseJSON && error.responseJSON.data && error.responseJSON.data.message)
                        || (error && error.message)
                        || 'Error saving layout';

                    console.log('WR HB save error', error);
                    window.alert(errorMessage);

                    if (wp.data && wp.data.dispatch) {
                        wp.data.dispatch('core/notices').createNotice('error', errorMessage, {
                            isDismissible: true,
                        });
                    }
                });
        },
    };

    $(function () {
        WRHB.init();
    });
})(jQuery);
