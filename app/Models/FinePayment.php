<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinePayment extends Model
{
    protected $fillable = [
        'fine_id', 
        'amount', 
        'payment_date', 
        'payment_method', 
        'processed_by_id', 
        'reference_number'
    ];

    public function fine() {
        return $this->belongsTo(Fine::class);
    }

    public function processedBy() {
        return $this->belongsTo(User::class, 'processed_by_id');
    }
}