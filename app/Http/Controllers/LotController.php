<?php

namespace App\Http\Controllers;

use App\Http\Resources\Lot as ResourcesLot;
use App\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    //
    public function index() {
        return ResourcesLot::collection(Lot::where("status",1)->get());
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

    public function show($id) {
        $lot = Lot::findOrFail($id);
        if($lot == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesLot($lot);
    }

    public function list(Request $request) {
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
