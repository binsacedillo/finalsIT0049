@extends('layouts.app')

@section('title', 'Add staff')
@section('heading', 'Add staff')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('staff.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">← Back to staff</a>
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-semibold tracking-tight">New staff account</h2>
            <p class="mt-1 text-sm text-slate-500">Create sign-in credentials for a staff member.</p>
            @include('staff._form', ['action' => route('staff.store'), 'method' => 'POST', 'staff' => null, 'buttonText' => 'Create account'])
        </div>
    </div>
@endsection
