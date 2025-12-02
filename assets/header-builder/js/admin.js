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

    // Init
    loadInitialLayout();
});
