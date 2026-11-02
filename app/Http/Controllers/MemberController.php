<?php
namespace App\Http\Controllers;

use App\Models\BorrowingTransaction;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    // Staff: View all members with search and filtering
    public function index(Request $request)
    {
        $members = User::whereHas('role', fn($q) => $q->where('name', 'Member'))
            ->with('profile')
            ->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', fn($profileQ) => $profileQ->where('barcode', 'like', "%{$search}%"));
            })
            ->paginate(15);
            
        return view('members.index', compact('members'));
    }

    // Staff: Show member creation form (if not using a modal)
    public function create()
    {
        return view('members.create');
    }

    // Staff: Register a new member manually
public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Wrap the creation process in a database transaction
        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role_id' => 3, 
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('members', 'public');
            }

            \App\Models\MemberProfile::create([
                'user_id' => $user->id,
                'barcode' => 'MEM-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                'phone' => $request->phone,
                'address' => $request->address,
                'dob' => $request->dob,
                'photo_path' => $photoPath,
                'status' => $request->status,
            ]);

        }); // End transaction

        return redirect()->route('dashboard')->with('success', 'New member registered successfully!');
    }

    // ==========================================
    // VIEW MEMBER DETAILS
    // ==========================================
    public function show($id)
    {
        $member = \App\Models\User::with('profile')->findOrFail($id);
        return view('members.show', compact('member'));
    }

    // ==========================================
    // SHOW EDIT FORM
    // ==========================================
    public function edit($id)
    {
        $member = \App\Models\User::with('profile')->findOrFail($id);
        return view('members.edit', compact('member'));
    }

    // ==========================================
    // PROCESS UPDATE
    // ==========================================
    public function update(Request $request, $id)
    {
        $member = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $member->id, // Ignore current user's email
            'password' => 'nullable|string|min:8|confirmed', // Password is optional when editing
            'phone' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $member) {
            // Update User login details
            $member->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Only update password if the user typed a new one
            if ($request->filled('password')) {
                $member->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);
            }

            // Handle new photo upload
            $photoPath = $member->profile->photo_path ?? null;
            if ($request->hasFile('photo')) {
                // Delete old photo if it exists
                if ($photoPath) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('members', 'public');
            }

            // Update or Create the Profile record
            $member->profile()->updateOrCreate(
                ['user_id' => $member->id],
                [
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'dob' => $request->dob,
                    'photo_path' => $photoPath,
                    'status' => $request->status,
                ]
            );
        });

        return redirect()->route('members.index')->with('success', 'Member updated successfully!');
    }

    // ==========================================
    // DELETE MEMBER
    // ==========================================
    public function destroy(User $member)
{
    // 1. Check if the member has any borrowing history
    $hasHistory = BorrowingTransaction::where('user_id', $member->id)->exists();

    if ($hasHistory) {
        return redirect()->back()->with('error', 'Cannot delete this member because they have active or past borrowing transactions. Please deactivate their account instead.');
    }

    // 2. If no history exists, proceed with your original deletion logic
    if ($member->profile) {
        $member->profile->delete();
    }
    
    $member->delete();

    return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
}

    
}