<form method="POST" action="{{ $action }}" class="mt-7 space-y-5">
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    <div>
        <label for="full_name" class="mb-2 block text-sm font-medium">Full name</label>
        <input id="full_name" name="full_name" value="{{ old('full_name', $customer?->full_name) }}" required maxlength="100" autocomplete="name" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
        @error('full_name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="email" class="mb-2 block text-sm font-medium">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $customer?->email) }}" required maxlength="100" autocomplete="email" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="phone" class="mb-2 block text-sm font-medium">Phone <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', $customer?->phone) }}" maxlength="20" autocomplete="tel" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            @error('phone') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('customers.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">{{ $buttonText }}</button>
    </div>
</form>
