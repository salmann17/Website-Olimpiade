<!DOCTYPE html>
<html lang="en">

<head>
  @vite('resources/css/app.css')
  <meta charset="UTF-8">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" src="{{ asset('icon.png') }}" type="image/png">
  <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#010101] via-[#2b2b4d] to-[#3b3b75]">
  <div class="bg-gradient-to-br from-[#48dbfb] to-[#5f27cd] p-8 rounded-2xl shadow-2xl w-full max-w-sm text-white text-center space-y-5">

    <div>
      <img src="{{ asset('logo.png') }}" alt="Logo" class="h-12 mx-auto mb-2">
    </div>
    <div>
      <h2 class="text-l font-bold">Selamat Datang di Portal Olimpiade</h2>
      <p class="text-sm text-white/80">Silahkan Login!</p>
    </div>
    <form action="{{ route('login.submit') }}" method="POST" class="text-left space-y-4">
      @csrf
      <div>
        <label for="username" class="block mb-1">Username</label>
        <input type="text" name="username" id="username" required
          class="w-full p-2 rounded-lg bg-white text-gray-800 focus:outline-none">
      </div>
      <div>
        <label for="password" class="block mb-1">Password</label>
        <input type="password" name="password" id="password" required
          class="w-full p-2 rounded-lg bg-white text-gray-800 focus:outline-none">
      </div>
      <button type="submit" class="w-full py-2 bg-gray-600 text-[#ffffff] hover:text-[#5f27cd] font-semibold rounded-lg hover:bg-white transition">Login</button>
    </form>

  </div>
</body>

</html>