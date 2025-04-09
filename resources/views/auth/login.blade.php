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

<body class="min-h-screen flex items-center justify-center bg-gradient-to-b from-[#004a80] to-white">
  <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-sm text-white text-center space-y-5">

    <div>
      <img src="{{ asset('logo.png') }}" alt="Logo" class="h-12 mx-auto mb-2">
    </div>
    <div>
      <h2 class="text-l font-bold text-[#004a80]">Selamat Datang di Portal Olimpiade</h2>
      <p class="text-sm text-[#004a80] font-semibold">Silahkan Login!</p>
    </div>
    <form action="{{ route('login.submit') }}" method="POST" class="text-left space-y-4">
      @csrf
      <div>
        <label for="username" class="block mb-1 text-[#004a80]">Username</label>
        <input type="text" name="username" id="username" required
          class="w-full p-2 rounded-lg bg-slate-400 text-gray-800 focus:outline-none">
      </div>
      <div>
        <label for="password" class="block mb-1 text-[#004a80]" >Password</label>
        <input type="password" name="password" id="password" required
          class="w-full p-2 rounded-lg bg-slate-400 text-gray-800 focus:outline-none">
      </div>
      <button type="submit" class="w-full py-2 bg-[#004a80] text-[#ffffff] hover:text-[#dcdcdc] font-semibold rounded-lg hover:bg-gray-600 transition">Login</button>
    </form>

  </div>
</body>

</html>