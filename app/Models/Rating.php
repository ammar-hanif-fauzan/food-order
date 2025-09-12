<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = ['transaction_detail_id', 'customer_id', 'product_id', 'rating', 'review'];

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
