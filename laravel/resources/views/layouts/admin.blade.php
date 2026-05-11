<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ' . config('app.name', 'e-com'))</title>

    {{-- Semantic UI --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.css">

    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #f4f6f8;
            font-family: 'Lato', 'Helvetica Neue', Arial, Helvetica, sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        /* === Global admin top bar === */
        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.5rem;
            min-height: 56px;
            background: #1b1c1d;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
        }
        .admin-topbar .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }
        .admin-topbar .brand i.icon {
            color: #2185d0;
            margin: 0;
        }
        .admin-topbar .brand:hover { color: #e6f4ff; }
        .admin-topbar .topbar-spacer { flex: 1; }
        .admin-topbar .user-area {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #ddd;
            font-size: 0.95rem;
        }
        .admin-topbar .user-area i.icon { margin: 0; }
        .admin-topbar form.logout-form { margin: 0; }
        .admin-topbar form.logout-form button {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.75rem;
            background: transparent;
            border: 0;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.92rem;
        }
        .admin-topbar form.logout-form button:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* === Main content area: full width, responsive padding === */
        main.admin-page {
            flex: 1 0 auto;
            width: 100%;
            box-sizing: border-box;
            padding: 1.5rem 2rem 2.5rem;
        }
        .admin-section {
            background: #fff;
            border: 1px solid #e0e1e2;
            border-radius: 6px;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            box-sizing: border-box;
        }
        .admin-section + .admin-section { margin-top: 1rem; }

        /* === Page header (breadcrumb + title + actions) === */
        .admin-page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .admin-page-header .ui.breadcrumb {
            font-size: 0.92rem;
            color: #777;
        }
        .admin-page-header .ui.breadcrumb a.section { color: #2185d0; }
        .admin-page-header .ui.breadcrumb .divider { margin: 0 0.4rem; }
        .admin-page-header .page-actions {
            display: inline-flex;
            gap: 0.5rem;
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .admin-topbar { padding: 0.55rem 1rem; }
            main.admin-page { padding: 1rem; }
            .admin-section { padding: 1.1rem; }
            .admin-topbar .user-area .user-name { display: none; }
        }
        @media (max-width: 480px) {
            .admin-topbar .brand-text { display: none; }
        }
        /* Toolbar (search + page size + counter) above paginated tables */
        .ui.admin-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1em;
        }
        .ui.admin-toolbar .ui.input {
            flex: 1 1 280px;
            min-width: 220px;
        }
        .ui.admin-toolbar .toolbar-info {
            color: #666;
            font-size: 0.9em;
            margin-left: auto;
        }
        .ui.admin-toolbar .page-size-select {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #555;
            font-size: 0.9em;
        }
        .ui.admin-toolbar .page-size-select select {
            padding: 0.4em 0.6em;
            border: 1px solid #d4d4d5;
            border-radius: 4px;
            background: #fff;
        }
        .admin-pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1em;
        }
        .admin-pagination-container .ui.pagination.menu .item {
            cursor: pointer;
            user-select: none;
        }
        .admin-empty-state {
            margin-top: 1em;
        }
        /* Header count badge */
        .ui.header .count-badge {
            display: inline-block;
            margin-left: 0.4em;
            padding: 0.1em 0.55em;
            background: #e0e1e2;
            color: #555;
            border-radius: 999px;
            font-size: 0.75em;
            vertical-align: middle;
        }
        /* Dashboard cards: subtle lift on hover */
        .ui.cards > .card.admin-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .ui.cards > .card.admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }
    </style>

    @stack('head')
