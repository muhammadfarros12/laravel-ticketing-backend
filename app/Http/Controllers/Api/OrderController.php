<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Sku;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\OrderTicket;
use Illuminate\Http\Request;
use App\Service\Midtrans\CreatePaymentUrlService;

class OrderController extends Controller
{
    //create order
    public function create(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'order_details' => 'required|array',
            'order_details.*.sku_id' => 'required|exists:skus,id',
            'quantity' => 'required|integer|min:1',
            'event_date' => 'required',
        ]);

        // $event = Event::find($request->event_id);

        $total = 0;
        foreach ($request->order_details as $order_detail) {
            $sku = Sku::find($order_detail['sku_id']);
            $qty = $order_detail['qty'];
            $total += $sku->price * $qty;
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'event_id' => $request->event_id,
            'event_date'=> $request->event_date,
            'quantity' => $request->quantity,
            'total_price' => $total,
        ]);

        $total = 0;
        foreach ($request->order_details as $order_detail) {
            $sku = Sku::find($order_detail['sku_id']);
            $qty = $order_detail['qty'];

            for ($i=0; $i < $qty; $i++) {
                $ticket = Ticket::where('sku_id', $sku->id)
                ->where('status','available')
                ->first();
                OrderTicket::create([
                    'order_id' => $order->id,
                    'ticket_id' => $ticket->id,
                ]);
                $ticket->update([
                    'status' => 'booked'
                ]);
            }
        }

        $midtrans = new CreatePaymentUrlService();
        $user = $request->user();
        $order['user'] = $user;
        $order['orderItems'] = $request->order_details;
        $paymentUrl = $midtrans->getPaymentUrl($order);
        $order['payment_url'] = $paymentUrl;

        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }
}
