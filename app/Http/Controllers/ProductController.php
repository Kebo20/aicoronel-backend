<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product as ResourcesProduct;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    public function index() {
        return ResourcesProduct::collection(Product::where("status",1)->get());
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric'

        ]);

        $exist_product = Product::where('name', $request->name)->first();
        if($exist_product != null)
            return response()->json([
                'message' => 'Ya existe un nombre con el mismo nombre.'
            ], 400);

            if($request->price<1){
                return response()->json([
                    'message' => 'El precio debe ser maypr a 0'
                ], 400);
            }

            DB::beginTransaction();

            $product = new Product();
            $product->name = strip_tags($request->name);
            $product->price = strip_tags($request->price);
            $product->brand = strip_tags($request->brand);
            $product->units = strip_tags($request->units);
            $product->id_category =$request->id_category?strip_tags($request->id_category):null;
            $product->created_by = auth()->id();
            $product->updated_by = auth()->id();
            $product->save();


            DB::commit();
            return response()->json([
                'message' => 'Producto registrado.',
                'id_product' => $product->id_product
            ], 201);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric'        ]);

            if($request->price<1){
                return response()->json([
                    'message' => 'El precio debe ser maypr a 0'
                ], 400);
            }


            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->price = $request->price;
            $product->brand = $request->brand;
            $product->units = $request->units;
            $product->id_category =$request->id_category?strip_tags($request->id_category):null;
            $product->updated_by = auth()->id();
            $product->save();


            DB::commit();
            return response()->json([
                'message' => 'Producto actualizado.',
            ], 200);
    }

    public function destroy($id) {

        $product = Product::findOrFail($id);
        if($product == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

            DB::beginTransaction();

            $product->status = 0;
            $product->save();

            DB::commit();
            return response()->json([
                'message' => 'Producto eliminado.',
            ], 200);
    }

    public function show($id) {
        $product = Product::findOrFail($id);
        if($product == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesProduct($product);
    }

    public function list(Request $request) {
        return ResourcesProduct::collection(Product::where("name", "like", "%".$request->search."%")->get());
    }
}
