<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
   public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');
        
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048'
        ]);

        // Update the main user record
        $user->update(['name' => $request->name]);
        
        // Prepare profile-specific data
        $profileData = $request->only(['phone', 'address']);
        
        // Handle photo upload if a file was provided
        if ($request->hasFile('photo')) {
            $profileData['photo_path'] = $request->file('photo')->store('profiles', 'public');
        }

        // Update or create the attached member_profile record
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id], 
            $profileData
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}