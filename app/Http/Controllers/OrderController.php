<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
        // Get the authenticated user's orders
        $orders = Order::with('user')->where('user_id', $user->id)->get();
        return response()->json([
            'data' => $orders,
            'status' => 200,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        // Create a new order
        $order = Order::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'price' => $this->getProductPrice($request->product_id),
        ]);
        return response()->json([
            'status' => 201,
            'message' => 'Order created successfully',
            'data' => $order,
        ]);
    }

    /**
     * Get the price of the product.
     */
    protected function getProductPrice($productId)
    {
        return Product::findOrFail($productId)->price; // Throws a 404 if not found
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
        // Fetch the order associated with the authenticated user
        $order = Order::where('id', $id)->where('user_id', $user->id)->first();
        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found',
            ], 404);
        }
        // Get the related product details
        $product = $order->product; //  product 
        return response()->json([
            'data' => [
                'order' => $order,
                'product' => $product,
            ],
            'status' => 200,
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        // Fetch the order associated with the authenticated user
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found or you are not authorized to update this order.',
            ], 404);
        }
        // Update the order with the validated data
        $order->update($request->only('quantity'));
        $product = $order->product; // Fetch the related product if necessary    
        return response()->json([
            'status' => 200,
            'message' => 'Order updated successfully',
            'data' => [
                'order' => $order,
                'product' => $product, 
            ],
        ]);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Fetch the order associated with the authenticated user
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found or you are not authorized to delete this order.',
            ], 404);
        }
        // Delete the order
        $order->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Order deleted successfully',
        ]);
    }    
}
