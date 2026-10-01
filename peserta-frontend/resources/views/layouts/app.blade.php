<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Data Peserta Sertifikasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app">

    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-mark">@include('partials.icon', ['name' => 'award'])</span>
            <span>Sertifikasi<small>Data Peserta</small></span>
        </a>

        <nav class="menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'grid']) Dashboard
            </a>
            <a href="{{ route('peserta.index') }}" class="menu-item {{ request()->routeIs('peserta.*') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'users']) Peserta
            </a>
            <a href="{{ route('skema.index') }}" class="menu-item {{ request()->routeIs('skema.*') ? 'active' : '' }}">
                @include('partials.icon', ['name' => 'layers']) Skema
            </a>
        </nav>

        <div class="side-foot">
            <div class="user">
                <span class="avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(session('api_user.nama', 'A'), 0, 1)) }}</span>
                <div>
                    <div class="user-name">{{ session('api_user.nama', 'Administrator') }}</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item btn-plain">
                    @include('partials.icon', ['name' => 'log-out']) Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="content">
        <main class="main">
            @if (session('success'))
                <div class="alert alert-success">@include('partials.icon', ['name' => 'check-circle']) {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">@include('partials.icon', ['name' => 'alert']) {{ session('error') }}</div>
            @endif
            @if (! empty($apiError))
                <div class="alert alert-error">@include('partials.icon', ['name' => 'alert']) {{ $apiError }}</div>
            @endif

            <div class="page-head">
                <div>
                    <h1>@yield('heading')</h1>
                    @hasSection('subheading')
                        <p class="sub">@yield('subheading')</p>
                    @endif
                </div>
                <div class="head-actions">@yield('actions')</div>
            </div>

            @yield('content')
        </main>
    </div>
</div>

<dialog id="confirmDialog">
    <form method="dialog">
        <div class="dialog-body">
            <h3>Konfirmasi Hapus</h3>
            <p id="confirmText"></p>
            <div class="dialog-actions">
                <button value="cancel" class="btn btn-ghost">Batal</button>
                <button value="ok" class="btn btn-danger">Hapus</button>
            </div>
        </div>
    </form>
</dialog>

<script>
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var dialog = document.getElementById('confirmDialog');
            document.getElementById('confirmText').textContent = form.dataset.confirm;
            dialog.returnValue = '';
            dialog.addEventListener('close', function handler() {
                dialog.removeEventListener('close', handler);
                if (dialog.returnValue === 'ok') { form.submit(); }
            });
            dialog.showModal();
        });
    });
</script>
</body>
</html>
