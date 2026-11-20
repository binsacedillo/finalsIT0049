<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ImageStorage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'active');
        $search = trim((string) $request->query('q', ''));

        $staff = User::query()
            ->withCount('sales')
            ->when($status === 'active', fn (Builder $query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn (Builder $query) => $query->where('is_active', false))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();

        return view('staff.index', compact('staff', 'status', 'search'));
    }

    public function create(): View
    {
        return view('staff.create');
    }

    public function store(Request $request, ImageStorage $images): RedirectResponse
    {
        $data = $this->validatedStaff($request, creating: true);
        $avatar = $request->file('avatar');
        unset($data['avatar']);

        if ($avatar) {
            $uploaded = $images->store($avatar, 'avatars');
            $data['avatar'] = $uploaded['location'];
            $data['avatar_file_id'] = $uploaded['file_id'];
        }

        User::create($data);

        return redirect()->route('staff.index')->with('status', 'Staff account created.');
    }

    public function show(User $staff): View
    {
        return view('staff.show', compact('staff'));
    }

    public function edit(User $staff): View
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff, ImageStorage $images): RedirectResponse
    {
        $data = $this->validatedStaff($request, $staff);
        $avatar = $request->file('avatar');
        unset($data['avatar']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($avatar) {
            $oldAvatar = $staff->avatar;
            $oldFileId = $staff->avatar_file_id;
            $uploaded = $images->store($avatar, 'avatars');
            $data['avatar'] = $uploaded['location'];
            $data['avatar_file_id'] = $uploaded['file_id'];
            $staff->update($data);

            $images->delete($oldAvatar, $oldFileId);
        } else {
            $staff->update($data);
        }

        return redirect()->route('staff.show', $staff)->with('status', 'Staff account updated.');
    }

    public function destroy(User $staff, ImageStorage $images): RedirectResponse
    {
        if ($staff->is(auth()->user())) {
            return back()->withErrors(['staff' => 'You cannot delete your own signed-in account.']);
        }

        if ($staff->sales()->exists()) {
            return back()->withErrors(['staff' => 'This staff member has sales in history. Deactivate the account instead.']);
        }

        $avatar = $staff->avatar;
        $avatarFileId = $staff->avatar_file_id;
        $staff->delete();

        $images->delete($avatar, $avatarFileId);

        return redirect()->route('staff.index')->with('status', 'Staff account deleted.');
    }

    public function toggleStatus(User $staff): RedirectResponse
    {
        if ($staff->is(auth()->user())) {
            return back()->withErrors(['staff' => 'You cannot deactivate your own signed-in account.']);
        }

        $staff->update(['is_active' => ! $staff->is_active]);

        $status = $staff->is_active ? 'activated' : 'deactivated';

        return redirect()->route('staff.index', ['status' => $staff->is_active ? 'active' : 'inactive'])
            ->with('status', "Staff account {$status}.");
    }

    /** @return array<string, mixed> */
    private function validatedStaff(Request $request, ?User $staff = null, bool $creating = false): array
    {
        return $request->validate([
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($staff?->id),
            ],
            'full_name' => ['required', 'string', 'max:100'],
            'password' => [$creating ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
