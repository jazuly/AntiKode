<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ["name", "logo", "banner"];

    public function outlet() {
        return $this->hasMany(Outlet::class)
            ->selectRaw("id, brand_id, name, trunc((point(-6.175136383245059, 106.82709913722499) <@> (point(longitude, latitude)::point))::NUMERIC * 1.609344, 2) || ' KM' as distance")
            ->orderBy("distance");
    }

    public function product() {
        return $this->hasMany(Product::class);
    }
}
