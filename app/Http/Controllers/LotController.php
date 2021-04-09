<?php

namespace App\Http\Controllers;

use App\Http\Resources\Lot as ResourcesLot;
use App\Http\Resources\MoveProduct as ResourcesMoveProduct;
use App\Lot;
use App\MoveProduct;
use App\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    //
    public function index()
    {
        return ResourcesLot::collection(Lot::where("status", 1)->get());
    }

    public function listMovementInitialByStorage($id)
    {
        $move_product = MoveProduct::select('moves_product.*')->join('lots', 'lots.id_lot', '=', 'moves_product.id_lot')->join('storages', 'lots.id_storage', '=', 'storages.id_storage')
            ->where("moves_product.table_reference", "initial")->where("storages.id_storage", $id)->get();
        return ResourcesMoveProduct::collection($move_product);
    }


    public function add(Request $request)
    {

        $validatedData = $request->validate([
            'quantity' => 'required|numeric',
            'id_product' => 'required',
            'id_storage' => 'required'
        ]);
        try {

            DB::beginTransaction();
            $product = Product::where('id_product', $request->id_product)->where('status', 1)->first();
            if ($product == null) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Producto no existe'
                ], 400);
            }

            $lot_old = Lot::where('id_product', $request->id_product)->where('id_storage', $request->id_storage)->first();
            if ($lot_old == null) {
                $lot_new = new Lot();
                $lot_new->quantity = strip_tags($request->quantity);
                $lot_new->id_product = $request->id_product;
                $lot_new->id_storage = $request->id_storage;
                $lot_new->created_by = auth()->id();
                $lot_new->updated_by = auth()->id();
                $lot_new->save();
                $lot = $lot_new;
            } else {
                $lot_old->quantity += strip_tags($request->quantity);
                $lot_old->updated_by = auth()->id();
                $lot_old->save();

                $lot = $lot_old;
            }

            $move_product = new MoveProduct();
            $move_product->date = date('Y-m-d');
            $move_product->type = "entrada";
            $move_product->stock = $lot->quantity;
            $move_product->quantity = strip_tags($request->quantity);
            $move_product->price = 0;
            $move_product->total_cost = 0;
            $move_product->table_reference = 'initial';
            $move_product->id_product = $product->id_product;
            $move_product->id_lot = $lot->id_lot;
            $move_product->id_reference = null;
            $move_product->created_by = auth()->id();
            $move_product->updated_by = auth()->id();
            $move_product->save();

            DB::commit();
        } catch (Exception $e) {
        }
    }

    public function remove(Request $request)
    {

        $validatedData = $request->validate([
            'quantity' => 'required|numeric',
            'id_product' => 'required',
            'id_storage' => 'required'
        ]);
        try {

            DB::beginTransaction();
            $product = Product::where('id_product', $request->id_product)->where('status', 1)->first();
            if ($product == null) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Producto no existe'
                ], 400);
            }

            $lot_old = Lot::where('id_product', $request->id_product)->where('id_storage', $request->id_storage)->first();
            if ($lot_old == null) {
                DB::rollBack();
                return response()->json([
                    'message' => 'No se puede retirar. Aún no se ha ingresado este producto al almacén'
                ], 400);
            } else {
                if ($request->quantity > $lot_old->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Stock insuficiente para retirar'
                    ], 400);
                }
                $lot_old->quantity -= strip_tags($request->quantity);
                $lot_old->updated_by = auth()->id();
                $lot_old->save();

                $lot = $lot_old;
            }

            $move_product = new MoveProduct();
            $move_product->date = date('Y-m-d');
            $move_product->type = "salida";
            $move_product->stock = $lot->quantity;
            $move_product->quantity = strip_tags($request->quantity);
            $move_product->price = 0;
            $move_product->total_cost = 0;
            $move_product->table_reference = 'initial';
            $move_product->id_product = $product->id_product;
            $move_product->id_lot = $lot->id_lot;
            $move_product->id_reference = null;
            $move_product->created_by = auth()->id();
            $move_product->updated_by = auth()->id();
            $move_product->save();

            DB::commit();
        } catch (Exception $e) {
        }
    }

    /*
    public function store(Request $request) {

            DB::beginTransaction();

            $lot = new Lot();
            $lot->quantity = strip_tags($request->quantity);
            $lot->id_product = strip_tags($request->id_product);
            $lot->id_storage = strip_tags($request->id_storage);
            $lot->created_by = auth()->id();
            $lot->updated_by = auth()->id();
            $lot->save();


            DB::commit();
            return response()->json([
                'message' => 'Lote registrado.',
                'id_lot' => $lot->id_lot
            ], 201);
    }

    public function update(Request $request, $id) {

            DB::beginTransaction();

            $lot = Lot::findOrFail($id);
            $lot->quantity = strip_tags($request->quantity);
            $lot->id_product = strip_tags($request->id_product);
            $lot->id_storage = strip_tags($request->id_storage);
            $lot->updated_by = auth()->id();
            $lot->save();


            DB::commit();
            return response()->json([
                'message' => 'Lote actualizado.',
            ], 200);
    }

    public function destroy($id) {

        $lot = Lot::findOrFail($id);
        if($lot == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

            DB::beginTransaction();

            $lot->status = 0;
            $lot->save();

            DB::commit();
            return response()->json([
                'message' => 'Lote eliminado.',
            ], 200);
    }
    */

    public function show($id)
    {
        $lot = Lot::findOrFail($id);
        if ($lot == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesLot($lot);
    }

    public function list(Request $request)
    {
        if (Auth::user()->id_role == 2) {
            $id_storage = 1;
        }

        if (Auth::user()->id_role == 3) {
            $id_storage = 2;
        }

        if (Auth::user()->id_role == 1) {
            $id_storage = $request->id_storage;
        }
        return ResourcesLot::collection(Lot::where('status', 1)->where('id_storage', $id_storage)->get());
    }
}
