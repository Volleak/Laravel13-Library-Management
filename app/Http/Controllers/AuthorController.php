<?php
namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // Staff: View all authors
    public function index(Request $request)
    {
        $authors = Author::withCount('books')
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(15);
            
        return view('authors.index', compact('authors'));
    }

    // Staff: Add a new author
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string'
        ]);
        
        Author::create($validated);
        
        return back()->with('success', 'Author added successfully.');
    }

    // Staff: Update an existing author
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string'
        ]);
        
        $author->update($validated);
        
        return back()->with('success', 'Author updated successfully.');
    }

    // Staff: Safely delete an author
    public function destroy(Author $author)
    {
        // Prevent deleting if the author is attached to any books in the catalog
        if ($author->books()->exists()) {
            return back()->with('error', 'Cannot delete this author because they have books in the catalog.');
        }
        
        $author->delete();
        
        return back()->with('success', 'Author deleted successfully.');
    }
}