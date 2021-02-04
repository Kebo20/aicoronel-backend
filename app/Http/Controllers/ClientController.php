<?php

namespace App\Http\Controllers;

use App\Http\Resources\Client as ResourcesClient;
use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    //
    public function index() {
        return ResourcesClient::collection(Client::where("status",1)->get());
    }

    public function count(Request $request) {
        return Client::where('status','1')->count();
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'number_doc' => 'required|numeric',
            'name' => 'required|max:255'
        ]);

        $exist_client = Client::where('number_doc', $request->number_doc)->first();
        if($exist_client != null)
            return response()->json([
                'message' => 'Ya existe una empresa con el mismo número de documento.'
            ], 400);

            DB::beginTransaction();

            $client = new Client();
            $client->number_doc = strip_tags($request->number_doc);
            $client->name = strip_tags($request->name);
            $client->type_doc = strip_tags($request->type_doc);
            $client->address = strip_tags($request->address);
            $client->phone = strip_tags($request->phone);
            $client->email = strip_tags($request->email);
            $client->created_by = auth()->id();
            $client->updated_by = auth()->id();
            $client->save();


            DB::commit();
            return response()->json([
                'message' => 'Cliente registrado.',
                'id_client' => $client->id_client
            ], 201);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'number_doc' => 'required|numeric',
            'name' => 'required|max:255'
        ]);

            DB::beginTransaction();

            $client = Client::findOrFail($id);
            $client->number_doc = $request->number_doc;
            $client->name = $request->name;
            $client->type_doc = $request->type_doc;
            $client->address = $request->address;
            $client->phone = $request->phone;
            $client->email = $request->email;
            $client->updated_by = auth()->id();
            $client->save();


            DB::commit();
            return response()->json([
                'message' => 'Cliente actualizado.',
            ], 200);
    }

    public function destroy($id) {

        $client = Client::findOrFail($id);
        if($client == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

            DB::beginTransaction();

            $client->status = 0;
            $client->save();

            DB::commit();
            return response()->json([
                'message' => 'Cliente eliminado.',
            ], 200);
    }

    public function show($id) {
        $client = Client::findOrFail($id);
        if($client == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesClient($client);
    }

    public function list(Request $request) {
        return ResourcesClient::collection(Client::where("name", "like", "%".$request->search."%")->get());
    }
}
