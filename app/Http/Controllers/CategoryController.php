<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Staff: View all categories
    public function index(Request $request)
    {
        $categories = Category::withCount('books')
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(15);
            
        return view('categories.index', compact('categories'));
    }

    // Staff: Create a new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);
        
        // Automatically generate a URL-friendly slug from the name
        $validated['slug'] = Str::slug($validated['name']);
        
        Category::create($validated);
        
        return back()->with('success', 'Category created successfully.');
    }

    // Staff: Update an existing category
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        $category->update($validated);
        
        return back()->with('success', 'Category updated successfully.');
    }

    // Staff: Delete a category (Safely)
    public function destroy(Category $category)
    {
        // Prevent deleting if books are currently using this category
        if ($category->books()->exists()) {
            return back()->with('error', 'Cannot delete this category because it contains books.');
        }
        
        $category->delete();
        
        return back()->with('success', 'Category deleted successfully.');
    }
}