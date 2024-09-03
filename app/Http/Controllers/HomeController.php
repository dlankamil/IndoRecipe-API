<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function categories()
    {
        $categories = Category::get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    public function recipes()
    {
        $recipes = Recipe::get();

        return response()->json(['recipes' => $recipes->map(function ($recipe) {
            return [
                'id' =>  $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'ingredients' => $recipe->ingredients,
                'method' => $recipe->method,
                'tips' => $recipe->tips,
                'energy' => $recipe->energy . ' kcal',
                'carbohydrate' => $recipe->carbohydrate . 'g',
                'protein' => $recipe->protein . 'g',
                'thumbnail' => asset($recipe->thumbnail),
                'created_at' => $recipe->created_at,
                'author' => $recipe->user->username,
                'ratings_avg' => round($recipe->ratings()->avg('rating'), 1),
                'category' => $recipe->category
            ];
        })]);
    }

    public function show($slug)
    {
        $recipe = Recipe::where('slug', $slug)->first();

        return response()->json([
            'id' => $recipe->id,
            'title' => $recipe->title,
            'slug' => $recipe->slug,
            'ingredients' => $recipe->ingredients,
            'method' => $recipe->method,
            'tips' => $recipe->tips,
            'energy' => $recipe->energy . ' kcal',
            'carbohydrate' => $recipe->carbohydrate . 'g',
            'protein' => $recipe->protein . 'g',
            'thumbnail' => asset($recipe->thumbnail),
            'created_at' => $recipe->created_at,
            'author' => $recipe->user->username,
            'ratings_avg' => round($recipe->ratings()->avg('rating'), 1),
            'category' => $recipe->category,
            'comments' => $recipe->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at,
                    'comment_author' => $comment->user->username,
                ];
            })
        ]);
    }

    public function top()
    {
        $recipes =  Recipe::get()->map(function ($recipe) {
            return [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'ingredients' => $recipe->ingredients,
                'method' => $recipe->method,
                'tips' => $recipe->tips,
                'energy' => $recipe->energy . ' kcal',
                'carbohydrate' => $recipe->carbohydrate . 'g',
                'protein' => $recipe->protein . 'g',
                'thumbnail' => asset($recipe->thumbnail),
                'created_at' => $recipe->created_at,
                'author' => $recipe->user->username,
                'ratings_avg' => round($recipe->ratings()->avg('rating'), 1),
                'category' => $recipe->category,
            ];
        });

        $data = $recipes->sortByDesc('ratings_avg')->values()->take(3);

        return response()->json([
            'best-recipes' => $data
        ]);
    }
}
