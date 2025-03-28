<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Olimpiade</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 via-indigo-700 to-purple-600">

    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-12">
        </div>

        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Login Olimpiade</h2>

        @if($errors->has('login_gagal'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first('login_gagal') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-gradient-to-r from-blue-700 to-indigo-600 text-white font-semibold rounded-lg hover:opacity-90 transition">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} Olimpiade Ekonomi • Bank Indonesia
        </p>
    </div>

</body>
</html>
