<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;


    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'category_id');
    // }


    public function products()
    {
        return $this->hasMany(Product::class, 'category_id')
            ->where('active_status', 1)
            ->orderBy('position')
            ->orderBy('product_name');
    }

    public function getSelectedFractions()
    {
        $fracs = explode(",", $this->fractions);

        return WidthHeightFraction::whereIn('fraction_value', $fracs)
            ->orderBy('decimal_value', 'asc')
            ->get();
    }
}
