@extends('layouts.app')

@section('title', 'Edit customer')
@section('heading', 'Edit customer')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('customers.show', $customer) }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">← Back to customer</a>
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-semibold tracking-tight">Edit customer</h2>
            <p class="mt-1 text-sm text-slate-500">Update the saved customer contact details.</p>
            @include('customers._form', ['action' => route('customers.update', $customer), 'method' => 'PUT', 'customer' => $customer, 'buttonText' => 'Save changes'])
        </div>
    </div>
@endsection
