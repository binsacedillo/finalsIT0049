@extends('layouts.app')

@section('title', 'Edit staff')
@section('heading', 'Edit staff')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('staff.show', $staff) }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">← Back to staff member</a>
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-semibold tracking-tight">Edit staff account</h2>
            <p class="mt-1 text-sm text-slate-500">Leave the password blank to keep the current password.</p>
            @include('staff._form', ['action' => route('staff.update', $staff), 'method' => 'PUT', 'staff' => $staff, 'buttonText' => 'Save changes'])
        </div>
    </div>
@endsection
