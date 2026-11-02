<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    // Only admins can manage users. 
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool { return false; }
    public function create(User $user): bool { return false; }
    
    public function update(User $user, User $model): Response
    {
        // Prevent Librarians/Members from editing Admins, and Admins from demoting themselves
        if ($model->hasRole('Admin') && $user->id !== $model->id) {
            return Response::deny('You cannot modify another administrator.');
        }
        return Response::allow();
    }
}