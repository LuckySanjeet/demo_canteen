<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Traits\HttpResponses;
use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    use HttpResponses;

    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createPaymentLink(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);


        $paymentLink = $this->createStripePaymentLink($order);

        return $this->success(['payment_link' => $paymentLink], 'Payment link created successfully');
    }

    public function createStripePaymentLink($order)
    {

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Order ' . $order->id,
                    ],
                    'unit_amount' => $order->total * 100, // in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
        ]);

        return $session->url;
    }

    public function handleSuccess()
    {
        // Handle successful payment
        return $this->success([], 'Payment successful');
    }

    public function handleCancel()
    {
        // Handle cancelled payment
        return $this->error([], 'Payment cancelled');
    }
}

