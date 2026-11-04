@extends('layouts.app')

@section('title', 'Add product')
@section('heading', 'Add product')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('products.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">← Back to products</a>
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-semibold tracking-tight">Product details</h2>
            <p class="mt-1 text-sm text-slate-500">Add an item to your store catalog.</p>
            @include('products._form', ['action' => route('products.store'), 'method' => 'POST', 'product' => null, 'buttonText' => 'Save product'])
        </div>
    </div>
@endsection
