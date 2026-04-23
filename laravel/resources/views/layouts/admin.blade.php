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

    @stack('scripts')
</body>
</html>
