<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['image', 'link'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
