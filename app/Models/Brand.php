<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Brand extends Model
{
    protected $table='agclientbrand';
    protected $fillable = ['Name','addressOfficeOrStore','csPhone','csEmail','web','twitter','instagram','facebook','whatsapp','line','agClient_id','lat','lon','image','sort_'];
    protected $hidden = [
        'agClient_id',
    ];
    protected $with = ['client'];

    public function client(){
        return $this->belongsTo('App\Models\Client','agClient_id');
    }

    public static function selectOption(){
        $option=[];
        $selectOption=Brand::orderBy('Name','asc')->get();
        foreach($selectOption as $row){
            $option[$row->id]=$row->Name;
        }
        return $option;
    }
}
