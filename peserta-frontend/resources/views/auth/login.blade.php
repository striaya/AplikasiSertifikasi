<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Data Peserta Sertifikasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-page">
    <div class="card login-card">
        {{-- <span class="brand-mark">@include('partials.icon', ['name' => 'award', 'size' => 20])</span> --}}
        <h1>Masuk ke Aplikasi</h1>
        <p class="sub">Pengelolaan Data Peserta Sertifikasi</p>

        @if ($errors->has('login'))
            <div class="alert alert-error">@include('partials.icon', ['name' => 'alert']) {{ $errors->first('login') }}</div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" class="input @error('username') invalid @enderror"
                       value="{{ old('username') }}" autocomplete="username" autofocus>
                @error('username') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" class="input @error('password') invalid @enderror"
                       autocomplete="current-password">
                @error('password') <span class="err">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>
