<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryControlller extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ],422);
        }

        Category::create($request->all());

        return response()->json([
            'message' => 'Category created successful',
        ]);
    }

    public function destroy($slug, Request $request){
        Category::where('slug', $slug)->first()->delete();

        return response()->json([
            'message' => 'Category deleted successful'
        ]);
    }
}
