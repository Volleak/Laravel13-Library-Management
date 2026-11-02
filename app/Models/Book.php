<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'isbn',
        'category_id',
        'cover_path', // Updated to match database
        'published_year',
        'publisher',
        'shelf_location',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    // 2. Calculate $book->available_copies
    public function getAvailableCopiesAttribute()
    {
        // Make sure the status text matches your database perfectly (e.g., 'available')
        return $this->copies()->where('status', 'available')->count();
    }

    // 3. Calculate $book->copies_count
    public function getCopiesCountAttribute()
    {
        return $this->copies()->count();
    }
}