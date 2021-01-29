<?php

namespace App\Http\Controllers;

use App\Http\Resources\Storage as ResourcesStorage;
use App\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageController extends Controller
{
    //
    public function index() {
        return ResourcesStorage::collection(Storage::where("status",1)->get());
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|max:255'
        ]);

        $exist_storage = Storage::where('name', $request->name)->first();
        if($exist_storage != null)
            return response()->json([
                'message' => 'Ya existe un almacén con el mismo nombre.'
            ], 400);

            DB::beginTransaction();

            $storage = new Storage();
            $storage->name = strip_tags($request->name);
            $storage->address = strip_tags($request->address);
            $storage->responsable = strip_tags($request->responsable);
            $storage->created_by = auth()->id();
            $storage->updated_by = auth()->id();
            $storage->save();


            DB::commit();
            return response()->json([
                'message' => 'Almacén registrado.',
                'id_storage' => $storage->id_storage
            ], 201);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'required|max:255'
        ]);

            DB::beginTransaction();

            $storage = Storage::findOrFail($id);
            $storage->name = $request->name;
            $storage->address = $request->address;
            $storage->responsable = $request->responsable;
            $storage->updated_by = auth()->id();
            $storage->save();


            DB::commit();
            return response()->json([
                'message' => 'Almacén actualizado.',
            ], 200);
    }

    public function destroy($id) {

        $storage = Storage::findOrFail($id);
        if($storage == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

            DB::beginTransaction();

            $storage->status = 0;
            $storage->save();

            DB::commit();
            return response()->json([
                'message' => 'Almacén eliminado.',
            ], 200);
    }

    public function show($id) {
        $storage = Storage::findOrFail($id);
        if($storage == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesStorage($storage);
    }

    public function list(Request $request) {
        return ResourcesStorage::collection(Storage::where("name", "like", "%".$request->search."%")->get());
    }
}
