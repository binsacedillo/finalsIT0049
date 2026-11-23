@extends('layouts.app')

@section('title', 'Customer details')
@section('heading', 'Customer details')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('customers.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">← Back to customers</a>
        <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="grid size-14 place-items-center rounded-2xl bg-indigo-50 text-lg font-semibold text-indigo-700">{{ strtoupper(mb_substr($customer->full_name, 0, 1)) }}</span>
                    <div><p class="text-sm text-slate-500">Customer record</p><h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ $customer->full_name }}</h2></div>
                </div>
                <a href="{{ route('customers.edit', $customer) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-medium hover:bg-slate-50">Edit details</a>
            </div>
            <dl class="mt-8 grid gap-5 border-t border-slate-100 pt-6 sm:grid-cols-2">
                <div class="min-w-0"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</dt><dd class="mt-2 break-all text-sm font-medium">{{ $customer->email }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Phone</dt><dd class="mt-2 text-sm font-medium">{{ $customer->phone ?: 'Not provided' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Added</dt><dd class="mt-2 text-sm font-medium">{{ $customer->created_at->format('M j, Y') }}</dd></div>
            </dl>
        </section>
    </div>
@endsection
