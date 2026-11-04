<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'Finals POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[250px_1fr]">
        <aside class="flex flex-col border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-6 py-5">
                <span class="grid size-10 place-items-center rounded-xl bg-indigo-600 text-sm font-bold text-white">FP</span>
                <span>
                    <span class="block text-sm font-semibold tracking-tight">Finals POS</span>
                    <span class="block text-xs text-slate-500">Staff workspace</span>
                </span>
            </a>
            <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:flex-col lg:px-3">
                <a href="{{ route('dashboard') }}" @class(['flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium', 'bg-indigo-50 text-indigo-700' => request()->routeIs('dashboard'), 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('dashboard')])>
                    <span aria-hidden="true">⌂</span> Dashboard
                </a>
                @foreach ([['Products', 'products.index'], ['Customers', 'customers.index'], ['Staff', 'staff.index'], ['Record sale', 'sales.create'], ['Sales history', 'sales.index']] as [$label, $routeName])
                    @if (Illuminate\Support\Facades\Route::has($routeName))
                        <a href="{{ route($routeName) }}" @class(['flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium', 'bg-indigo-50 text-indigo-700' => request()->routeIs(str_replace('.index', '.*', $routeName), str_replace('.create', '.*', $routeName)), 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs(str_replace('.index', '.*', $routeName), str_replace('.create', '.*', $routeName))])>
                            <span aria-hidden="true">•</span> {{ $label }}
                        </a>
                    @endif
                @endforeach
            </nav>
            @auth
                <div class="mt-auto hidden border-t border-slate-200 p-4 lg:block">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->full_name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ '@' . auth()->user()->username }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button class="w-full rounded-lg border border-slate-200 px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50">Sign out</button>
                    </form>
                </div>
            @endauth
        </aside>

        <main class="min-w-0">
            @auth
                <header class="flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 sm:px-8">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-400">Point of sale</p>
                        <h1 class="mt-1 text-lg font-semibold tracking-tight">@yield('heading', 'Dashboard')</h1>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Staff session</span>
                </header>
            @endauth

            <div class="mx-auto w-full max-w-7xl px-5 py-7 sm:px-8 sm:py-9">
                @if (session('status'))
                    <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
