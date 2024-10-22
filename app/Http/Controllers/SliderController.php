<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;

class SliderController extends Controller
{
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function index(Request $request){
        try{
            $row = 5;
            $columns = array(
                'image',
                'name',
                'description'
            );
            $pageName = 'Slider';
            $page = 1;
            if ($request){
                $row = $request->row;
                $pageName = $request->pageName;
                $page = $request->page;
            }

            $slider = Cache::remember('sliderimage_003'.$row, 24*60, function () use ($row, $columns, $pageName, $page) {
                return Slider::where('client', null)
                                ->orderBy('order', 'asc')
                                ->where('active', 1)
                                ->paginate($row, $columns, $pageName, $page);
            });
            //dd($slider);
        } catch (Exception $e){
            return response()->json(['status' => 'error', 'message' => 'failed get slider']);
        }
        $pathImg = 'admin.authenticguards.com/storage/';
            $extns = ".jpg";
        return response()->json(['status' => 'success', 'result' => $slider,'pathImg'=>$pathImg,'extensn'=>$extns]);
    }

    public function show(Request $request, $id){
        return Slider::find($id);
    }
}
