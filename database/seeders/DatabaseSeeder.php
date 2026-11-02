<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $librarianRole = Role::create(['name' => 'Librarian']);
        $memberRole = Role::create(['name' => 'Member']);

        User::create([
            'name' => 'System Admin',
            'email' => 'admin@library.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Head Librarian',
            'email' => 'librarian@library.com',
            'password' => Hash::make('password'),
            'role_id' => $librarianRole->id,
        ]);
        
        $member = User::create([
            'name' => 'John Doe',
            'email' => 'member@library.com',
            'password' => Hash::make('password'),
            'role_id' => $memberRole->id,
        ]);

        $member->profile()->create([
            'barcode' => 'MEM-10001',
            'phone' => '1234567890'
        ]);
    }
}