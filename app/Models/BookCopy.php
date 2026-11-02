<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model {
    protected $fillable = ['book_id', 'barcode', 'status'];
    public function book() { return $this->belongsTo(Book::class); }
    public function transactions() { return $this->hasMany(BorrowingTransaction::class); }
}//