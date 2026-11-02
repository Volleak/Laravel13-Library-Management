<?php
namespace App\Http\Controllers;

use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added Auth import

class FineController extends Controller
{
    // Staff: View all fines
    public function index(Request $request)
    {
        $fines = Fine::with(['borrowingTransaction.user', 'borrowingTransaction.bookCopy.book'])
            ->when($request->search, function ($q, $search) {
                $q->whereHas('borrowingTransaction.user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('fines.index', compact('fines'));
    }

    // Member: View own fines
    public function myFines()
    {
        $fines = Fine::with(['borrowingTransaction.bookCopy.book', 'payments'])
            ->whereHas('borrowingTransaction', fn($q) => $q->where('user_id', Auth::id())) 
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('fines.my_fines', compact('fines'));
    }
}