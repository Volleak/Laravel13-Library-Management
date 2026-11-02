<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BorrowingTransaction;
use App\Models\User;
use App\Models\BookCopy;
use Carbon\Carbon;

class BorrowingTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all members (role_id 3) and all physical book copies
        $members = User::where('role_id', 3)->get();
        $copies = BookCopy::all();

        if ($members->isEmpty() || $copies->isEmpty()) {
            $this->command->info('You need at least 1 member and 1 book copy to seed transactions.');
            return;
        }

        // 1. Generate Historical Data for the 6-Month Chart (Returned Books)
        for ($i = 0; $i < 40; $i++) {
            $borrowDate = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(1, 28));
            $dueDate = $borrowDate->copy()->addDays(14);
            $returnDate = $dueDate->copy()->subDays(rand(1, 5)); // Returned early or on time

            BorrowingTransaction::create([
                'user_id' => $members->random()->id,
                'book_copy_id' => $copies->random()->id,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'return_date' => $returnDate,
                'status' => 'returned',
            ]);
        }

        // 2. Generate Active Loans (Not overdue yet)
        for ($i = 0; $i < 5; $i++) {
            $borrowDate = Carbon::now()->subDays(rand(1, 10));
            
            BorrowingTransaction::create([
                'user_id' => $members->random()->id,
                'book_copy_id' => $copies->random()->id,
                'borrow_date' => $borrowDate,
                'due_date' => $borrowDate->copy()->addDays(14),
                'return_date' => null,
                'status' => 'active',
            ]);
        }

        // 3. Generate Overdue Books (Due date was in the past)
        for ($i = 0; $i < 3; $i++) {
            $borrowDate = Carbon::now()->subDays(rand(20, 30));
            
            BorrowingTransaction::create([
                'user_id' => $members->random()->id,
                'book_copy_id' => $copies->random()->id,
                'borrow_date' => $borrowDate,
                'due_date' => $borrowDate->copy()->addDays(14),
                'return_date' => null,
                'status' => 'active',
            ]);
        }
    }
}