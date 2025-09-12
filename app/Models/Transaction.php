<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['customer_id', 'province_id', 'city_id', 'district_id', 'invoice', 'weight', 'address', 'total', 'status', 'snap_token'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }
}
