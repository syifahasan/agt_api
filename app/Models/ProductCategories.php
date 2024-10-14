<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductCategories extends Model
{
    protected $table = 'agproductcategory';

    protected $fillable = ['name'];
    protected $dates = ['deleted_at'];

    protected $with = ['products'];

    public function products(){
        return $this->belongsToMany(Product::class, 'agproductclient_category', 'agproductcategory_id', 'agproductclient_id');
    }
}
