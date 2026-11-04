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
        <div class="flex gap-2 text-sm">
            @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'all' => 'All'] as $key => $label)
                <a href="{{ route('staff.index', array_filter(['status' => $key, 'q' => $search])) }}" @class(['rounded-lg px-3 py-2 font-medium', 'bg-indigo-50 text-indigo-700' => $status === $key, 'text-slate-600 hover:bg-white' => $status !== $key])>{{ $label }}</a>
            @endforeach
        </div>
        <form method="GET" action="{{ route('staff.index') }}" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <label for="staff-search" class="sr-only">Search staff</label>
            <input id="staff-search" name="q" value="{{ $search }}" placeholder="Search name or username" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-64">
            <button class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium hover:bg-slate-50">Search</button>
        </form>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
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
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($staffMember->avatar) }}" alt="{{ $staffMember->full_name }} avatar" class="size-10 rounded-full object-cover">
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
        @if ($staff->hasPages()) <div class="border-t border-slate-100 px-5 py-4">{{ $staff->links() }}</div> @endif
    </div>
@endsection
