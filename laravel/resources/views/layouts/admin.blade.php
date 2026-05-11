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
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main.admin-page {
            flex: 1 0 auto;
            padding: 2em 0;
        }
    </style>

    @stack('head')
</head>
<body>
    <main class="admin-page">
        @yield('content')
    </main>

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

    @stack('scripts')
</body>
</html>
