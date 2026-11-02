<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = ['borrowing_transaction_id', 'amount', 'status'];

    public function borrowingTransaction() {
        return $this->belongsTo(BorrowingTransaction::class);
    }

    public function payments() {
        return $this->hasMany(FinePayment::class);
    }
}