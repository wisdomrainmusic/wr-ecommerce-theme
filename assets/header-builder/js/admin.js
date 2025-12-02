/* -------------------------------------------------
 * WR Header Builder – Admin UI (Step 1)
 * Row + Column scaffold, JSON binding only
 * ------------------------------------------------- */

document.addEventListener('DOMContentLoaded', function () {

    const appEl   = document.getElementById('wr-header-builder-app');
    const inputEl = document.getElementById('wr_header_json');

    if (!appEl || !inputEl) {
        return;
    }

    console.log('Header Builder Admin Loaded');

    // ---------------------------------------------
    // Layout state
    // ---------------------------------------------
    let layout = [];

    function loadInitialLayout() {
        let json = appEl.dataset.json || '[]';

        try {
            const parsed = JSON.parse(json);
            if (Array.isArray(parsed)) {
                layout = parsed;
            } else {
                layout = [];
            }
        } catch (e) {
            console.warn('WR Header Builder: JSON parse error, resetting layout.', e);
            layout = [];
        }

        // Eğer boşsa en az 1 satırla başlayalım (isteğe bağlı)
        if (layout.length === 0) {
            addRow();
        } else {
            render();
            syncHiddenInput();
        }
    }

    function syncHiddenInput() {
        inputEl.value = JSON.stringify(layout);
    }

    // ---------------------------------------------
    // Row helpers
    // ---------------------------------------------
    function createRowObject() {
        const id = 'row_' + Date.now() + '_' + Math.floor(Math.random() * 9999);

        return {
            id: id,
            left: [],
            center: [],
            right: []
        };
    }

    function addRow() {
        layout.push(createRowObject());
        render();
        syncHiddenInput();
    }

    function deleteRow(rowId) {
        layout = layout.filter(row => row.id !== rowId);
        if (layout.length === 0) {
            layout.push(createRowObject());
        }
        render();
        syncHiddenInput();
    }

    // ---------------------------------------------
    // Render UI
    // ---------------------------------------------
    function render() {

        appEl.innerHTML = '';

        const wrapper = document.createElement('div');

        // Header
        const header = document.createElement('div');
        header.className = 'wr-hb-header';

        const title = document.createElement('h3');
        title.className = 'wr-hb-title';
        title.textContent = 'Header Layout Rows';

        const actions = document.createElement('div');
        actions.className = 'wr-hb-actions';

        const addRowBtn = document.createElement('button');
        addRowBtn.type = 'button';
        addRowBtn.className = 'wr-hb-btn';
        addRowBtn.textContent = '+ Add Row';
        addRowBtn.addEventListener('click', addRow);

        actions.appendChild(addRowBtn);

        header.appendChild(title);
        header.appendChild(actions);

        wrapper.appendChild(header);

        // Rows container
        const rowsContainer = document.createElement('div');
        rowsContainer.className = 'wr-hb-rows';

        layout.forEach((row, index) => {

            const rowEl = document.createElement('div');
            rowEl.className = 'wr-hb-row';
            rowEl.dataset.rowId = row.id;

            // Row header
            const rowHeader = document.createElement('div');
            rowHeader.className = 'wr-hb-row-header';

            const rowTitle = document.createElement('div');
            rowTitle.className = 'wr-hb-row-title';
            rowTitle.textContent = 'Row ' + (index + 1);

            const rowActions = document.createElement('div');
            rowActions.className = 'wr-hb-row-actions';

            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'wr-hb-row-btn';
            deleteBtn.textContent = 'Remove';
            deleteBtn.addEventListener('click', function () {
                deleteRow(row.id);
            });

            rowActions.appendChild(deleteBtn);

            rowHeader.appendChild(rowTitle);
            rowHeader.appendChild(rowActions);

            rowEl.appendChild(rowHeader);

            // Row grid (Left / Center / Right)
            const grid = document.createElement('div');
            grid.className = 'wr-hb-row-grid';

            ['left', 'center', 'right'].forEach(area => {
                const col = document.createElement('div');
                col.className = 'wr-hb-col wr-hb-col-' + area;

                const label = document.createElement('span');
                label.className = 'wr-hb-col-label';
                label.textContent = area.toUpperCase();

                col.appendChild(label);
                grid.appendChild(col);
            });

            rowEl.appendChild(grid);

            // Alt bilgi
            const helper = document.createElement('div');
            helper.className = 'wr-hb-empty-helper';
            helper.textContent = 'Widgets will be added here in the next step.';

            rowEl.appendChild(helper);

            rowsContainer.appendChild(rowEl);
        });

        wrapper.appendChild(rowsContainer);

        appEl.appendChild(wrapper);
    }

    // -----------------------------------------------------------
    // Widget Panel (Step 2)
    // -----------------------------------------------------------
    function buildWidgetPanel() {

        const panel = document.createElement('div');
        panel.className = 'wr-hb-widgets-panel';

        const title = document.createElement('h3');
        title.className = 'wr-hb-widgets-title';
        title.textContent = 'Widgets';

        const list = document.createElement('div');
        list.className = 'wr-hb-widget-list';

        const widgets = [
            { type: 'logo', label: 'Logo' },
            { type: 'menu', label: 'Menu' },
            { type: 'search', label: 'Search' },
            { type: 'cart', label: 'Cart' },
            { type: 'account', label: 'Account' },
            { type: 'button', label: 'Button' },
            { type: 'html', label: 'HTML Block' },
            { type: 'spacer', label: 'Spacer' }
        ];

        widgets.forEach(w => {
            const item = document.createElement('div');
            item.className = 'wr-hb-widget-item';
            item.textContent = w.label;
            item.dataset.widget = w.type;

            // ileride Sortable.drag handle olarak kullanılacak
            item.setAttribute('draggable', 'true');

            list.appendChild(item);
        });

        panel.appendChild(title);
        panel.appendChild(list);

        return panel;
    }

    // Render fonksiyonuna panel ekleme
    const originalRender = render;
    render = function () {
        appEl.innerHTML = '';

        // Ana layout container (2 kolon: builder + panel)
        const layoutWrapper = document.createElement('div');
        layoutWrapper.className = 'wr-hb-layout';

        // Sol taraf: Orijinal builder UI
        const builderWrapper = document.createElement('div');
        builderWrapper.className = 'wr-hb-builder-container';

        originalRender.call(null);

        // builder içeriğini appEl'den al
        builderWrapper.append(...appEl.childNodes);

        // yeni layout içine koy
        appEl.innerHTML = '';
        layoutWrapper.appendChild(builderWrapper);

        // sağ taraf: widget panel
        layoutWrapper.appendChild(buildWidgetPanel());

        appEl.appendChild(layoutWrapper);
    };

    // Init
    loadInitialLayout();
});

