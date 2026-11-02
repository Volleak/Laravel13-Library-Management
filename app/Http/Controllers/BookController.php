<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Http\Requests\StoreBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index(Request $request)
    {
        // DB-backed search, filter, sort
        $books = Book::with(['category', 'authors'])
            ->withCount(['copies', 'copies as available_copies' => function ($query) {
                $query->where('status', 'available');
            }])
            ->when($request->search, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            })
            ->when($request->category_id, fn($q, $cat) => $q->where('category_id', $cat))
            ->orderBy($request->sort ?? 'title', $request->direction ?? 'asc')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();
        return view('books.index', compact('books', 'categories'));
    }

    // Show the form to add a new book
    public function create()
    {
        $categories = Category::all();
        $authors = Author::orderBy('name', 'asc')->get();

        return view('books.create', compact('categories', 'authors'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'category_id' => 'required|exists:categories,id',
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // 1. Handle the file upload (if provided)
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        // 2. Create the book
        $book = Book::create([
            'title' => $validated['title'],
            'isbn' => $validated['isbn'],
            'category_id' => $validated['category_id'],
            'cover_path' => $coverPath,
        ]);

        // 3. Attach the selected authors to the pivot table
        $book->authors()->attach($validated['author_ids']);

        return redirect()->route('books.index')->with('success', 'Book added successfully!');
    }



    // ==========================================
    // 1. VIEW BOOK DETAILS (Show)
    // ==========================================
    public function show(Book $book)
    {
        // Load the book along with its category and inventory copies
        $book->load(['category', 'copies']);

        return view('books.show', compact('book'));
    }

    // ==========================================
    // 2. SHOW THE EDIT FORM (Edit)
    // ==========================================
    public function edit(Book $book)
    {
        $categories = Category::all();
        $authors = Author::orderBy('name', 'asc')->get();

        // Get an array of IDs for the authors currently assigned to this book
        $selectedAuthors = $book->authors->pluck('id')->toArray();

        return view('books.edit', compact('book', 'categories', 'authors', 'selectedAuthors'));
    }

    // ==========================================
    // 3. PROCESS THE UPDATE (Update)
    // ==========================================
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'category_id' => 'required|exists:categories,id',
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // Handle cover image upload if a new one is provided
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            $book->cover_path = $coverPath;
        }

        $book->update([
            'title' => $validated['title'],
            'isbn' => $validated['isbn'],
            'category_id' => $validated['category_id'],
        ]);

        // Sync updates the pivot table. It adds new authors and removes unselected ones.
        $book->authors()->sync($validated['author_ids']);

        return redirect()->route('books.index')->with('success', 'Book updated successfully!');
    }

    // ==========================================
    // 4. DELETE THE BOOK (Destroy)
    // ==========================================
    public function destroy(Book $book)
    {
        // Delete the cover image from storage to save space
        if ($book->cover_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($book->cover_path);
        }

        // Delete the database record
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Book deleted successfully!');
    }
}