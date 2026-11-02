<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            ['name' => 'J.K. Rowling'],
            ['name' => 'George R.R. Martin'],
            ['name' => 'Stephen King'],
            ['name' => 'Agatha Christie'],
            ['name' => 'J.R.R. Tolkien'],
            ['name' => 'Jane Austen'],
            ['name' => 'Mark Twain'],
            ['name' => 'Haruki Murakami'],
        ];

        foreach ($authors as $author) {
            Author::firstOrCreate($author);
        }
    }
}