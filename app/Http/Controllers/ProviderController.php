<?php

namespace App\Http\Controllers;

use App\Http\Resources\Provider as ResourcesProvider;
use App\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderController extends Controller
{
    //
    public function index() {
        return ResourcesProvider::collection(Provider::where("status",1)->get());
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'number_doc' => 'required|numeric',
            'name' => 'required|max:255'
        ]);

        $exist_provider = Provider::where('number_doc', $request->number_doc)->first();
        if($exist_provider != null)
            return response()->json([
                'message' => 'Ya existe una empresa con el mismo número de documento.'
            ], 400);

            DB::beginTransaction();

            $provider = new Provider();
            $provider->number_doc = strip_tags($request->number_doc);
            $provider->name = strip_tags($request->name);
            $provider->type_doc = strip_tags($request->type_doc);
            $provider->address = strip_tags($request->address);
            $provider->phone = strip_tags($request->phone);
            $provider->email = strip_tags($request->email);
            $provider->created_by = auth()->id();
            $provider->updated_by = auth()->id();
            $provider->save();


            DB::commit();
            return response()->json([
                'message' => 'Proveedor registrado.',
                'id_provider' => $provider->id_provider
            ], 201);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'number_doc' => 'required|numeric',
            'name' => 'required|max:255'
        ]);

            DB::beginTransaction();

            $provider = Provider::findOrFail($id);
            $provider->number_doc = $request->number_doc;
            $provider->name = $request->name;
            $provider->type_doc = $request->type_doc;
            $provider->address = $request->address;
            $provider->phone = $request->phone;
            $provider->email = $request->email;
            $provider->updated_by = auth()->id();
            $provider->save();


            DB::commit();
            return response()->json([
                'message' => 'Proveedor actualizado.',
            ], 200);
    }

    public function destroy($id) {

        $provider = Provider::findOrFail($id);
        if($provider == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

            DB::beginTransaction();

            $provider->status = 0;
            $provider->save();

            DB::commit();
            return response()->json([
                'message' => 'Proveedor eliminado.',
            ], 200);
    }

    public function show($id) {
        $provider = Provider::findOrFail($id);
        if($provider == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesProvider($provider);
    }

    public function list(Request $request) {
        return ResourcesProvider::collection(Provider::where("name", "like", "%".$request->search."%")->get());
    }
}
