<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIDOKU') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800">
<div class="min-h-screen lg:grid lg:grid-cols-2">

    {{-- Panel kiri (hanya di layar lebar) --}}
    <div class="relative hidden overflow-hidden bg-gradient-to-br from-blue-950 via-blue-800 to-sky-600 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-32 -left-20 h-[28rem] w-[28rem] rounded-full bg-white/5"></div>
        <div class="absolute bottom-40 right-10 h-40 w-40 rounded-full bg-sky-300/20"></div>

        <div class="relative flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C12 2 5 10.5 5 15a7 7 0 0014 0c0-4.5-7-13-7-13z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-wide">SIDOKU</div>
                <div class="text-xs text-blue-200">BBWS Sumatera II Medan</div>
            </div>
        </div>

        <div class="relative max-w-md">
            <h2 class="text-4xl font-bold leading-tight">Sistem Pengelolaan Dokumen Kedinasan</h2>
            <p class="mt-4 text-blue-100">Penerapan Metode K-Means Clustering untuk Pengelompokan Dokumen pada Kedinasan.</p>

            <ul class="mt-8 space-y-3 text-sm text-blue-50">
                @foreach (['Arsip dokumen terpusat dan mudah dicari', 'Impor data dari Excel dengan template', 'Pengelompokan dokumen otomatis dengan K-Means'] as $poin)
                    <li class="flex items-center gap-3">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        {{ $poin }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="relative text-xs text-blue-200">Kerja Praktik &middot; Kementerian Pekerjaan Umum &middot; Ditjen Sumber Daya Air</div>
    </div>

    {{-- Panel kanan: form --}}
    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-6 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center lg:hidden">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-700 text-white">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C12 2 5 10.5 5 15a7 7 0 0014 0c0-4.5-7-13-7-13z"/></svg>
                </div>
                <div class="mt-3 text-2xl font-bold text-blue-900">SIDOKU</div>
                <div class="text-xs text-slate-500">Sistem Pengelolaan Dokumen Kedinasan</div>
            </div>

            <div class="rounded-2xl bg-white p-8 shadow-xl shadow-slate-200/70 ring-1 ring-slate-100">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} SIDOKU &middot; BBWS Sumatera II Medan</p>
        </div>
    </div>
</div>
</body>
</html>