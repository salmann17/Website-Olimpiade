<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Portal Olimpiade</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

  <nav class="bg-white shadow p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <h1 class="text-xl font-bold text-indigo-600">Portal Olimpiade</h1>
      <div>
        <a href="{{ route('logout') }}" class="text-gray-700 hover:text-indigo-600">Logout</a>
      </div>
    </div>
  </nav>

  <main class="py-6">
    @yield('content')
  </main>
</body>
</html>
