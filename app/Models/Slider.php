<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Slider extends Model
{
    protected $table='agfrontslider';

    protected $fillable = ['name','image','order','active','client','description'];

    public $timestamps = false;

	public static function getData(){
        $data=DB::table('agfrontslider')
                ->select(['agfrontslider.*','agclient.name as client_name'])
                ->join('agclient','agfrontslider.client','=','agclient.id','left');
        return $data;
    }
}
