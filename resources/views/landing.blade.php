<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIDOKU | Sistem Pengelolaan Dokumen Kedinasan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-800 antialiased">

<div class="relative flex min-h-screen flex-col overflow-hidden bg-gradient-to-br from-blue-950 via-blue-800 to-sky-600">

    {{-- Hiasan latar --}}
    <div class="absolute -right-32 -top-32 h-[28rem] w-[28rem] rounded-full bg-white/10"></div>
    <div class="absolute -bottom-40 -left-24 h-[30rem] w-[30rem] rounded-full bg-white/5"></div>
    <div class="absolute bottom-32 left-1/2 h-40 w-40 rounded-full bg-sky-300/20"></div>

    {{-- Navbar --}}
    <header class="relative z-10">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <a href="{{ url('/') }}" class="flex items-center gap-3 text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C12 2 5 10.5 5 15a7 7 0 0014 0c0-4.5-7-13-7-13z"/></svg>
                </span>
                <span>
                    <span class="block text-lg font-bold leading-none tracking-wide">SIDOKU</span>
                    <span class="text-[11px] text-blue-200">BBWS Sumatera II Medan</span>
                </span>
            </a>

            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">Dashboard</a>
            @else
                <a href="#masuk" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">Masuk</a>
            @endauth
        </div>
    </header>

    {{-- Hero + form login --}}
    <main class="relative z-10 mx-auto grid w-full max-w-7xl flex-1 items-center gap-12 px-6 py-10 lg:grid-cols-2">

        <div class="text-white">
            <span class="inline-block rounded-full bg-white/15 px-4 py-1 text-xs font-semibold tracking-wide text-blue-50">
                Kementerian Pekerjaan Umum &middot; Ditjen Sumber Daya Air
            </span>
            <h1 class="mt-5 text-4xl font-extrabold leading-tight sm:text-5xl">
                Sistem Pengelolaan Dokumen Kedinasan
            </h1>
            <p class="mt-5 max-w-xl text-lg text-blue-100">
                Kelola arsip dokumen dan kelompokkan secara otomatis dengan metode K-Means Clustering
                di Balai Besar Wilayah Sungai Sumatera II Medan.
            </p>
        </div>

        {{-- Kartu login --}}
        <div id="masuk" class="mx-auto w-full max-w-md scroll-mt-24">
            <div class="rounded-2xl bg-white p-8 shadow-2xl shadow-blue-950/40">

                @auth
                    <div class="mb-6 rounded-xl bg-blue-50 p-4 text-sm">
                        <div class="text-slate-600">Anda sedang masuk sebagai</div>
                        <div class="font-semibold text-blue-900">{{ Auth::user()->name }}</div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('dashboard') }}" class="flex-1 rounded-lg bg-blue-700 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-blue-800">Buka Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                @csrf
                                <button class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Masuk dengan akun lain</h2>
                @else
                    <h2 class="text-2xl font-bold text-slate-900">Selamat datang</h2>
                    <p class="mt-1 text-sm text-slate-500">Masuk untuk mengelola dan mengelompokkan dokumen kedinasan.</p>
                @endauth

                @if (session('status'))
                    <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('masuk') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                   placeholder="nama@email.com"
                                   class="block w-full rounded-lg border-slate-300 py-2.5 pl-10 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                        </div>
                        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ lihat: false }">
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </span>
                            <input id="password" :type="lihat ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                   placeholder="Masukkan password"
                                   class="block w-full rounded-lg border-slate-300 py-2.5 pl-10 pr-16 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                            <button type="button" @click="lihat = !lihat"
                                    class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-500 hover:text-blue-700"
                                    x-text="lihat ? 'Sembunyi' : 'Lihat'"></button>
                        </div>
                        @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                        <span class="text-sm text-slate-600">Ingat saya</span>
                    </label>

                    <button type="submit"
                            class="w-full rounded-lg bg-blue-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-700/30 transition hover:bg-blue-800">
                        Masuk
                    </button>
                </form>

                <p class="mt-5 text-center text-xs text-slate-400">Akun dibuat oleh administrator. Hubungi admin bila belum memiliki akun.</p>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="relative z-10 py-6 text-center text-xs text-blue-200">
        Balai Besar Wilayah Sungai Sumatera II Medan &middot; &copy; {{ date('Y') }} SIDOKU
    </footer>
</div>

</body>
</html>