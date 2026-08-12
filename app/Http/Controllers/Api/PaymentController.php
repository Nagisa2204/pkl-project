<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use App\Services\OrderLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function handlePayment(
        Request $request,
        MidtransService $midtrans,
        OrderLifecycleService $lifecycle
    ): JsonResponse {
        $payload = $request->validate([
            'order_id' => ['required', 'string', 'max:64'],
            'transaction_status' => ['required', 'string', Rule::in([
                'pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'failure',
            ])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'status_code' => ['required', 'string', 'max:10'],
            'gross_amount' => ['required', 'numeric'],
            'signature_key' => ['required', 'string', 'size:128'],
            'payment_type' => ['nullable', 'string', 'max:80'],
            'fraud_status' => ['nullable', 'string', 'max:30'],
            'bank' => ['nullable', 'string', 'max:50'],
            'va_numbers' => ['nullable', 'array'],
            'va_numbers.*.bank' => ['nullable', 'string', 'max:50'],
            'va_numbers.*.va_number' => ['nullable', 'string', 'max:100'],
            'permata_va_number' => ['nullable', 'string', 'max:100'],
            'bill_key' => ['nullable', 'string', 'max:100'],
            'biller_code' => ['nullable', 'string', 'max:100'],
        ]);

        if (! $midtrans->hasValidSignature($payload)) {
            Log::warning('Rejected Midtrans webhook with invalid signature', ['order_id' => $payload['order_id']]);

            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $order = $lifecycle->applyMidtransNotification($payload);

        return response()->json(['message' => 'Notification processed.', 'order_no' => $order->order_no]);
    }
}
