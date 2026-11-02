<?php

namespace App\Services;

use App\Models\Author;
use App\Models\BookCopy;
use App\Models\BorrowingTransaction;
use App\Models\BorrowingRequest;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LibraryService
{
    /**
     * Approves a request and issues a book with row locking to prevent double booking.
     */
    public function issueBook(BorrowingRequest $request, string $copyBarcode): BorrowingTransaction
    {
        return DB::transaction(function () use ($request, $copyBarcode) {
            // 1. Lock the specific copy for update to prevent race conditions
            $copy = BookCopy::where('barcode', $copyBarcode)
                ->where('book_id', $request->book_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($copy->status !== 'available') {
                throw new \Exception("This copy is currently {$copy->status}.");
            }

            // 2. Mark request as fulfilled
            $request->update(['status' => 'fulfilled']);

            // 3. Update copy status
            $copy->update(['status' => 'borrowed']);

            // 4. Create transaction (Configurable loan period, assuming 14 days default)
            $loanDays = DB::table('library_settings')->where('key', 'loan_period_days')->value('value') ?? 14;

            $transaction = BorrowingTransaction::create([
                'user_id' => $request->user_id,
                'book_copy_id' => $copy->id,
                'borrow_date' => now()->toDateString(),
                'due_date' => now()->addDays((int)$loanDays)->toDateString(),
                'status' => 'active',
            ]);

            // 5. Audit Log
            $this->logActivity(Auth::id(), 'issued_book', "Issued copy {$copyBarcode} to user {$request->user_id}");
            return $transaction;
        });
    }

    /**
     * Processes a return and calculates potential fines.
     */
    public function processReturn(BorrowingTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $transaction = BorrowingTransaction::lockForUpdate()->find($transaction->id);

            if ($transaction->status === 'returned') {
                throw new \Exception("This book is already returned.");
            }

            $returnDate = now()->toDateString();
            $status = 'returned';

            // Check for overdue fines
            if (Carbon::parse($transaction->due_date)->isPast()) {
                $daysOverdue = Carbon::parse($transaction->due_date)->diffInDays(now());
                $dailyRate = DB::table('library_settings')->where('key', 'daily_fine_rate')->value('value') ?? 1.00;

                Fine::create([
                    'borrowing_transaction_id' => $transaction->id,
                    'amount' => $daysOverdue * $dailyRate,
                    'status' => 'unpaid'
                ]);
            }

            $transaction->update([
                'return_date' => $returnDate,
                'status' => $status
            ]);

            // Free up the book copy
            $transaction->bookCopy()->update(['status' => 'available']);

            $this->logActivity(Author::find($transaction->user_id)->id(), 'processed_return', "Processed return for transaction {$transaction->id}");
        });
    }

    public function logActivity(?int $userId, string $action, string $description): void
    {
        DB::table('activity_logs')->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}