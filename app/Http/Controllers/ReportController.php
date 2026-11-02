<?php
namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\BorrowingTransaction;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard()
    {
        // Analytics directly from MySQL
        $stats = [
            'total_books' => DB::table('books')->count(),
            'total_copies' => BookCopy::count(),
            'available_copies' => BookCopy::where('status', 'available')->count(),
            'active_loans' => BorrowingTransaction::where('status', 'active')->count(),
            'overdue_loans' => BorrowingTransaction::where('status', 'overdue')
                                ->orWhere(function($query) {
                                    $query->where('status', 'active')
                                          ->where('due_date', '<', now()->toDateString());
                                })->count(),
            'total_fines_outstanding' => Fine::whereIn('status', ['unpaid', 'partial'])->sum('amount'),
            'total_members' => User::whereHas('role', fn($q) => $q->where('name', 'Member'))->count(),
        ];

        // Borrowing trends (Last 6 months)
        $trends = BorrowingTransaction::selectRaw('DATE_FORMAT(borrow_date, "%Y-%m") as month, count(*) as total')
            ->where('borrow_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.reports', compact('stats', 'trends'));
    }
}