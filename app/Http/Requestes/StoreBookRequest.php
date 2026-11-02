<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('Admin') || $this->user()->hasRole('Librarian'); }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'isbn' => 'nullable|string|unique:books,isbn',
            'published_year' => 'nullable|digits:4|integer|min:1800|max:' . date('Y'),
            'publisher' => 'nullable|string|max:255',
            'shelf_location' => 'nullable|string|max:50',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
            'initial_copies' => 'required|integer|min:0|max:50'
        ];
    }
}