// -----------------------------------------------------------
// DRAG & DROP (Step 3)
// -----------------------------------------------------------

function initDragDrop() {

    // 1) Widget panelinden sürükleme
    document.querySelectorAll('.wr-hb-widget-item').forEach(item => {

        item.addEventListener('dragstart', function (e) {
            e.dataTransfer.effectAllowed = "copy";
            e.dataTransfer.setData('widget-type', item.dataset.widget);

            // Ghost görseli
            const clone = item.cloneNode(true);
            clone.style.opacity = '0.5';
            document.body.appendChild(clone);
            e.dataTransfer.setDragImage(clone, 50, 20);

            setTimeout(() => {
                document.body.removeChild(clone);
            }, 10);
        });

    });

    // 2) Her kolon Sortable droppable olacak
    document.querySelectorAll('.wr-hb-col').forEach(col => {

        const rowId = col.closest('.wr-hb-row').dataset.rowId;
        const area  = col.classList.contains('wr-hb-col-left') ? 'left' :
                      col.classList.contains('wr-hb-col-center') ? 'center' :
                      'right';

        // Sortable instance
        new Sortable(col, {
            group: {
                name: 'wr-hb',
                pull: false,
                put: true
            },
            animation: 150,
            ghostClass: 'wr-hb-sort-ghost',

            onAdd: function (evt) {
                const widgetType = evt.item.dataset.widget;

                // Remove temporary element & replace with widget card
                evt.item.remove();

                addWidgetToColumn(rowId, area, widgetType);
                render();
                syncHiddenInput();
                initDragDrop();
            }
        });
    });
}


// -----------------------------------------------------------
// Widget Kartı oluşturma
// -----------------------------------------------------------
function createWidgetCard(widget) {
    const el = document.createElement('div');
    el.className = 'wr-hb-widget-card';
    el.dataset.widget = widget.type;

    const label = document.createElement('span');
    label.className = 'wr-hb-widget-card-label';
    label.textContent = widget.type.toUpperCase();

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'wr-hb-widget-card-remove';
    removeBtn.textContent = '×';

    removeBtn.addEventListener('click', function () {
        removeWidgetFromLayout(widget.__rowId, widget.__area, widget.__index);
        render();
        syncHiddenInput();
        initDragDrop();
    });

    el.appendChild(label);
    el.appendChild(removeBtn);

    return el;
}


// -----------------------------------------------------------
// JSON: Widget ekleme
// -----------------------------------------------------------
function addWidgetToColumn(rowId, area, type) {
    layout = layout.map(row => {
        if (row.id === rowId) {
            row[area].push({
                type: type,
                settings: {}
            });
        }
        return row;
    });
}


// -----------------------------------------------------------
// JSON: Widget silme
// -----------------------------------------------------------
function removeWidgetFromLayout(rowId, area, index) {
    layout = layout.map(row => {
        if (row.id === rowId) {
            row[area].splice(index, 1);
        }
        return row;
    });
}


// -----------------------------------------------------------
// RENDER override: widget kartlarını kolonlara yerleştir
// -----------------------------------------------------------
const oldRender = render;

render = function () {

    oldRender();

    // widget kartlarını kolonlara ekleyelim
    layout.forEach(row => {

        const rowEl = document.querySelector(`.wr-hb-row[data-row-id="${row.id}"]`);
        if (!rowEl) return;

        ['left', 'center', 'right'].forEach(area => {

            const colEl = rowEl.querySelector(`.wr-hb-col-${area}`);
            if (!colEl) return;

            // etiketi kaldırıyoruz
            colEl.innerHTML = '';

            row[area].forEach((widget, index) => {
                widget.__rowId = row.id;
                widget.__area  = area;
                widget.__index = index;

                const card = createWidgetCard(widget);
                colEl.appendChild(card);
            });
        });

    });

    initDragDrop();
};

