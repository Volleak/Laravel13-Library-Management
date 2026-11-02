<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Fiction',
            'Non-Fiction',
            'Science Fiction',
            'Technology',
            'History',
            'Biography',
            'Children',
            'Romance',
            'Mystery & Thriller'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName)
            ]);
        }
    }
}