<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'Finals POS') }}</title>
    <script>
        (() => {
            let preference = 'system';

            try {
                preference = localStorage.getItem('pos-theme') || 'system';
            } catch (error) {
                // Continue with the operating-system preference when storage is unavailable.
            }

            const theme = preference === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : preference;

            document.documentElement.dataset.theme = theme;
            document.documentElement.dataset.themePreference = preference;
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[250px_minmax(0,1fr)]">
        <aside class="relative z-20 border-b border-slate-200 bg-white lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:justify-start">
                <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-indigo-600 text-sm font-bold text-white">FP</span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold tracking-tight">Finals POS</span>
                        <span class="block truncate text-xs text-slate-500">Coffeehouse team</span>
                    </span>
                </a>
                <button type="button" id="mobile-menu-toggle" aria-controls="primary-navigation" aria-expanded="false" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:hidden">
                    <span class="sr-only">Open navigation menu</span>
                    <svg data-menu-icon="open" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg data-menu-icon="close" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>

            <div id="primary-navigation" class="border-t border-slate-100 px-3 pb-4 pt-3 lg:block lg:border-0 lg:px-0 lg:pt-1">
                <nav aria-label="Main navigation" class="flex flex-col gap-1">
                    <a href="{{ route('dashboard') }}" @class(['primary-nav-link flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors', 'bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white focus-visible:bg-indigo-600 focus-visible:text-white' => request()->routeIs('dashboard'), 'text-slate-600 hover:bg-indigo-600 hover:text-white focus-visible:bg-indigo-600 focus-visible:text-white' => ! request()->routeIs('dashboard')])>
                        <span aria-hidden="true">⌂</span> Dashboard
                    </a>
                    @foreach ([['Products', 'products.index'], ['Customers', 'customers.index'], ['Staff', 'staff.index'], ['Record sale', 'sales.create'], ['Sales history', 'sales.index']] as [$label, $routeName])
                        @if (Illuminate\Support\Facades\Route::has($routeName))
                            <a href="{{ route($routeName) }}" @class(['primary-nav-link flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors', 'bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white focus-visible:bg-indigo-600 focus-visible:text-white' => request()->routeIs(str_replace('.index', '.*', $routeName), str_replace('.create', '.*', $routeName)), 'text-slate-600 hover:bg-indigo-600 hover:text-white focus-visible:bg-indigo-600 focus-visible:text-white' => ! request()->routeIs(str_replace('.index', '.*', $routeName), str_replace('.create', '.*', $routeName))])>
                                <span aria-hidden="true">•</span> {{ $label }}
                            </a>
                        @endif
                    @endforeach
                </nav>

                @auth
                    <div class="mt-4 border-t border-slate-100 pt-4 lg:hidden">
                        <p class="truncate px-3 text-sm font-medium">{{ auth()->user()->full_name }}</p>
                        <p class="truncate px-3 text-xs text-slate-500">{{ '@' . auth()->user()->username }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                            @csrf
                            <button class="min-h-11 w-full rounded-lg border border-slate-200 px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50">Sign out</button>
                        </form>
                    </div>
                @endauth
            </div>

            @auth
                <div class="absolute inset-x-0 bottom-0 hidden border-t border-slate-200 bg-white p-4 lg:block">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->full_name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ '@' . auth()->user()->username }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button class="min-h-11 w-full rounded-lg border border-slate-200 px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50">Sign out</button>
                    </form>
                </div>
            @endauth
        </aside>

        <main class="min-w-0">
            @auth
                <header class="flex min-h-[76px] items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:px-8">
                    <div class="min-w-0">
                        <p class="text-[0.65rem] font-medium uppercase tracking-[0.16em] text-slate-400">Coffeehouse point of sale</p>
                        <h1 class="mt-1 truncate text-lg font-semibold tracking-tight">@yield('heading', 'Dashboard')</h1>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                        <button type="button" data-theme-toggle class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-slate-200 px-3 text-sm font-medium text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100" aria-label="Color theme: system. Activate to change theme">
                            <svg data-theme-icon="light" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2m10-10h-2M4 12H2m17.07-7.07-1.42 1.42M6.35 17.65l-1.42 1.42m14.14 0-1.42-1.42M6.35 6.35 4.93 4.93"/></svg>
                            <svg data-theme-icon="dark" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.2 15.1A8.5 8.5 0 0 1 8.9 3.8 8.5 8.5 0 1 0 20.2 15.1Z"/></svg>
                            <span data-theme-label class="hidden sm:inline">System</span>
                        </button>
                        <span class="hidden rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 sm:inline-flex">Staff session</span>
                    </div>
                </header>
            @endauth

            @guest
                <div class="flex justify-end px-5 pt-5 sm:px-8">
                    <button type="button" data-theme-toggle class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100" aria-label="Color theme: system. Activate to change theme">
                        <svg data-theme-icon="light" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2m10-10h-2M4 12H2m17.07-7.07-1.42 1.42M6.35 17.65l-1.42 1.42m14.14 0-1.42-1.42M6.35 6.35 4.93 4.93"/></svg>
                        <svg data-theme-icon="dark" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="hidden size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.2 15.1A8.5 8.5 0 0 1 8.9 3.8 8.5 8.5 0 1 0 20.2 15.1Z"/></svg>
                        <span data-theme-label>System theme</span>
                    </button>
                </div>
            @endguest

            <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-8 sm:py-9">
                @if (session('status'))
                    <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
