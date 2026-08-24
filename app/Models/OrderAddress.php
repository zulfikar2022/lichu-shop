<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id', 'division_id', 'district_id', 'upazila_id', 'name', 'address', 'phone', 'alternative_phone',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class);
    }
}
