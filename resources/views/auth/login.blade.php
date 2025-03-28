<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="p-5">
    <h2>Login</h2>
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>

        @error('login_gagal')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </form>
</body>
</html>
