@extends('layouts.app')

@section('title', 'Staff')
@section('heading', 'Staff')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-slate-500">Manage staff accounts and sign-in access.</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">Staff directory</h2>
        </div>
        <a href="{{ route('staff.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Add staff</a>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-2 overflow-x-auto text-sm">
            @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'all' => 'All'] as $key => $label)
                <a href="{{ route('staff.index', array_filter(['status' => $key, 'q' => $search])) }}" @class(['inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 py-2 font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600', 'bg-indigo-600 text-white shadow-sm hover:bg-indigo-700' => $status === $key, 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => $status !== $key]) @if ($status === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </div>
        <form method="GET" action="{{ route('staff.index') }}" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <label for="staff-search" class="sr-only">Search staff</label>
            <input id="staff-search" name="q" value="{{ $search }}" placeholder="Search name or username" class="min-h-11 w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-64">
            <button class="min-h-11 shrink-0 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium hover:bg-slate-50">Search</button>
        </form>
    </div>

    <div class="mt-4 space-y-3 md:hidden">
        @forelse ($staff as $staffMember)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex min-w-0 items-center gap-3">
                    @if ($staffMember->avatar)
                        <img src="{{ $staffMember->avatar_url }}" alt="{{ $staffMember->full_name }} avatar" class="size-11 shrink-0 rounded-full object-cover">
                    @else
                        <span class="grid size-11 shrink-0 place-items-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-700">{{ strtoupper(mb_substr($staffMember->full_name, 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('staff.show', $staffMember) }}" class="break-words font-semibold text-slate-900 hover:text-indigo-700">{{ $staffMember->full_name }} @if ($staffMember->is(auth()->user())) <span class="text-xs font-normal text-slate-400">(you)</span> @endif</a>
                        <p class="mt-1 truncate text-sm text-slate-600">{{ '@'.$staffMember->username }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-sm">
                    <span class="text-slate-600">{{ number_format($staffMember->sales_count) }} sales</span>
                    <span @class(['rounded-full px-2.5 py-1 text-xs font-medium', 'bg-emerald-50 text-emerald-700' => $staffMember->is_active, 'bg-slate-100 text-slate-600' => ! $staffMember->is_active])>{{ $staffMember->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('staff.edit', $staffMember) }}" class="inline-flex min-h-11 items-center rounded-lg border border-slate-200 px-3 text-sm font-medium hover:bg-slate-50">Edit</a>
                    @if (! $staffMember->is(auth()->user()))
                        <form method="POST" action="{{ route('staff.status', $staffMember) }}">
                            @csrf @method('PATCH')
                            <button class="min-h-11 rounded-lg border border-slate-200 px-3 text-sm font-medium hover:bg-slate-50">{{ $staffMember->is_active ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                        @if ($staffMember->sales_count === 0)
                            <form method="POST" action="{{ route('staff.destroy', $staffMember) }}" onsubmit="return confirm('Delete this staff account?')">
                                @csrf @method('DELETE')
                                <button class="min-h-11 rounded-lg border border-rose-200 px-3 text-sm font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                            </form>
                        @endif
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center"><p class="font-medium text-slate-700">No staff accounts found</p><p class="mt-1 text-sm text-slate-500">Add a staff member or try another filter.</p></div>
        @endforelse
    </div>

    <div class="mt-4 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[780px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3 font-semibold">Staff member</th><th class="px-5 py-3 font-semibold">Username</th><th class="px-5 py-3 font-semibold">Sales</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 text-right font-semibold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($staff as $staffMember)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <a href="{{ route('staff.show', $staffMember) }}" class="flex items-center gap-3">
                                    @if ($staffMember->avatar)
                                        <img src="{{ $staffMember->avatar_url }}" alt="{{ $staffMember->full_name }} avatar" class="size-10 rounded-full object-cover">
                                    @else
                                        <span class="grid size-10 place-items-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-700">{{ strtoupper(mb_substr($staffMember->full_name, 0, 1)) }}</span>
                                    @endif
                                    <span class="font-medium text-slate-900 hover:text-indigo-700">{{ $staffMember->full_name }} @if ($staffMember->is(auth()->user())) <span class="text-xs font-normal text-slate-400">(you)</span> @endif</span>
                                </a>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ '@'.$staffMember->username }}</td>
                            <td class="px-5 py-4 tabular-nums">{{ number_format($staffMember->sales_count) }}</td>
                            <td class="px-5 py-4"><span @class(['rounded-full px-2.5 py-1 text-xs font-medium', 'bg-emerald-50 text-emerald-700' => $staffMember->is_active, 'bg-slate-100 text-slate-600' => ! $staffMember->is_active])>{{ $staffMember->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('staff.edit', $staffMember) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium hover:bg-slate-50">Edit</a>
                                    @if (! $staffMember->is(auth()->user()))
                                        <form method="POST" action="{{ route('staff.status', $staffMember) }}">
                                            @csrf @method('PATCH')
                                            <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium hover:bg-slate-50">{{ $staffMember->is_active ? 'Deactivate' : 'Activate' }}</button>
                                        </form>
                                        @if ($staffMember->sales_count === 0)
                                            <form method="POST" action="{{ route('staff.destroy', $staffMember) }}" onsubmit="return confirm('Delete this staff account?')">
                                                @csrf @method('DELETE')
                                                <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center"><p class="font-medium text-slate-700">No staff accounts found</p><p class="mt-1 text-sm text-slate-500">Add a staff member or try another filter.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($staff->hasPages()) <div class="border-t border-slate-100 px-4 py-4 sm:px-5">{{ $staff->links() }}</div> @endif
    </div>
    @if ($staff->hasPages()) <div class="mt-4 md:hidden">{{ $staff->links() }}</div> @endif
@endsection
