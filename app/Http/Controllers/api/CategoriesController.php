<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function createCategory(Request $request)
    {
        $this->validate($request,[
            'categoryName' => 'required',
        ]);

        $category = new Category;
        $category->category_name = $request->input('category_name');
        $category->description = $request->input('description');
        $category->price= $request->input('price');
        $category->save();
        return response()->json(['message' => 'Category created successfully', 'category' => $category],200);
    }

    public function updateCategory(Request $request,$id)
    {
        $this->validate($request,[
            'categoryName' => 'required',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category = new Category;
        $category->category_name = $request->input('category_name');
        $category->description = $request->input('description');
        $category->price= $request->input('price');
        $category->save();
        return response()->json(['message' => 'Category updated successfully', 'category' => $category],200);
    }

    public function viewCategories()
    {
        $categories = Category::all();
        if ($categories->count() > 0) {
            return response()->json(['categorys'=>$categories],200);
        } else {
            return response()->json(['message' => 'No categorys found'], 404);
        }
    }

    public function viewSingleCategory($id)
    {
        $category = Category::where('id',  "$id")
                    ->exists();
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }else{
            $fetched_category = Category::where('id',  "$id")
                    ->get();
        }
        return response()->json($fetched_category,200);
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message'=>'Category Has been deleted'],200);
    }
}
