@extends('layouts.app')

@section('title', 'Add customer')
@section('heading', 'Add customer')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('customers.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">← Back to customers</a>
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-semibold tracking-tight">Customer details</h2>
            <p class="mt-1 text-sm text-slate-500">Add contact information to the customer directory.</p>
            @include('customers._form', ['action' => route('customers.store'), 'method' => 'POST', 'customer' => null, 'buttonText' => 'Save customer'])
        </div>
    </div>
@endsection
