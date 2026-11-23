@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <section class="overflow-hidden rounded-2xl bg-indigo-950 p-7 text-white shadow-sm sm:p-10">
        <p class="text-sm font-medium text-indigo-200">Welcome, {{ auth()->user()->full_name }}</p>
        <h2 class="mt-3 max-w-2xl text-3xl font-semibold tracking-tight sm:text-4xl">Your coffeehouse workspace is ready.</h2>
        <p class="mt-4 max-w-2xl text-sm leading-6 text-indigo-100/75">Manage products and stock, keep customer and staff records, record sales, and review transaction history.</p>
    </section>

    <section class="mt-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Workspace</p>
                <h2 class="mt-1 text-xl font-semibold tracking-tight">Management areas</h2>
            </div>
            <span class="text-xs text-slate-500">Signed in as {{ '@' . auth()->user()->username }}</span>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ([
                ['01', 'Products', 'Manage items, prices, and stock.', 'products.index'],
                ['02', 'Customers', 'Keep customer contact details organized.', 'customers.index'],
                ['03', 'Staff', 'Manage staff accounts and access.', 'staff.index'],
                ['04', 'Record a sale', 'Complete a sale and update inventory.', 'sales.create'],
                ['05', 'Sales history', 'Review previous transactions.', 'sales.index'],
            ] as [$number, $title, $description, $routeName])
                @if (Illuminate\Support\Facades\Route::has($routeName))
                    <a href="{{ route($routeName) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
                        <span class="grid size-9 place-items-center rounded-lg bg-indigo-50 text-xs font-semibold text-indigo-700">{{ $number }}</span>
                        <h3 class="mt-5 font-semibold group-hover:text-indigo-700">{{ $title }}</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </a>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <span class="grid size-9 place-items-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-500">{{ $number }}</span>
                        <h3 class="mt-5 font-semibold text-slate-700">{{ $title }}</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $description }}</p>
                        <p class="mt-4 text-xs font-medium text-slate-400">Coming in the next project phase</p>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
@endsection
