<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIDOKU') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>[x-cloak]{display:none !important;}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-800">
@php
    $menu = [
        ['Dashboard', 'dashboard', ['dashboard'],
         'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
        ['Data Dokumen', 'dokumen.index', ['dokumen.index', 'dokumen.create', 'dokumen.edit', 'dokumen.show'],
         'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['Impor Excel', 'dokumen.import', ['dokumen.import'],
         'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5'],
        ['Clustering K-Means', 'clustering.index', ['clustering.*'],
         'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'],
    ];

    // Menu khusus admin
    if (Auth::user()->role === 'admin') {
        $menu[] = ['Kelola Pengguna', 'pengguna.index', ['pengguna.*'],
         'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'];
    }
@endphp

<div x-data="{ sidebar: false }" class="min-h-screen lg:flex">

    {{-- Latar gelap saat sidebar terbuka di HP --}}
    <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 -translate-x-full flex-col bg-gradient-to-b from-blue-900 to-blue-800 text-white transition-transform duration-200 lg:static lg:translate-x-0">

        <a href="{{ url('/') }}" class="flex items-center gap-3 px-6 py-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C12 2 5 10.5 5 15a7 7 0 0014 0c0-4.5-7-13-7-13z"/></svg>
            </div>
            <div>
                <div class="text-lg font-bold leading-none tracking-wide">SIDOKU</div>
                <div class="mt-1 text-[11px] text-blue-200">BBWS Sumatera II Medan</div>
            </div>
        </a>

        <nav class="flex-1 space-y-1 px-3">
            @foreach ($menu as [$label, $rute, $pola, $ikon])
                @php $aktif = request()->routeIs(...$pola); @endphp
                <a href="{{ route($rute) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                          {{ $aktif ? 'bg-white text-blue-800 shadow' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon }}"/>
                    </svg>
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold">{{ Auth::user()->name }}</div>
                    <div class="truncate text-xs text-blue-200">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('profile.edit') }}" class="flex-1 rounded-md bg-white/10 px-3 py-1.5 text-center text-xs font-medium hover:bg-white/20">Profil</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button class="w-full rounded-md bg-white/10 px-3 py-1.5 text-xs font-medium hover:bg-red-500/80">Keluar</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="min-w-0 flex-1">
        {{-- Bar atas khusus HP --}}
        <div class="flex items-center gap-3 bg-blue-900 px-4 py-3 text-white lg:hidden">
            <button @click="sidebar = true" class="rounded-md p-1 hover:bg-white/10">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="font-bold tracking-wide">SIDOKU</span>
        </div>

        @isset($header)
            <header class="border-b border-slate-200 bg-white">
                <div class="px-4 py-5 sm:px-6 lg:px-8">{{ $header }}</div>
            </header>
        @endisset

        <main>{{ $slot }}</main>
    </div>
</div>
</body>
</html>