<x-guest-layout>
    <h1 class="text-2xl font-bold text-slate-900">Selamat datang</h1>
    <p class="mt-1 text-sm text-slate-500">Masuk untuk mengelola dan mengelompokkan dokumen kedinasan.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="nama@email.com"
                       class="block w-full rounded-lg border-slate-300 py-2.5 pl-10 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ lihat: false }">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-blue-700 hover:underline">Lupa password?</a>
                @endif
            </div>
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
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-blue-700 focus:ring-blue-600">
            <span class="text-sm text-slate-600">Ingat saya</span>
        </label>

        <button type="submit"
                class="w-full rounded-lg bg-blue-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-700/30 transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
            Masuk
        </button>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-blue-700 hover:underline">Daftar</a>
        </p>
    @endif
</x-guest-layout>