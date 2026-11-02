<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable; // 2. Add HasFactory here

    protected $fillable = ['role_id', 'name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function profile(): HasOne { return $this->hasOne(MemberProfile::class); }
    public function borrowingTransactions(): HasMany { return $this->hasMany(BorrowingTransaction::class); }
    
    // Authorization Helpers
    public function hasRole(string $roleName): bool {
        return $this->role->name === $roleName;
    }
}