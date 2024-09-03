<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class RecipeControlller extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id' => 'required|exists:categories,id',
            'energy' => 'required|numeric',
            'carbohydrate' => 'required|numeric',
            'protein' => 'required|numeric',
            'ingredients' => 'required',
            'method' => 'required',
            'tips' => 'required',
            'thumbnail' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnail->move("/recipes", "$request->slug.png");
        }

        $user = auth()->user();

        Recipe::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
            'energy' => $request->energy,
            'carbohydrate' => $request->carbohydrate,
            'protein' => $request->protein,
            'ingredients' => $request->ingredients,
            'method' => $request->method,
            'tips' => $request->tips,
            'thumbnail' => "recipes/$request->slug.png",
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Recipe created successful',
        ]);
    }

    public function destroy($slug)
    {
        $recipe = Recipe::where('slug', $slug)->first();

        if (auth()->user()->id !== $recipe->user_id) {
            return response()->json([
                'message' => 'Forbidden access'
            ], 403);
        }

        $recipe->delete();

        return response()->json([
            'message' => 'Recipe deleted successful'
        ]);
    }

    public function rating($slug, Request $request)
    {
        $recipe = Recipe::where('slug', $slug)->first();

        if (!$recipe) {
            return response()->json([
                'message' => 'Not found!'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user()->id;
        if ($user == $recipe->user_id) {
            return response()->json([
                'message' => 'You cannot rate your own recipe'
            ], 403);
        }

        foreach ($recipe->ratings as $rating) {
            if (auth()->user()->id == $rating->user_id) {
                return response()->json([
                    'message' => 'You have rated'
                ], 403);
            }
        }

        Rating::create([
            'recipe_id' => $recipe->id,
            'user_id' => $user,
            'rating' => $request->rating,
            'created_at' => Date::now(),
        ]);

        return response()->json([
            'message' => 'Rating success'
        ]);
    }

    public function comment($slug, Request $request)
    {
        $recipe = Recipe::where('slug', $slug)->first();

        $validator = Validator::make($request->all(), [
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        Comment::create([
            'recipe_id' => $recipe->id,
            'user_id' => auth()->user()->id,    
            'comment' => $request->comment,
            'created_at' => Date::now(),
        ]);

        return response()->json([
            'message' => 'Comment success'
        ]);
    }
}