</head>
<body>
    @hasSection('without-chrome')
        @yield('content')
    @else
        @php
            $authUser = \Illuminate\Support\Facades\Auth::guard('web')->user();
            $authUserName = $authUser?->name ?? '';
        @endphp
        <header class="admin-topbar">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                <i class="shield alternate icon"></i>
                <span class="brand-text">Painel Administrativo</span>
            </a>
            <div class="topbar-spacer"></div>
            @if ($authUser)
                <div class="user-area">
                    <i class="user icon"></i>
                    <span class="user-name">{{ $authUserName }}</span>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" title="Sair">
                        <i class="sign-out icon"></i>
                        <span class="user-name">Sair</span>
                    </button>
                </form>
            @endif
        </header>
        <main class="admin-page">
            @yield('content')
        </main>
    @endif

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.js"></script>

    {{-- Global loading state for any action button in the admin area --}}
    <script>
        (function () {
            'use strict';

            function applyLoading(el) {
                if (!el || el.classList.contains('loading')) {
                    return;
                }
                el.classList.add('loading', 'disabled');
                if (el.tagName === 'BUTTON' || el.tagName === 'INPUT') {
                    el.disabled = true;
                }
                el.setAttribute('aria-busy', 'true');
            }

            // Forms: on submit, lock every submit button inside the form.
            document.addEventListener('submit', function (e) {
                if (e.defaultPrevented) {
                    return;
                }
                var form = e.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }
                if (form.dataset.noLoading === 'true') {
                    return;
                }

                var submitters = form.querySelectorAll(
                    'button[type="submit"], button:not([type]), input[type="submit"]'
                );
                submitters.forEach(applyLoading);
            });

            // Action links (opt-in via data-loading attribute on an <a>).
            document.addEventListener('click', function (e) {
                var link = e.target.closest('a[data-loading]');
                if (!link) {
                    return;
                }
                if (link.classList.contains('loading') || link.classList.contains('disabled')) {
                    e.preventDefault();
                    return;
                }
                applyLoading(link);
            });
        })();
    </script>

    {{--
        Client-side pagination + search for admin tables.

        Wire it up by adding `js-paginated-table` to a <table> and pointing
        at related controls via data attributes:

            <table class="ui table js-paginated-table"
                   data-page-size="10"
                   data-search="#types-search"
                   data-info="#types-info"
                   data-pagination="#types-pagination"
                   data-empty="#types-empty"
                   data-page-size-select="#types-page-size"
                   data-count-badge="#types-count">

        Rows opt into search via `data-search-text="lowercased searchable"`.
    --}}
    <script>
        (function () {
            'use strict';

            function init(table) {
                var tbody = table.querySelector('tbody');
                if (!tbody) {
                    return;
                }
                var allRows = Array.from(tbody.querySelectorAll('tr'));

                var pageSize = parseInt(table.dataset.pageSize || '10', 10) || 10;
                var searchEl   = table.dataset.search        ? document.querySelector(table.dataset.search)        : null;
                var infoEl     = table.dataset.info          ? document.querySelector(table.dataset.info)          : null;
                var pagEl      = table.dataset.pagination    ? document.querySelector(table.dataset.pagination)    : null;
                var emptyEl    = table.dataset.empty         ? document.querySelector(table.dataset.empty)         : null;
                var pageSizeEl = table.dataset.pageSizeSelect? document.querySelector(table.dataset.pageSizeSelect): null;
                var countEl    = table.dataset.countBadge    ? document.querySelector(table.dataset.countBadge)    : null;

                var state = { page: 1, filter: '' };

                function filteredRows() {
                    if (!state.filter) {
                        return allRows;
                    }
                    var q = state.filter.toLowerCase();
                    return allRows.filter(function (row) {
                        var hay = (row.dataset.searchText || row.textContent || '').toLowerCase();
                        return hay.indexOf(q) !== -1;
                    });
                }

                function render() {
                    var rows = filteredRows();
                    var total = rows.length;
                    var totalPages = pageSize > 0 ? Math.max(1, Math.ceil(total / pageSize)) : 1;
                    if (state.page > totalPages) {
                        state.page = totalPages;
                    }
                    if (state.page < 1) {
                        state.page = 1;
                    }

                    // Hide everything first, then reveal the slice for the current page.
                    allRows.forEach(function (r) { r.style.display = 'none'; });
                    var start = pageSize > 0 ? (state.page - 1) * pageSize : 0;
                    var end = pageSize > 0 ? start + pageSize : total;
                    rows.slice(start, end).forEach(function (r) { r.style.display = ''; });

                    renderEmpty(total);
                    renderInfo(total);
                    renderPagination(total, totalPages);
                    renderCountBadge(total);
                }

                function renderEmpty(total) {
                    if (emptyEl) {
                        emptyEl.style.display = total === 0 ? '' : 'none';
                    }
                    // When there's no result we hide the entire table for cleanliness.
                    table.style.display = total === 0 ? 'none' : '';
                }

                function renderInfo(total) {
                    if (!infoEl) return;
                    if (total === 0) {
                        infoEl.textContent = state.filter
                            ? 'Nenhum resultado para "' + state.filter + '".'
                            : 'Nenhum registro.';
                        return;
                    }
                    if (pageSize <= 0 || total <= pageSize) {
                        infoEl.textContent = 'Exibindo ' + total + ' de ' + total + (total === 1 ? ' registro.' : ' registros.');
                        return;
                    }
                    var start = (state.page - 1) * pageSize + 1;
                    var end = Math.min(state.page * pageSize, total);
                    infoEl.textContent = 'Exibindo ' + start + '–' + end + ' de ' + total + (total === 1 ? ' registro.' : ' registros.');
                }

                function renderCountBadge(total) {
                    if (!countEl) return;
                    countEl.textContent = String(total);
                }

                function renderPagination(total, totalPages) {
                    if (!pagEl) return;
                    pagEl.innerHTML = '';
                    if (totalPages <= 1) return;

                    var menu = document.createElement('div');
                    menu.className = 'ui small pagination menu';

                    function addItem(label, page, opts) {
                        opts = opts || {};
                        var item = document.createElement('a');
                        item.className = 'item';
                        if (opts.active)   item.classList.add('active');
                        if (opts.disabled) item.classList.add('disabled');
                        item.textContent = label;
                        if (!opts.disabled && !opts.active && page) {
                            item.addEventListener('click', function (e) {
                                e.preventDefault();
                                state.page = page;
                                render();
                                scrollIntoView();
                            });
                        }
                        menu.appendChild(item);
                    }

                    addItem('«', state.page - 1, { disabled: state.page <= 1 });

                    var windowSize = 5;
                    var startPage = Math.max(1, state.page - Math.floor(windowSize / 2));
                    var endPage = Math.min(totalPages, startPage + windowSize - 1);
                    if (endPage - startPage + 1 < windowSize) {
                        startPage = Math.max(1, endPage - windowSize + 1);
                    }
                    if (startPage > 1) {
                        addItem('1', 1);
                        if (startPage > 2) {
                            addItem('…', null, { disabled: true });
                        }
                    }
                    for (var p = startPage; p <= endPage; p++) {
                        addItem(String(p), p, { active: p === state.page });
                    }
                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            addItem('…', null, { disabled: true });
                        }
                        addItem(String(totalPages), totalPages);
                    }

                    addItem('»', state.page + 1, { disabled: state.page >= totalPages });

                    pagEl.appendChild(menu);
                }

                function scrollIntoView() {
                    var rect = table.getBoundingClientRect();
                    if (rect.top < 0) {
                        table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }

                if (searchEl) {
                    searchEl.addEventListener('input', function (e) {
                        state.filter = (e.target.value || '').trim();
                        state.page = 1;
                        render();
                    });
                }

                if (pageSizeEl) {
                    pageSizeEl.addEventListener('change', function (e) {
                        var val = parseInt(e.target.value, 10);
                        pageSize = isNaN(val) ? 0 : val;
                        state.page = 1;
                        render();
                    });
                }

                render();
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('table.js-paginated-table').forEach(init);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
