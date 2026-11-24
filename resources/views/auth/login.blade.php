<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff sign in · {{ config('app.name', 'Finals POS') }}</title>
    <script>
        (() => {
            let preference = 'system';

            try {
                preference = localStorage.getItem('pos-theme') || 'system';
            } catch (error) {
                // Continue with the operating-system preference when storage is unavailable.
            }

            document.documentElement.dataset.theme = preference === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : preference;
            document.documentElement.dataset.themePreference = preference;
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">
        <section class="relative hidden overflow-hidden bg-indigo-950 p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-16">
            <div class="absolute -right-24 -top-24 size-96 rounded-full border border-white/10"></div>
            <div class="absolute -bottom-40 -left-28 size-[30rem] rounded-full border border-white/10"></div>
            <a href="{{ route('login') }}" class="relative flex items-center gap-3">
                <span class="grid size-11 place-items-center rounded-xl bg-white text-sm font-bold text-indigo-950">FP</span>
                <span class="text-sm font-semibold">Finals POS</span>
            </a>
            <div class="relative max-w-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-200">Coffeehouse operations</p>
                <h1 class="mt-5 text-5xl font-semibold leading-tight tracking-tight">A clear view of every sale.</h1>
                <p class="mt-6 max-w-md text-base leading-7 text-indigo-100/75">Sign in with your staff account to manage products, customers, and transactions.</p>
            </div>
            <p class="relative text-xs text-indigo-200/60">Authorized staff access only</p>
        </section>

        <section class="relative flex items-center justify-center px-5 py-20 sm:px-10 sm:py-16">
            <div class="absolute right-5 top-5 sm:right-8 sm:top-8">
                <button type="button" data-theme-toggle class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100" aria-label="Color theme: system. Activate to change theme">
                    <svg data-theme-icon="light" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2m10-10h-2M4 12H2m17.07-7.07-1.42 1.42M6.35 17.65l-1.42 1.42m14.14 0-1.42-1.42M6.35 6.35 4.93 4.93"/></svg>
                    <svg data-theme-icon="dark" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.2 15.1A8.5 8.5 0 0 1 8.9 3.8 8.5 8.5 0 1 0 20.2 15.1Z"/></svg>
                    <span data-theme-label>System theme</span>
                </button>
            </div>
            <div class="w-full max-w-md">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="grid size-10 place-items-center rounded-xl bg-indigo-600 text-sm font-bold text-white">FP</span>
                    <span class="text-sm font-semibold">Finals POS</span>
                </div>
                <p class="text-sm font-semibold text-indigo-700">Staff portal</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight">Welcome back</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Enter your staff credentials to continue.</p>

                @if (config('pos.demo_access_enabled') && filled(config('pos.admin_username')) && filled(config('pos.admin_password')))
                    <aside class="instructor-review mt-6 rounded-2xl border border-indigo-200 bg-indigo-50 p-4 text-sm text-slate-800" aria-label="Instructor review login details">
                        <p class="font-semibold text-indigo-700">For instructor review</p>
                        <p class="mt-1 leading-6 text-slate-700">Use this shared staff account to explore the POS:</p>
                        <dl class="mt-3 grid gap-2 sm:grid-cols-[5.5rem_1fr]">
                            <dt class="font-medium">Username</dt>
                            <dd><code class="break-all rounded bg-white/80 px-1.5 py-0.5">{{ config('pos.admin_username') }}</code></dd>
                            <dt class="font-medium">Password</dt>
                            <dd><code class="break-all rounded bg-white/80 px-1.5 py-0.5">{{ config('pos.admin_password') }}</code></dd>
                        </dl>
                        <p class="mt-3 text-xs leading-5 text-slate-600">This shared account is visible to anyone who visits this page and has staff access to the system. Use it only with demo data.</p>
                    </aside>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="username" class="mb-2 block text-sm font-medium">Username</label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        @error('username') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Keep me signed in
                    </label>
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Sign in</button>
                </form>
                <p class="mt-8 text-center text-xs text-slate-400">Use the staff account configured for this POS.</p>
            </div>
        </section>
    </main>
</body>
</html>
