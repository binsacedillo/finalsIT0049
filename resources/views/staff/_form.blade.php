<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mt-7 space-y-5">
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="full_name" class="mb-2 block text-sm font-medium">Full name</label>
            <input id="full_name" name="full_name" value="{{ old('full_name', $staff?->full_name) }}" required maxlength="100" autocomplete="name" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            @error('full_name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="username" class="mb-2 block text-sm font-medium">Username</label>
            <input id="username" name="username" value="{{ old('username', $staff?->username) }}" required maxlength="50" autocomplete="username" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            @error('username') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="password" class="mb-2 block text-sm font-medium">{{ $staff ? 'New password' : 'Password' }}{{ $staff ? ' (optional)' : '' }}</label>
            <input id="password" name="password" type="password" {{ $staff ? '' : 'required' }} autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            @error('password') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-medium">Confirm password{{ $staff ? ' (if changing)' : '' }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" {{ $staff ? '' : 'required' }} autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
        </div>
    </div>
    <div>
        <label for="avatar" class="mb-2 block text-sm font-medium">Avatar <span class="font-normal text-slate-400">(JPG, PNG, or WebP; up to 2 MB)</span></label>
        @if ($staff?->avatar)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($staff->avatar) }}" alt="Current {{ $staff->full_name }} avatar" class="mb-3 size-16 rounded-full object-cover">
        @endif
        <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-indigo-700">
        @error('avatar') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ $staff ? route('staff.show', $staff) : route('staff.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">{{ $buttonText }}</button>
    </div>
</form>
