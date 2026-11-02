<?php
namespace App\Http\Controllers;

use App\Models\BorrowingRequest;
use App\Models\Book;
use App\Services\LibraryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingRequestController extends Controller
{
    // View requests (Staff see all, Members see their own)
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $query = BorrowingRequest::with(['user', 'book']);
        
        // Members only see their own requests
        if ($user->hasRole('Member')) {
            $query->where('user_id', $user->id);
        }
        
        $requests = $query->latest()->paginate(15);
        return view('borrowing.requests', compact('requests'));
    }

    // Member: Submit a new borrowing request
    public function store(Request $request, Book $book)
    {
        $userId = Auth::id();

        // Check if member already has a pending request for this specific book
        $exists = BorrowingRequest::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have an active request for this book.');
        }

        BorrowingRequest::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Borrowing request submitted successfully.');
    }

    // Staff: Approve a request and issue the physical book
    public function approve(Request $request, BorrowingRequest $borrowingRequest, LibraryService $service)
    {
        $request->validate([
            'barcode' => 'required|exists:book_copies,barcode'
        ]);
        
        try {
            // Utilizes the DB Transaction & Row Locking we implemented in the LibraryService
            $service->issueBook($borrowingRequest, $request->barcode);
            return back()->with('success', 'Request fulfilled and book issued successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Staff: Reject a request
    public function reject(BorrowingRequest $borrowingRequest)
    {
        $borrowingRequest->update(['status' => 'rejected']);
        return back()->with('success', 'Request rejected.');
    }
}