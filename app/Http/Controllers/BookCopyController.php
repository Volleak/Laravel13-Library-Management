<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    // Add a new physical copy to a book's inventory
    public function store(Request $request, Book $book)
    {
        // Count existing copies to generate a sequential barcode
        $copyNumber = $book->copies()->count() + 1;
        
        // Generate a barcode like: BK-0012-CPY-001
        $barcode = 'BK-' . str_pad($book->id, 4, '0', STR_PAD_LEFT) . '-CPY-' . str_pad($copyNumber, 3, '0', STR_PAD_LEFT);

        // Create the new physical copy in the database
        $book->copies()->create([
            'barcode' => $barcode,
            'status' => 'available', // Ready to be borrowed
            'condition' => 'New',
        ]);

        return back()->with('success', '1 new copy added to inventory! Barcode: ' . $barcode);
    }

    // Update the status of a specific copy (e.g., lost, damaged)
    public function updateStatus(Request $request, BookCopy $copy)
    {
        $request->validate([
            'status' => 'required|in:available,borrowed,lost,damaged'
        ]);

        $copy->update(['status' => $request->status]);

        return back()->with('success', 'Copy status updated to ' . $request->status);
    }
}