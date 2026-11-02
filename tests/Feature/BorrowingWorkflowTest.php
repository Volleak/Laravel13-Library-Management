<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookCopy;
use App\Models\BorrowingRequest;
use App\Services\LibraryService;

class BorrowingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_issue_book_with_row_locking()
    {
        // Setup data
        $librarianRole = Role::create(['name' => 'Librarian']);
        $memberRole = Role::create(['name' => 'Member']);
        
        $librarian = User::factory()->create(['role_id' => $librarianRole->id]);
        $member = User::factory()->create(['role_id' => $memberRole->id]);
        
        $category = $category::create(['name' => 'Science', 'slug' => 'science']);
        $book = Book::create(['title' => 'Physics 101', 'category_id' => $category->id]);
        $copy = BookCopy::create(['book_id' => $book->id, 'barcode' => 'CPY-001', 'status' => 'available']);
        
        $borrowRequest = BorrowingRequest::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'pending'
        ]);

        // Execute Service Logic mimicking the controller
        $service = new LibraryService();
        $this->actingAs($librarian);
        $transaction = $service->issueBook($borrowRequest, 'CPY-001');

        // Assertions
        $this->assertDatabaseHas('borrowing_transactions', [
            'id' => $transaction->id,
            'user_id' => $member->id,
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('book_copies', [
            'id' => $copy->id,
            'status' => 'borrowed'
        ]);

        $this->assertDatabaseHas('borrowing_requests', [
            'id' => $borrowRequest->id,
            'status' => 'fulfilled'
        ]);
        
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $librarian->id,
            'action' => 'issued_book'
        ]);
    }
}