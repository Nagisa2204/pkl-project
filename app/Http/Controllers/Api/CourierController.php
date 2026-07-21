<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourierController extends Controller
{
    public function handleCourier(Request $request)
    {
        Log::info('Courier Payload:', $request->all());

        $request->validate([
            'awb_number' => 'required|string',
            'status'     => 'required|string',
        ]);

        $awbNumber = $request->input('awb_number');
        $status    = strtolower($request->input('status'));

        $shipment = Shipment::where('awb_number', $awbNumber)->first();

        if (!$shipment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Courier not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $updateData = [
                'status'       => $status,
                'raw_response' => $request->all(),
            ];

            if ($status === 'shipped' && !$shipment->shipped_at) {
                $updateData['shipped_at'] = $now;
            } elseif ($status === 'delivered') {
                $updateData['delivered_at'] = $now;
            }

            $shipment->update($updateData);

            if ($order = $shipment->order) {
                if ($status === 'shipped') {
                    $order->update([
                        'delivery_status' => 'shipped',
                        'status' => 'on_delivery',
                    ]);
                } elseif ($status === 'delivered') {
                    $order->update([
                        'delivery_status' => 'delivered',
                        'fulfillment_status' => 'fulfilled',
                        'status' => 'completed',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status Payment Changed',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Courier Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update status',
            ], 500);
        }
    }
}