<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BorrowingTransaction;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $role = $user->role->name;

        if ($role === 'Admin') {
            return $this->adminDashboard();
        } elseif ($role === 'Librarian') {
            return $this->librarianDashboard();
        } else {
            return $this->memberDashboard($user);
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_members' => User::whereHas('role', fn($q) => $q->where('name', 'Member'))->count(),
            'total_books' => Book::count(),
            'available_copies' => BookCopy::where('status', 'available')->count(),
            'active_loans' => BorrowingTransaction::where('status', 'active')->count(),
            'overdue_loans' => BorrowingTransaction::where('status', 'active')->where('due_date', '<', Carbon::today())->count(),
            'outstanding_fines' => Fine::where('status', 'unpaid')->sum('amount'),
        ];

        // Chart Data: Borrowing trends last 6 months
        $trendData = BorrowingTransaction::select(
                DB::raw('DATE_FORMAT(borrow_date, "%b %Y") as month'), 
                DB::raw('COUNT(*) as total')
            )
            ->where('borrow_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderByRaw('MIN(borrow_date)')
            ->pluck('total', 'month');

        $recentTransactions = BorrowingTransaction::with(['user', 'bookCopy.book'])->latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'trendData', 'recentTransactions'));
    }

    private function librarianDashboard()
    {
        // 1. Calculate today's stats
        $issuedToday = BorrowingTransaction::whereDate('borrow_date', Carbon::today())->count();
        $returnedToday = BorrowingTransaction::whereDate('return_date', Carbon::today())->count();

        // 2. Generate the graph data for the last 7 days
        $dates = [];
        $issuedData = [];
        $returnedData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('M d'); 
            
            $issuedData[] = BorrowingTransaction::whereDate('borrow_date', $date->toDateString())->count();
            $returnedData[] = BorrowingTransaction::whereDate('return_date', $date->toDateString())->count();
        }

        return view('dashboard.librarian', compact(
            'issuedToday', 
            'returnedToday', 
            'dates', 
            'issuedData', 
            'returnedData'
        ));
    }

    private function memberDashboard($user)
    {
        $activeLoans = BorrowingTransaction::with('bookCopy.book')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('due_date')
            ->get();
            
        $fines = Fine::whereHas('borrowingTransaction', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'unpaid')
            ->sum('amount');

        return view('dashboard.member', compact('activeLoans', 'fines'));
    }
}