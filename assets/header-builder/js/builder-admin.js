(function ($) {
    if (typeof window.wrHbData === 'undefined') {
        return;
    }

    const state = {
        device: 'desktop',
        layouts: window.wrHbData.layouts || {},
    };

    const widthOptions = ['25%', '33%', '50%', '66%', '75%', '100%'];

    const uid = (prefix) => `${prefix}-${Math.random().toString(16).slice(2, 10)}`;

    function ensureLayout(device) {
        if (!state.layouts[device] || typeof state.layouts[device] !== 'object') {
            state.layouts[device] = {
                id: `layout-${device}`,
                name: `${device} header`,
                device,
                rows: [],
            };
        }

        if (!Array.isArray(state.layouts[device].rows)) {
            state.layouts[device].rows = [];
        }

        if (!state.layouts[device].rows.length) {
            state.layouts[device].rows.push(createDefaultRow(device));
        }

        return state.layouts[device];
    }

    function createDefaultRow(device) {
        return {
            id: uid(`row-${device}`),
            order: 1,
            settings: {},
            columns: [
                { id: uid('col'), order: 1, width: '25%', settings: {}, widgets: [{ id: uid('wg'), type: 'logo', order: 1, settings: {} }] },
                { id: uid('col'), order: 2, width: '50%', settings: {}, widgets: [{ id: uid('wg'), type: 'menu', order: 1, settings: {} }] },
                { id: uid('col'), order: 3, width: '25%', settings: {}, widgets: [{ id: uid('wg'), type: 'cart', order: 1, settings: {} }] },
            ],
        };
    }

    function render() {
        const layout = ensureLayout(state.device);
        const $canvas = $('#wr-hb-canvas');
        $canvas.empty();

        layout.rows
            .sort((a, b) => (a.order || 0) - (b.order || 0))
            .forEach((row) => {
                const $row = $('<div class="wr-hb-row" />').attr('data-id', row.id);
                const $header = $('<div class="wr-hb-row-header" />');
                $header.append(`<strong>${row.name || 'Row'}</strong>`);

                const $rowActions = $('<div class="wr-hb-row-actions" />');
                const $addCol = $('<button type="button" class="button">+ Column</button>');
                const $removeRow = $('<button type="button" class="button-link-delete">' + wrHbData.i18n?.removeRow || 'Remove' + '</button>');

                $addCol.on('click', () => addColumn($row));
                $removeRow.on('click', () => {
                    $row.remove();
                });

                $rowActions.append($addCol, $removeRow);
                $header.append($rowActions);

                const $columns = $('<div class="wr-hb-columns" />');
                (row.columns || [])
                    .sort((a, b) => (a.order || 0) - (b.order || 0))
                    .forEach((column) => {
                        $columns.append(createColumn(column));
                    });

                $row.append($header, $columns);
                $canvas.append($row);
            });

        bindWidgetLibrary();
    }

    function createColumn(column) {
        const $column = $('<div class="wr-hb-column" />').attr('data-id', column.id || uid('col'));
        const $settings = $('<div class="wr-hb-column-settings" />');
        const $width = $('<select class="wr-hb-width" />');

        widthOptions.forEach((value) => {
            const $opt = $('<option />').attr('value', value).text(value);
            if (column.width === value) {
                $opt.attr('selected', 'selected');
            }
            $width.append($opt);
        });

        const $remove = $('<button type="button" class="button-link-delete">&times;</button>');
        $remove.on('click', () => $column.remove());

        $settings.append($('<span>Width</span>'), $width, $remove);

        const $stack = $('<div class="wr-hb-widget-stack" />');
        (column.widgets || [])
            .sort((a, b) => (a.order || 0) - (b.order || 0))
            .forEach((widget) => {
                $stack.append(createWidgetChip(widget.type, widget.id));
            });

        $column.append($settings, $stack);
        makeSortable($stack[0]);
        return $column;
    }

    function createWidgetChip(type, id) {
        const label = $(`#wr-hb-widget-library .wr-hb-widget[data-type="${type}"] .wr-hb-widget-label`).text() || type;
        const $chip = $('<div class="wr-hb-widget-chip" />')
            .attr('data-type', type)
            .attr('data-id', id || uid('wg'))
            .append(`<span>${label}</span>`);

        const $actions = $('<div class="wr-hb-chip-actions" />');
        const $remove = $('<button type="button" class="button-link-delete">&times;</button>');
        $remove.on('click', () => $chip.remove());
        $actions.append($remove);

        $chip.append($actions);
        return $chip;
    }

    function bindWidgetLibrary() {
        const library = document.getElementById('wr-hb-widget-library');
        if (library && !library.dataset.sortableBound) {
            library.dataset.sortableBound = '1';
            Sortable.create(library, {
                group: { name: 'wr-hb-widgets', pull: 'clone', put: false },
                sort: false,
                animation: 150,
            });
        }
    }

    function makeSortable(el) {
        Sortable.create(el, {
            group: { name: 'wr-hb-widgets', pull: true, put: true },
            animation: 150,
            onAdd: function (evt) {
                const $item = $(evt.item);
                const type = $item.data('type');
                const $chip = createWidgetChip(type);
                $item.replaceWith($chip);
            },
        });
    }

    function addRow() {
        const layout = ensureLayout(state.device);
        layout.rows.push(createDefaultRow(state.device));
        render();
    }

    function addColumn($row) {
        const $columns = $row.find('.wr-hb-columns').first();
        $columns.append(createColumn({ id: uid('col'), order: $columns.children().length + 1, width: '50%', widgets: [] }));
    }

    function collectLayout() {
        const rows = [];
        $('#wr-hb-canvas .wr-hb-row').each(function (rIndex) {
            const $row = $(this);
            const row = {
                id: $row.data('id') || uid('row'),
                order: rIndex + 1,
                settings: {},
                columns: [],
            };

            $row.find('.wr-hb-column').each(function (cIndex) {
                const $col = $(this);
                const widgets = [];
                $col.find('.wr-hb-widget-chip').each(function (wIndex) {
                    const $widget = $(this);
                    widgets.push({
                        id: $widget.data('id') || uid('wg'),
                        type: $widget.data('type'),
                        order: wIndex + 1,
                        settings: {},
                    });
                });

                row.columns.push({
                    id: $col.data('id') || uid('col'),
                    order: cIndex + 1,
                    width: $col.find('.wr-hb-width').val() || '33%',
                    settings: {},
                    widgets,
                });
            });

            rows.push(row);
        });

        state.layouts[state.device] = {
            id: `layout-${state.device}`,
            name: `${state.device} header`,
            device: state.device,
            rows,
        };
    }

    function saveLayout() {
        collectLayout();

        $.post(
            wrHbData.ajaxUrl,
            {
                action: 'wr_hb_save_layout',
                nonce: wrHbData.nonce,
                layouts: JSON.stringify(state.layouts),
            }
        )
            .done(() => {
                wp.data && wp.data.dispatch('core/notices')?.createNotice?.('success', wrHbData.i18n?.saveSuccess || 'Saved', {
                    isDismissible: true,
                });
            })
            .fail(() => {
                wp.data && wp.data.dispatch('core/notices')?.createNotice?.('error', wrHbData.i18n?.saveError || 'Error', {
                    isDismissible: true,
                });
            });
    }

    function bindEvents() {
        $('#wr-hb-add-row').on('click', addRow);
        $('#wr-hb-save-layout').on('click', saveLayout);

        $('.wr-hb-device-switch .button').on('click', function () {
            $('.wr-hb-device-switch .button').removeClass('active');
            $(this).addClass('active');
            state.device = $(this).data('device');
            render();
        });
    }

    $(document).ready(function () {
        bindEvents();
        render();
    });
})(jQuery);
