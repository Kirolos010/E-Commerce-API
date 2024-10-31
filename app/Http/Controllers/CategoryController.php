<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all categories 
        $categories = Category::paginate(10);   //Fetches 10 products per page
        return response()->json([
            'data'=>$categories,
            'status' => 200,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'required|string',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        // Create the category
        $category = Category::create([
            'title' => $request->title,
            'image' => $request->image,
        ]);
        // Return a success response
        return response()->json([
            'status' => 201,
            'message' => 'Category created successfully',
            'data' => $category,
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Get single category 
            $category = Category::findOrFail($id);
            //dd(category);
            // Return the category data if found
            return response()->json([
                'data' => $category,
                'status' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            // Return error response if category not found
            return response()->json([
                'message' => 'Category not found',
                'status' => 404,
            ]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|required|string',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        try {
            // Find the category by ID or fail
            $category = Category::findOrFail($id);
            // Update the category attributes
            $category->update($request->only('title', 'image'));
            // Return a success response
            return response()->json([
                'status' => 200,
                'message' => 'Category updated successfully',
                'data' => $category,
            ]);
        } catch (ModelNotFoundException $e) {
            // Return error response if category not found
            return response()->json([
                'message' => 'Category not found',
                'status' => 404,
                'error' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the category by ID or fail
            $category = Category::findOrFail($id);
            // Delete the category
            $category->delete();
            // Return a success response
            return response()->json([
                'status' => 200,
                'message' => 'Category deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            // Return error response if category not found
            return response()->json([
                'message' => 'Category not found',
                'status' => 404,
            ]);
        }        
    }
}
