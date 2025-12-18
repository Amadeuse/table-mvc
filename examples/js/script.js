document.addEventListener('DOMContentLoaded', function() {
    if (!window.TABLE_CONFIGS) return;

    Object.keys(window.TABLE_CONFIGS).forEach(key => {
        const config = window.TABLE_CONFIGS[key];
        const id = config.table;
        let currentPage = 1;
        let searchTimeout;
        let currentSortField = '';
        let currentSortOrder = 'ASC';

        const searchInput = document.getElementById('mvc_table_search');
        const perPageSelect = document.getElementById('mvc_table_perpage');
        const tableContainer = document.getElementById('mvc_table_container');
        const paginationContainer = document.getElementById('mvc_table_pagination');
        const recordsInfo = document.getElementById('mvc_table_records_info');
        const colsContainer = document.getElementById('mvc_table_cols');

        // init per-page from localStorage
        if (perPageSelect) {
            const perKey = 'mvc_table_perpage';
            try {
                const saved = localStorage.getItem(perKey);
                if (saved) perPageSelect.value = saved;
            } catch (e) {}
        }

        // columns menu
        const columnsMenu = document.getElementById('mvc_table_cols');
        const storageKeyCols = 'mvc_table_columns';
        let visibleColumns = null;
        try {
            const saved = localStorage.getItem(storageKeyCols);
            if (saved) {
                const parsed = JSON.parse(saved);
                if (Array.isArray(parsed)) visibleColumns = parsed;
                else if (parsed && typeof parsed === 'object') {
                    visibleColumns = Object.keys(config.headers).filter(f => !parsed[f]);
                    localStorage.setItem(storageKeyCols, JSON.stringify(visibleColumns));
                }
            }
        } catch (e) {}

        if (columnsMenu && config.headers) {
            columnsMenu.innerHTML = '';
            Object.keys(config.headers).forEach(field => {
                const cid = `mvc_table_col_${field}`;
                const isChecked = visibleColumns === null ? true : (visibleColumns.indexOf(field) !== -1);
                const wrapper = document.createElement('div');
                wrapper.className = 'form-check';
                wrapper.innerHTML = `\n                    <input class="form-check-input" type="checkbox" id="${cid}" data-field="${field}" ${isChecked ? 'checked' : ''}>\n                    <label class="form-check-label" for="${cid}">${config.headers[field] || field}</label>\n                `;
                columnsMenu.appendChild(wrapper);
            });

            columnsMenu.querySelectorAll('input[type=checkbox]').forEach(ch => {
                ch.addEventListener('change', function() {
                    const visible = Array.from(columnsMenu.querySelectorAll('input[type=checkbox]:checked')).map(i => i.getAttribute('data-field'));
                    localStorage.setItem(storageKeyCols, JSON.stringify(visible));
                    applyColumnVisibility();
                });
            });

            columnsMenu.addEventListener('click', function(e) { e.stopPropagation(); });
            document.addEventListener('click', function() { columnsMenu.style.display = 'none'; });
        }

        // open columns menu when button clicked
        const colsBtn = document.querySelector(`#mvc_table_cols`)?.previousElementSibling;
        if (colsBtn) {
            colsBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const shown = columnsMenu.style.display !== 'block';
                document.querySelectorAll('.mvc-dropdown-menu.custom-columns').forEach(el => el.style.display = 'none');
                if (shown) {
                    columnsMenu.classList.add('custom-columns');
                    columnsMenu.style.display = 'block';
                } else columnsMenu.style.display = 'none';
            });
        }

        loadData();

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => { currentPage = 1; loadData(); }, 400);
            });
        }

        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                currentPage = 1;
                try { localStorage.setItem('mvc_table_perpage', perPageSelect.value); } catch(e) {}
                loadData();
            });
        }

        function loadData() {
            const params = new URLSearchParams();
            params.set('table', id);
            params.set('page', currentPage);
            params.set('search', searchInput ? searchInput.value : '');
            params.set('sort_field', currentSortField);
            params.set('sort_dir', currentSortOrder);
            if (perPageSelect) params.set('per_page', perPageSelect.value);

            showSpinner();
            const url = (window.CONTROLLER_URL || 'api/table') + '?' + params.toString();
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    hideSpinner();
                    if (!data.success) {
                        tableContainer.innerHTML = `<div class="mvc-alert mvc-alert-danger">${data.message || 'Error'}</div>`;
                        return;
                    }
                    renderTable(data.data, data.total_records ? data.total_records : 0, data.total_pages);
                })
                .catch(err => { hideSpinner(); tableContainer.innerHTML = `<div class="mvc-alert mvc-alert-danger">${err.message}</div>`; });
        }

        function showSpinner() {
            tableContainer.innerHTML = `<div class="mvc-table-loading"><div class="spinner-border text-secondary" role="status"><span class="sr-only">Loading...</span></div></div>`;
        }
        function hideSpinner() { }

        function applyColumnVisibility() {
            try {
                const saved = localStorage.getItem(storageKeyCols);
                let visible = null;
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (Array.isArray(parsed)) visible = parsed;
                    else if (parsed && typeof parsed === 'object') {
                        visible = Object.keys(config.headers).filter(f => !parsed[f]);
                        localStorage.setItem(storageKeyCols, JSON.stringify(visible));
                    }
                }

                const ths = tableContainer.querySelectorAll('th[data-field]');
                ths.forEach(th => { const f = th.getAttribute('data-field'); const isVisible = visible === null ? true : visible.indexOf(f) !== -1; if (!isVisible) th.classList.add('mvc-hidden-column'); else th.classList.remove('mvc-hidden-column'); });
                const tds = tableContainer.querySelectorAll('td[data-field]');
                tds.forEach(td => { const f = td.getAttribute('data-field'); const isVisible = visible === null ? true : visible.indexOf(f) !== -1; if (!isVisible) td.classList.add('mvc-hidden-column'); else td.classList.remove('mvc-hidden-column'); });
            } catch (e) {}
        }

        function renderTable(rows, totalRecords, totalPages) {
            if (!rows || !rows.length) {
                tableContainer.innerHTML = '<div class="mvc-alert mvc-alert-info">მონაცემები არ მოიძებნა</div>';
                paginationContainer.innerHTML = '';
                if (recordsInfo) recordsInfo.innerText = `Showing 0–0 / 0`;
                return;
            }

            const fields = Object.keys(rows[0]);
            let html = '<table class="mvc-table table-sm table-bordered"><thead><tr>';
            fields.forEach(f => {
                const isSorted = currentSortField === f;
                const isSortable = Array.isArray(config.sortable) && config.sortable.indexOf(f) !== -1;
                const sortClass = isSortable ? 'mvc-sortable' + (isSorted ? (currentSortOrder === 'ASC' ? ' mvc-sort-asc' : ' mvc-sort-desc') : '') : '';
                html += `<th data-field="${f}" class="${sortClass}" ${isSortable ? 'data-sort="'+f+'"' : ''}>${config.headers[f] || f}</th>`;
            });
            html += '</tr></thead><tbody>';
            rows.forEach(r => { html += '<tr>'; fields.forEach(f => { html += `<td data-field="${f}">${r[f]}</td>`; }); html += '</tr>'; });
            html += '</tbody></table>';

            tableContainer.innerHTML = html;
            applyColumnVisibility();

            // attach sort handlers
            if (config.sortable) {
                tableContainer.querySelectorAll('th[data-sort]').forEach(h => h.addEventListener('click', function() {
                    const sf = this.getAttribute('data-sort');
                    if (currentSortField === sf) currentSortOrder = currentSortOrder === 'ASC' ? 'DESC' : 'ASC'; else { currentSortField = sf; currentSortOrder = 'ASC'; }
                    loadData();
                }));
            }

            // pagination
            let phtml = '';
            if (totalPages > 1) {
                phtml += `<li class="mvc-page-item ${currentPage === 1 ? 'mvc-disabled' : ''}"><button class="mvc-page-link" data-page="1">პირველი</button></li>`;
                phtml += `<li class="mvc-page-item ${currentPage === 1 ? 'mvc-disabled' : ''}"><button class="mvc-page-link" data-page="${currentPage-1}">წინა</button></li>`;
                for (let i = Math.max(1, currentPage-2); i <= Math.min(totalPages, currentPage+2); i++) phtml += `<li class="mvc-page-item ${i===currentPage ? 'mvc-active' : ''}"><button class="mvc-page-link" data-page="${i}">${i}</button></li>`;
                phtml += `<li class="mvc-page-item ${currentPage === totalPages ? 'mvc-disabled' : ''}"><button class="mvc-page-link" data-page="${currentPage+1}">შემდეგი</button></li>`;
                phtml += `<li class="mvc-page-item ${currentPage === totalPages ? 'mvc-disabled' : ''}"><button class="mvc-page-link" data-page="${totalPages}">ბოლო</button></li>`;
            }
            paginationContainer.innerHTML = phtml;
            Array.from(paginationContainer.querySelectorAll('button[data-page]')).forEach(b => b.addEventListener('click', function() { const p = parseInt(this.getAttribute('data-page')); if (!isNaN(p) && p !== currentPage) { currentPage = p; loadData(); } }));

            // records info
            if (recordsInfo) {
                const per = perPageSelect ? parseInt(perPageSelect.value,10) || 10 : 10;
                const start = (currentPage - 1) * per + 1;
                const end = Math.min(totalRecords, currentPage * per);
                recordsInfo.innerText = `გამოჩენილია: ${start}–${end} / ${totalRecords}`;
            }
        }
    });
});
