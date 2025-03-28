<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex">
    @include('layouts.sidebar')

    <main class="flex-1 p-6 bg-gray-50 min-h-screen">
        @yield('content')
    </main>
</body>
