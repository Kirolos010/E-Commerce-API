<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        //get all products
        $products= Product::paginate(10); //Fetches 10 products per page you can change the num
        return response()->json([
            'data'=>$products,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'cat_id' => 'required|exists:categories,id',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        // Create a new product
        $product = Product::create($request->only('title', 'description', 'image', 'price', 'quantity', 'cat_id'));
        // Return a success response
        return response()->json([
            'status' => 201,
            'message' => 'Product created successfully',
            'data' => $product,
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the product by ID or fail
            $product = Product::findOrFail($id);
    
            // Return the product data if found
            return response()->json([
                'data' => $product,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            // Return error response if product not found
            return response()->json([
                'message' => 'Product not found',
                'status' => 404,
                'error' => $e->getMessage(),
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
            'description' => 'sometimes|required|string',
            'image' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|required|integer|min:1',
            'cat_id' => 'sometimes|required|exists:categories,id',
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
            // Find the product by ID or fail
            $product = Product::findOrFail($id);
            // Update the product attributes
            $product->update($request->only('title', 'description', 'image', 'price', 'quantity', 'cat_id'));
            // Return a success response
            return response()->json([
                'data' => $product,
                'message' => 'Product updated successfully',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            return response()->json([
                'message' => 'An error occurred while updating the product',
                'status' => 500,
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
            // Find the product by ID or fail
            $product = Product::findOrFail($id);
            //dd(product);
            // Delete the product
            $product->delete();
            // Return a success response
            return response()->json([
                'status' => 200,
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            return response()->json([
                'message' => 'An error occurred while deleting the product',
                'status' => $e instanceof ModelNotFoundException ? 404 : 500,
                'error' => $e->getMessage(),
            ]);
        }
    }    
}
