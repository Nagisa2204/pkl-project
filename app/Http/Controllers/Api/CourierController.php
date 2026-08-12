<?php

namespace App\Http\Controllers\Api;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\OrderLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourierController extends Controller
{
    public function handleCourier(Request $request, OrderLifecycleService $lifecycle): JsonResponse
    {
        $data = $request->validate([
            'awb_number' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in([ShipmentStatus::Shipped->value, ShipmentStatus::Delivered->value])],
        ]);

        $shipment = Shipment::with('order')->where('awb_number', $data['awb_number'])->firstOrFail();
        $order = $lifecycle->updateShipment($shipment->order, $data['status'], $data['awb_number']);

        return response()->json(['message' => 'Status pengiriman diperbarui.', 'order_no' => $order->order_no]);
    }
}
