<?php

namespace App\Http\Controllers;

use App\Models\BorrowingTransaction;
use App\Services\LibraryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added Auth facade import

class BorrowingController extends Controller
{
    // Staff: View all transactions
    public function index(Request $request)
    {
        $transactions = BorrowingTransaction::with(['user', 'bookCopy.book'])
            ->when($request->search, function ($q, $search) {
                $q->whereHas('user', fn($userQ) => $userQ->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('bookCopy', fn($copyQ) => $copyQ->where('barcode', 'like', "%{$search}%"));
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('borrowing.index', compact('transactions'));
    }

    // Member: View own loans
    public function myLoans()
    {
        $activeLoans = BorrowingTransaction::with(['bookCopy.book'])
            ->where('user_id', Auth::id()) // Changed to Auth::id()
            ->where('status', 'active')
            ->orderBy('due_date', 'asc')
            ->get();

        $pastLoans = BorrowingTransaction::with(['bookCopy.book'])
            ->where('user_id', Auth::id()) // Changed to Auth::id()
            ->whereIn('status', ['returned', 'lost'])
            ->orderBy('return_date', 'desc')
            ->paginate(10);

        return view('borrowing.my_loans', compact('activeLoans', 'pastLoans'));
    }

    // Staff: Process a return
    public function processReturn(Request $request, BorrowingTransaction $transaction, LibraryService $service)
    {
        try {
            $service->processReturn($transaction);
            return back()->with('success', 'Book returned successfully. Any applicable fines have been calculated.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}