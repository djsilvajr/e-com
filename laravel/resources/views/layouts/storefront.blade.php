<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'e-com'))</title>

    {{-- Semantic UI --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.css">

    <style>
        /* ========= Brand palette (project colors) ========= */
        :root {
            --brand-primary: #F53003;
            --brand-primary-hover: #c8260a;
            --brand-primary-soft: #fff2f2;
            --brand-dark: #1b1b18;
            --brand-dark-2: #3E3E3A;
            --brand-light: #FDFDFC;
            --brand-muted: #706f6c;
            --brand-border: #19140035;
        }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        body {
            display: flex;
            flex-direction: column;
            background: var(--brand-light);
            color: var(--brand-dark);
        }
        main.page-content {
            flex: 1 0 auto;
            padding: 2em 0;
        }
        footer { flex-shrink: 0; }

        /* ========= Semantic UI overrides for brand ========= */
        .ui.red.button,
        .ui.red.buttons .button {
            background-color: var(--brand-primary);
        }
        .ui.red.button:hover,
        .ui.red.buttons .button:hover {
            background-color: var(--brand-primary-hover);
        }
        .ui.basic.red.button {
            box-shadow: 0 0 0 1px var(--brand-primary) inset !important;
            color: var(--brand-primary) !important;
        }
        .ui.basic.red.button:hover {
            background-color: var(--brand-primary-soft) !important;
            color: var(--brand-primary-hover) !important;
        }
        .ui.red.header,
        .ui.red.text { color: var(--brand-primary) !important; }

        a { color: var(--brand-primary); }
        a:hover { color: var(--brand-primary-hover); }

        .ui.form .field.error input,
        .ui.form .fields.error .field input {
            background: var(--brand-primary-soft);
            border-color: var(--brand-primary);
            color: var(--brand-dark);
        }

        /* ========= Navbar (2 rows) ========= */
        /* Normaliza ícones Semantic UI dentro da navbar para alinhar com o texto */
        .site-navbar i.icon {
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            vertical-align: middle;
            height: 1em;
            width: auto;
            min-width: 1em;
        }

        .site-navbar {
            background: #ffffff;
            box-shadow: 0 1px 2px var(--brand-border);
            border-bottom: 1px solid var(--brand-border);
        }
        .site-navbar .nav-top {
            background: var(--brand-dark);
            color: #ffffff;
            padding: 0.55em 0;
            font-size: 0.92rem;
        }
        .site-navbar .nav-top > .ui.container > div { /* top flex row */
            flex-wrap: wrap;
            row-gap: 0.5em;
        }
        .site-navbar .nav-top a { color: #ffffff; text-decoration: none; }
        .site-navbar .nav-top a:hover { color: var(--brand-primary); }
        .site-navbar .nav-top .brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5em;
            line-height: 1;
        }
        .site-navbar .nav-top .brand .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: var(--brand-primary);
            color: #ffffff;
            flex: 0 0 auto;
        }
        .site-navbar .nav-top .nav-top-actions {
            display: inline-flex;
            align-items: center;
            gap: 0.35em;
            flex-wrap: wrap;
            row-gap: 0.25em;
            justify-content: flex-end;
        }
        .site-navbar .nav-top .nav-top-actions > span,
        .site-navbar .nav-top .nav-top-actions > form {
            display: inline-flex;
            align-items: center;
        }
        .site-navbar .nav-top .nav-top-actions .link-btn {
            padding: 0.4em 0.9em;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 0.4em;
            line-height: 1;
            white-space: nowrap;
        }
        .site-navbar .nav-top .nav-top-actions .link-btn:hover {
            background: rgba(255,255,255,0.08);
            color: #ffffff;
        }
        .site-navbar .nav-top .nav-top-actions .link-btn.primary {
            background: var(--brand-primary);
            color: #ffffff;
        }
        .site-navbar .nav-top .nav-top-actions .link-btn.primary:hover {
            background: var(--brand-primary-hover);
        }
        .site-navbar .nav-top .divider-dot {
            width: 3px; height: 3px; border-radius: 50%;
            background: rgba(255,255,255,0.3);
            display: inline-block;
            margin: 0 0.4em;
            flex: 0 0 auto;
        }

        .site-navbar .nav-bottom {
            padding: 0.5em 0;
        }
        .site-navbar .nav-bottom .nav-row {
            display: flex;
            align-items: center;
            gap: 1em;
        }
        .site-navbar .nav-bottom .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25em;
            flex-wrap: wrap;
        }
        .site-navbar .nav-bottom .nav-links a {
            color: var(--brand-dark);
            padding: 0.55em 0.9em;
            border-radius: 4px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4em;
            line-height: 1;
            white-space: nowrap;
        }
        .site-navbar .nav-bottom .nav-links a:hover {
            background: var(--brand-primary-soft);
            color: var(--brand-primary);
        }
        .site-navbar .nav-bottom .nav-links a.active {
            background: var(--brand-primary-soft);
            color: var(--brand-primary);
        }
        .site-navbar .nav-bottom .nav-search {
            flex: 1;
            display: flex;
            justify-content: center;
            min-width: 0;
        }
        .site-navbar .nav-bottom .nav-search .ui.input { width: 100%; max-width: 520px; }
        .site-navbar .nav-bottom .nav-right {
            display: flex;
            align-items: center;
            gap: 0.5em;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .site-navbar .nav-bottom .nav-right a,
        .site-navbar .nav-bottom .nav-right .user-menu > a {
            color: var(--brand-dark);
            padding: 0.45em 0.8em;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 0.4em;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
        }
        .site-navbar .nav-bottom .nav-right a:hover,
        .site-navbar .nav-bottom .nav-right .user-menu > a:hover {
            background: var(--brand-primary-soft);
            color: var(--brand-primary);
        }
        .site-navbar .nav-bottom .user-menu.ui.dropdown .menu .item {
            display: flex;
            align-items: center;
            gap: 0.5em;
        }
        .site-navbar .nav-bottom .cart-count {
            background: var(--brand-primary);
            color: #ffffff;
            border-radius: 999px;
            font-size: 0.72rem;
            padding: 0.1em 0.5em;
            margin-left: 0.15em;
            min-width: 1.4em;
            text-align: center;
            line-height: 1.4;
            display: inline-block;
        }

        /* ========= Responsivo ========= */
        /* Tablets (≤ 960px): reduz espaçamentos para caber em telas médias */
        @media (max-width: 960px) {
            .site-navbar .nav-bottom .nav-row { gap: 0.5em; }
            .site-navbar .nav-bottom .nav-links a { padding: 0.5em 0.6em; font-size: 0.95rem; }
            .site-navbar .nav-bottom .nav-right a,
            .site-navbar .nav-bottom .nav-right .user-menu > a { padding: 0.4em 0.6em; font-size: 0.95rem; }
        }

        /* Celular (≤ 768px): empilha para melhor leitura */
        @media (max-width: 768px) {
            .site-navbar .nav-top { font-size: 0.85rem; padding: 0.5em 0; }
            .site-navbar .nav-top > .ui.container > div {
                justify-content: center !important;
            }
            .site-navbar .nav-top .brand { font-size: 1.1rem; }
            .site-navbar .nav-top .nav-top-actions {
                flex-basis: 100%;
                justify-content: center;
            }

            .site-navbar .nav-bottom .nav-row {
                flex-wrap: wrap;
                gap: 0.5em;
            }
            .site-navbar .nav-bottom .nav-links {
                order: 2;
                flex-basis: 100%;
                justify-content: center;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 0.25em;
            }
            .site-navbar .nav-bottom .nav-search {
                order: 1;
                flex-basis: 100%;
                margin-top: 0.25em;
            }
            .site-navbar .nav-bottom .nav-search .ui.input { max-width: 100%; }
            .site-navbar .nav-bottom .nav-right {
                order: 3;
                flex-basis: 100%;
                justify-content: space-around;
                border-top: 1px solid var(--brand-border);
                padding-top: 0.5em;
                margin-top: 0.25em;
            }

            main.page-content { padding: 1em 0; }
            .ui.container { padding-left: 1em !important; padding-right: 1em !important; }
        }

        /* Celular pequeno (≤ 480px): oculta rótulos deixando apenas ícones nas ações */
        @media (max-width: 480px) {
            .site-navbar .nav-top .divider-dot { display: none; }
            .site-navbar .nav-top .nav-top-actions .link-btn span,
            .site-navbar .nav-top .nav-top-actions > span {
                display: none;
            }
            .site-navbar .nav-bottom .nav-right a span:not(.cart-count),
            .site-navbar .nav-bottom .nav-right .user-menu > a span:not(.cart-count) {
                display: none;
            }
            .site-navbar .nav-bottom .nav-right a,
            .site-navbar .nav-bottom .nav-right .user-menu > a {
                padding: 0.55em 0.7em;
            }
            .site-navbar .nav-bottom .nav-links a { font-size: 0.9rem; padding: 0.45em 0.55em; }
            .site-navbar .nav-bottom .nav-links a span { display: inline; }
        }
    </style>

    @stack('head')
</head>
<body>
    <header>
        <x-navbar :active="$active ?? ''" />
    </header>

    <main class="page-content">
        <div class="ui container">
            @yield('content')
        </div>
    </main>

    <footer>
        <x-footer />
    </footer>

    {{-- jQuery (required by Semantic UI) + Semantic UI JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.js"></script>

    <script>
        $(function () {
            $('.ui.dropdown').dropdown();
        });
    </script>

    @stack('scripts')
</body>
</html>
