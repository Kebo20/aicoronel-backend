<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\Category as ResourcesCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    //
    public function index() {
        return ResourcesCategory::collection(Category::where("status",1)->get());
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|max:255'
        ]);

        $exist_category = Category::where('name', $request->name)->first();
        if($exist_category != null)
            return response()->json([
                'message' => 'Ya existe una categoría con el mismo nombre.'
            ], 400);

            DB::beginTransaction();

            $category = new Category();
            $category->name = strip_tags($request->name);
            $category->created_by = auth()->id();
            $category->updated_by = auth()->id();
            $category->save();


            DB::commit();
            return response()->json([
                'message' => 'Categoría registrada.',
                'id_category' => $category->id_category
            ], 201);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'required|max:255'
        ]);

            DB::beginTransaction();

            $category = Category::findOrFail($id);
            $category->name = $request->name;
            $category->updated_by = auth()->id();
            $category->save();


            DB::commit();
            return response()->json([
                'message' => 'Categoría actualizada.',
            ], 200);
    }

    public function destroy($id) {

        $category = Category::findOrFail($id);
        if($category == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

            DB::beginTransaction();

            $category->status = 0;
            $category->save();

            DB::commit();
            return response()->json([
                'message' => 'Categoría eliminada.',
            ], 200);
    }

    public function show($id) {
        $category = Category::findOrFail($id);
        if($category == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesCategory($category);
    }

    public function list(Request $request) {
        return ResourcesCategory::collection(Category::where("name", "like", "%".$request->search."%")->get());
    }
}
