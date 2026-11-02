<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookCopy;

class BookSeeder extends Seeder
{
    public function run()
    {
        // 1. First Book: The Great Gatsby
        $book1 = Book::create([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'isbn' => '9780743273565',
            'published_year' => 1925,
            'category' => 'Fiction',
            'description' => 'A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.',
        ]);

        // Create 2 physical copies for Book 1
        BookCopy::create(['book_id' => $book1->id, 'barcode' => 'BK-10001', 'status' => 'available']);
        BookCopy::create(['book_id' => $book1->id, 'barcode' => 'BK-10002', 'status' => 'available']);

        // 2. Second Book: 1984
        $book2 = Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'isbn' => '9780451524935',
            'published_year' => 1949,
            'category' => 'Science Fiction',
            'description' => 'A dystopian social science fiction novel and cautionary tale about the dangers of totalitarianism.',
        ]);

        // Create 3 physical copies for Book 2
        BookCopy::create(['book_id' => $book2->id, 'barcode' => 'BK-20001', 'status' => 'available']);
        BookCopy::create(['book_id' => $book2->id, 'barcode' => 'BK-20002', 'status' => 'borrowed']);
        BookCopy::create(['book_id' => $book2->id, 'barcode' => 'BK-20003', 'status' => 'available']);

        // 3. Third Book: Clean Code
        $book3 = Book::create([
            'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
            'author' => 'Robert C. Martin',
            'isbn' => '9780132350884',
            'published_year' => 2008,
            'category' => 'Technology',
            'description' => 'Even bad code can function. But if code isn\'t clean, it can bring a development organization to its knees.',
        ]);

        // Create 1 physical copy for Book 3
        BookCopy::create(['book_id' => $book3->id, 'barcode' => 'BK-30001', 'status' => 'available']);
    }
}