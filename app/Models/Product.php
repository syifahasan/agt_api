<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductCategories;

class Product extends Model
{
    protected $table = 'agproductclient';

    protected $fillable = ['image','nama','material','color','price','size','expireDate','agClientBrand_id','distributedOn','image2','image3','linkshop'];
    protected $dates = ['deleted_at'];

    protected $with = ['brand', 'categories'];

    public function code(){
        return $this->belongsToMany('App\Models\Code','agproductclient_agcodes','agProductClient_id','agCodes_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'agClientBrand_id');
    }

    public function categories(){
        return $this->belongsTo(ProductCategories::class, 'agproductclient_category', 'agproductclient_id', 'agproductcategory_id');
    }

}
