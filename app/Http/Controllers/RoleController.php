<?php

namespace App\Http\Controllers;

use App\Http\Resources\Role as ResourcesRole;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    //
    public function index() {
        return ResourcesRole::collection(Role::where("status",1)->get());
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|max:255'
        ]);

        $exist_role = Role::where('name', $request->name)->first();
        if($exist_role != null)
            return response()->json([
                'message' => 'Ya existe un rol con el mismo nombre.'
            ], 400);

            DB::beginTransaction();

            $role = new Role();
            $role->name = strip_tags($request->name);
            $role->created_by = auth()->id();
            $role->updated_by = auth()->id();
            $role->save();


            DB::commit();
            return response()->json([
                'message' => 'Rol registrado.',
                'id_role' => $role->id_role
            ], 201);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'required|max:255'
        ]);

            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->updated_by = auth()->id();
            $role->save();


            DB::commit();
            return response()->json([
                'message' => 'Rol actualizado.',
            ], 200);
    }

    public function destroy($id) {

        $role = Role::findOrFail($id);
        if($role == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

            DB::beginTransaction();

            $role->status = 0;
            $role->save();

            DB::commit();
            return response()->json([
                'message' => 'Rol eliminado.',
            ], 200);
    }

    public function show($id) {
        $role = Role::findOrFail($id);
        if($role == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesRole($role);
    }

    public function list(Request $request) {
        return ResourcesRole::collection(Role::where("name", "like", "%".$request->search."%")->get());
    }
}
