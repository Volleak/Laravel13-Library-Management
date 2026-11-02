<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Admin: View all users (Staff, Librarians, Admins)
    public function index(Request $request)
    {
        $users = User::with(['role', 'profile'])
            ->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('role_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Fetch roles to use in the Add/Edit modals
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    // Admin: Show the form to create a new user
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // Admin: Store a newly created user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' 
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            // Handle Photo Upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('users', 'public');
            }

            // Create Profile for the image
            $user->profile()->create([
                'photo_path' => $photoPath,
                'status' => 'active',
                'barcode' => 'USR-' . str_pad($user->id, 5, '0', STR_PAD_LEFT), // Fallback barcode
            ]);
        });

        return redirect()->route('users.index')->with('success', 'System user created successfully.');
    }

    // Admin: Show the form to edit an existing user
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    // Admin: Update a user's details and role
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' 
        ]);

        DB::transaction(function () use ($request, $user) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
            ];

            // Only update the password if the admin typed a new one
            if ($request->filled('password')) {
                $request->validate(['password' => 'string|min:8']);
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // Handle Photo Upload & Replacement
            $photoPath = $user->profile->photo_path ?? null;
            if ($request->hasFile('photo')) {
                // Delete old photo if it exists
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('users', 'public');
            }

            // Update or Create Profile safely
            $profile = $user->profile()->first();
            
            if ($profile) {
                // If they have a profile, just update the photo
                $profile->update(['photo_path' => $photoPath]);
            } else {
                // If they don't have one, create it and assign the required barcode and status
                $user->profile()->create([
                    'photo_path' => $photoPath,
                    'status' => 'active',
                    'barcode' => 'USR-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Admin: Safely delete a user
    public function destroy(User $user)
    {
        // Prevent admins from accidentally deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($user) {
            // Delete the physical image file
            if ($user->profile && $user->profile->photo_path) {
                Storage::disk('public')->delete($user->profile->photo_path);
            }

            // Delete profile and user
            if ($user->profile) {
                $user->profile->delete();
            }
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'User removed successfully.');
    }
}