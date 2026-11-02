<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowingTransaction extends Model
{
    protected $fillable = [
        'user_id', 
        'book_copy_id', 
        'borrow_date', 
        'due_date', 
        'return_date', 
        'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function bookCopy() {
        return $this->belongsTo(BookCopy::class);
    }

    public function fines() {
        return $this->hasMany(Fine::class);
    }
}