<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\cache;

class UserController extends Controller
{
    // index: normal view OR AJAX (mengembalikan partial rows + pagination)
    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('q') && $request->q !== '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Filter by online status
        if ($request->has('status') && $request->status !== '') {
            // This is complex, we'll handle in frontend
        }

        $users = $query->paginate(15);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'rows' => view('admin.users.partials.rows', compact('users'))->render(),
                'pagination' => view('admin.users.partials.pagination', compact('users'))->render(),
                'total' => $users->total()
            ]);
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show single user (with relationship)
     */
    public function show(User $user)
    {
        // Load approver relationship
        $user->load('approver');

        return response()->json($user);
    }

    /**
     * Store new user (with file uploads)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users'],
            'password' => ['required','string','min:8','confirmed'],
            'role' => ['required', Rule::in(['Admin','Officer','Member'])],
            'phone' => ['nullable','string','max:20'],
            'birth_date' => ['nullable','date','before:today'],
            'address' => ['nullable','string'],
            'nik' => ['required','string','size:16','unique:users,nik'],
            'ktp_photo' => ['required','image','mimes:jpg,jpeg,png','max:2048'],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Handle file uploads
        if ($request->hasFile('ktp_photo')) {
            $validated['ktp_photo'] = $request->file('ktp_photo')->store('ktp_photos', 'public');
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('profile_photos', 'public');
        }

        // Track who created this user
        $validated['approved_by'] = auth()->id();
        $validated['approved_at'] = now();
        $validated['email_verified_at'] = now();

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        // Prevent admin from demoting themselves
        if ($user->id === auth()->id() && $request->role !== $user->role && $user->role === 'Admin') {
            return response()->json(['error' => 'You cannot remove your own Admin role.'], 422);
        }

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['Admin','Officer','Member'])],
            'password' => ['nullable','string','min:8','confirmed'],
            'phone' => ['nullable','string','max:20'],
            'birth_date' => ['nullable','date','before:today'],
            'address' => ['nullable','string'],
            'nik' => ['nullable','string','size:16', Rule::unique('users')->ignore($user->id)],
            'ktp_photo' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Handle file uploads
        if ($request->hasFile('ktp_photo')) {
            // Delete old file if exists
            if ($user->ktp_photo && Storage::disk('public')->exists($user->ktp_photo)) {
                Storage::disk('public')->delete($user->ktp_photo);
            }
            $validated['ktp_photo'] = $request->file('ktp_photo')->store('ktp_photos', 'public');
        }

        if ($request->hasFile('photo')) {
            // Delete old file if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('profile_photos', 'public');
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Delete single user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'You cannot delete yourself.'], 422);
        }

        // Delete associated files
        if ($user->ktp_photo && Storage::disk('public')->exists($user->ktp_photo)) {
            Storage::disk('public')->delete($user->ktp_photo);
        }
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        if ($user->member_card_photo && Storage::disk('public')->exists($user->member_card_photo)) {
            Storage::disk('public')->delete($user->member_card_photo);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    /**
     * Bulk delete users
     */
    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No users selected.'], 422);
        }

        if (in_array(auth()->id(), $ids)) {
            return response()->json(['error' => 'You cannot delete yourself.'], 422);
        }

        $users = User::whereIn('id', $ids)->get();

        foreach ($users as $user) {
            // Delete associated files
            if ($user->ktp_photo && Storage::disk('public')->exists($user->ktp_photo)) {
                Storage::disk('public')->delete($user->ktp_photo);
            }
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            if ($user->member_card_photo && Storage::disk('public')->exists($user->member_card_photo)) {
                Storage::disk('public')->delete($user->member_card_photo);
            }

            $user->delete();
        }

        return response()->json(['message' => 'Selected users deleted successfully.']);
    }

    /**
     * Get online status for all users
     */
    public function onlineStatus()
    {
        $users = User::all();
        $status = [];

        foreach ($users as $user) {
            $status[$user->id] = $user->isOnline();
        }

        return response()->json($status);
    }
}
