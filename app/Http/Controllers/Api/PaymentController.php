<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function handlePayment(Request $request)
    {
        Log::info('Payment Payload', $request->all());

        $request->validate([
            'invoice_no' => 'required', 'string',
            'transaction_id' => 'required', 'string',
            'status' => 'required', 'string',
            'gross_amount' => 'required', 'integer',
        ]);

        $invoiceNo = $request->input('invoice_no');
        $status = $request->input('status');
        $txnId = $request->input('transaction_id');
        
        $order = Order::where('invoice_no', $invoiceNo)->first();

        if (!$order){
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $isPaid = in_array($status, ['success', 'pending']);

            Payment::updateorCreate([
                'order_id' => $order->id,
                'provider_order_id' => $txnId,
            ], [
                'provider' => $request->input('provider', 'midtrans'),
                'provider_order_id' => $request->input('provider_order_id', $invoiceNo),
                'payment_type' => $request->input('payment_type', 'bank_transfer'),
                'bank' => $request->input('bank', null),
                'va_number' => $request->input('va_number'),
                'status' => $status,
                'gross_amount' => $request->input('gross_amount'),
                'raw_response' => $request->all(),
                'paid_at' => $isPaid ? $now : null,
            ]);

            if ($isPaid) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'paid',
                    'paid_at' => $now,
                ]);
            } elseif (in_array($status, ['cancel', 'deny', 'expire', 'failed'])) {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle payment: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to handle payment: ' . $e->getMessage()
            ], 500);
        }
    }
}