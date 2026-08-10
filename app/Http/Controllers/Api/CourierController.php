<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\OrderLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function handleCourier(Request $request, OrderLifecycleService $lifecycle): JsonResponse
    {
        $data = $request->validate([
            'awb_number' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:shipped,delivered'],
        ]);

        $shipment = Shipment::with('order')->where('awb_number', $data['awb_number'])->firstOrFail();
        $order = $lifecycle->updateShipment($shipment->order, $data['status'], $data['awb_number']);

        return response()->json(['message' => 'Shipment updated.', 'order_no' => $order->order_no]);
    }
}
