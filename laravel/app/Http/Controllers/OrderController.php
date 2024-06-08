<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    use HttpResponses;

    public function create(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = new Order();
        $order->user_id = Auth::id();
        $order->status = 'pending';
        $order->total = 0;
        $order->save();

        $total = 0;
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            $quantity = $productData['quantity'];
            $order->products()->attach($product->id, ['quantity' => $quantity]);
            $total += $product->price * $quantity;
        }

        $order->total = $total;
        $order->save();

        return $this->success($order, 'Order created successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return $this->success($order, 'Order status updated successfully');
    }

    public function track($id)
    {
        $order = Order::with('products')->findOrFail($id);

        return $this->success($order, 'Order details retrieved successfully');
    }

    public function getAllOrderLists(){
        $orders = Order::with('userDetails')->get()->toArray();
        parent::replaceNullWithEmptyString($orders);
        return $this->success($orders);
    }
}

