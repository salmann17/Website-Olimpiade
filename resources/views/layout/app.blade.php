<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Portal Olimpiade</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">
</head>
<body class="bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75]">
@include('layout.sidebar')

  <main class="py-6">
    @yield('content')
  </main>
</body>
</html>
