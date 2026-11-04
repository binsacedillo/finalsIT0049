@extends('layouts.app')

@section('title', 'Staff details')
@section('heading', 'Staff details')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('staff.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">← Back to staff</a>
        <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-center gap-4">
                    @if ($staff->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($staff->avatar) }}" alt="{{ $staff->full_name }} avatar" class="size-16 rounded-2xl object-cover">
                    @else
                        <span class="grid size-16 place-items-center rounded-2xl bg-indigo-50 text-xl font-semibold text-indigo-700">{{ strtoupper(mb_substr($staff->full_name, 0, 1)) }}</span>
                    @endif
                    <div><p class="text-sm text-slate-500">{{ '@'.$staff->username }}</p><h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ $staff->full_name }}</h2></div>
                </div>
                <a href="{{ route('staff.edit', $staff) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-medium hover:bg-slate-50">Edit account</a>
            </div>
            <dl class="mt-8 grid gap-5 border-t border-slate-100 pt-6 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Account status</dt><dd class="mt-2 text-sm font-medium">{{ $staff->is_active ? 'Active' : 'Inactive' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sales recorded</dt><dd class="mt-2 text-sm font-medium">{{ number_format($staff->sales()->count()) }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Added</dt><dd class="mt-2 text-sm font-medium">{{ $staff->created_at->format('M j, Y') }}</dd></div>
            </dl>
        </section>
    </div>
@endsection
