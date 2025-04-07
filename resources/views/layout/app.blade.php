<head>
  @vite('resources/css/app.css')
  <link rel="icon" src="{{ asset('icon.png') }}" type="image/png">
  <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">
</head>

<body class="bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75] min-h-screen">
  <div class="flex">

    {{-- Sidebar --}}
    @include('layout.sidebar')

    {{-- Main Content --}}
    <main class="flex-1 ">
      @yield('content')
    </main>

  </div>
</body>
@stack('scripts')

<link rel="icon" href="{{ asset('icon.png') }}" type="image/png">