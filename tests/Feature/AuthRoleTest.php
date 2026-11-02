namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
// This resets the database for every test automatically
use RefreshDatabase;

public function test_public_user_registers_as_member()
{
// 1. Setup: Create the role first so the foreign key doesn't fail
Role::create(['name' => 'Member']);

// 2. Action: Hit the registration route
$response = $this->post('/register', [
'name' => 'Test User',
'email' => 'test@example.com',
'password' => 'password123',
'password_confirmation' => 'password123',
]);

// 3. Assert: Check the database and redirect
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);

$user = User::where('email', 'test@example.com')->first();
$this->assertEquals('Member', $user->role->name);

$response->assertRedirect('/dashboard');
}

// ... your other admin tests
}