@extends('layouts.app')

@section('title', 'Customers')
@section('heading', 'Customers')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-slate-500">Keep customer contact information organized.</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">Customer directory</h2>
        </div>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Add customer</a>
    </div>

    <form method="GET" action="{{ route('customers.index') }}" class="mt-6 flex flex-col gap-2 sm:flex-row">
        <label for="customer-search" class="sr-only">Search customers</label>
        <input id="customer-search" name="q" value="{{ $search }}" placeholder="Search name or email" class="min-h-11 w-full max-w-sm rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
        <button class="min-h-11 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium hover:bg-slate-50">Search</button>
    </form>

    <div class="mt-4 space-y-3 md:hidden">
        @forelse ($customers as $customer)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="min-w-0">
                    <a class="break-words font-semibold text-slate-900 hover:text-indigo-700" href="{{ route('customers.show', $customer) }}">{{ $customer->full_name }}</a>
                    <p class="mt-1 break-all text-sm text-slate-600">{{ $customer->email }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $customer->phone ?: 'No phone provided' }}</p>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('customers.edit', $customer) }}" class="inline-flex min-h-11 items-center rounded-lg border border-slate-200 px-3 text-sm font-medium hover:bg-slate-50">Edit</a>
                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer? Existing sales will remain in history.')">
                        @csrf @method('DELETE')
                        <button class="min-h-11 rounded-lg border border-rose-200 px-3 text-sm font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center"><p class="font-medium text-slate-700">No customers found</p><p class="mt-1 text-sm text-slate-500">Add a customer or try another search.</p></div>
        @endforelse
    </div>

    <div class="mt-4 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3 font-semibold">Customer</th><th class="px-5 py-3 font-semibold">Email</th><th class="px-5 py-3 font-semibold">Phone</th><th class="px-5 py-3 text-right font-semibold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-medium"><a class="text-slate-900 hover:text-indigo-700" href="{{ route('customers.show', $customer) }}">{{ $customer->full_name }}</a></td>
                            <td class="px-5 py-4 text-slate-600">{{ $customer->email }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $customer->phone ?: '—' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('customers.edit', $customer) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium hover:bg-slate-50">Edit</a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer? Existing sales will remain in history.')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-14 text-center"><p class="font-medium text-slate-700">No customers found</p><p class="mt-1 text-sm text-slate-500">Add a customer or try another search.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages()) <div class="border-t border-slate-100 px-4 py-4 sm:px-5">{{ $customers->links() }}</div> @endif
    </div>
    @if ($customers->hasPages()) <div class="mt-4 md:hidden">{{ $customers->links() }}</div> @endif
@endsection
