<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upazila extends Model
{
    protected $fillable = [
        'district_id', 'name'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function orderAddress()
    {
        return $this->hasMany(OrderAddress::class);
    }
}
