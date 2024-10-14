<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Api\Firebase\VerifyController as verify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ClaimProduct as Claim;
use App\Models\ProductCategories;
use App\Models\Code;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getCategories()
    {
        $categories = ProductCategories::all();
        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getProductsByCategory($categoryId)
    {
        $category = ProductCategories::with('products')->find($categoryId);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json($category->products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    public function claimProduct(Request $request, $code){
        try{
            if (!isset($request->appid) || $request->appid === null ) {
                return response()->json(['status' => 'Error_appid', 'message' => "Akses Ditolak!!"]);
            }
            if (!isset($request->token) || $request->token === null ) {
                return response()->json(['status' => 'Error_token', 'message' => "Akses Ditolak!!"]);
            }
            if (!isset($request->latitude) || !isset($request->longitude)) {
                return response()->json(['status' => 'error', 'message' => 'Lokasi Anda tidak diketahui, Silahkan Aktifkan GPS.'], 200);
            }

            $user_end = verify::decodeJson($request->token, $request->appid);
            if(!is_object($user_end)){
                return response()->json(['status' => "error", 'message' => "Token Expired"]);
            }

            $codes = Code::getId($code);

            $check = \App\Core\Library::CheckCode($codes);
            if ($check){
                if(\App\Core\Library::CheckClaim($codes)){
                    return response()->json(['status' => 'error', 'message' => 'Code claimed by other', 'result' => $check]);
                }
                $user = \App\Core\Library::getUser($user_end->email);

                if ($user && $check){
                    $data = array(
                        'agProductClient_id' => $check['product']['id'],
                        'agCodes_id' => $check['codeid'],
                        'userId' => @$user->id,
                        'time' => date("Y-m-d H:i:s"),
                        'geoLocLangClaim' => $request->latitude,
                        'geoLocLongClaim' => $request->longitude,
                        'approved' => 1,
                        'photo' => '',
                    );
                    $hasil = null;
                    if(empty($check['pin'])) {
                        $hasil = Claim::create($data);
                        if(!isset($hasil['id'])){
                            return response()->json(['status'=>'error', 'message'=>'Failed to claim product'], 200);
                        }
                    }else{
                        if(!$check['pin'] == $request->pin){
                            return response()->json(['status' => 'error', 'message' => 'Pin invalid'], 400);
                        }
                        $hasil = Claim::create($data);
                        if (!isset($hasil['id'])){
                            return response()->json(['status' => 'error', 'message' => 'Failed to claim product'], 200);
                        }
                    }
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Kode QR Tidak ditemukan']);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'message' => 'Gagal Klaim Produk']);
        }

        return response()->json(['status' => 'success', 'result' => $hasil]);
    }
}
