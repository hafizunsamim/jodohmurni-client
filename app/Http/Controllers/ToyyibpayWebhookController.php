<?php

namespace App\Http\Controllers;

use App\Services\ToyyibpayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Webhook (Callback) dari Toyyibpay untuk kemaskini status langganan selepas pembayaran.
 * Toyyibpay hantar POST ke billCallbackUrl dengan refno, status, billcode, order_id, amount, hash, dll.
 */
class ToyyibpayWebhookController extends Controller
{
    /** Status callback: 1=success, 2=pending, 3=fail */
    private const STATUS_SUCCESS = '1';

    public function __invoke(Request $request, ToyyibpayService $toyyibpay)
    {
        $refno = $request->input('refno', '');
        $status = $request->input('status', '');
        $orderId = $request->input('order_id', '');
        $billcode = $request->input('billcode', '');
        $amount = $request->input('amount', '');
        $reason = $request->input('reason', '');
        $receivedHash = $request->input('hash', '');

        Log::info('Toyyibpay callback received', [
            'refno' => $refno,
            'status' => $status,
            'order_id' => $orderId,
            'billcode' => $billcode,
        ]);

        if ($orderId === '' || $receivedHash === '') {
            Log::warning('Toyyibpay callback missing order_id or hash');
            return response('Bad Request', Response::HTTP_BAD_REQUEST);
        }

        if (!$toyyibpay->verifyCallbackHash($status, $orderId, $refno, $receivedHash)) {
            Log::warning('Toyyibpay callback hash verification failed', ['order_id' => $orderId]);
            return response('Invalid hash', Response::HTTP_FORBIDDEN);
        }

        if ($status !== self::STATUS_SUCCESS) {
            Log::info('Toyyibpay callback non-success status', [
                'order_id' => $orderId,
                'status' => $status,
                'reason' => $reason,
            ]);
            return response('OK', Response::HTTP_OK);
        }

        $context = $toyyibpay->getBillContext($orderId);
        if (!$context) {
            Log::warning('Toyyibpay callback: no context for order_id', ['order_id' => $orderId]);
            return response('OK', Response::HTTP_OK);
        }

        $toyyibpay->activateSubscriptionForOrder($orderId, (int) $amount);

        return response('OK', Response::HTTP_OK);
    }
}
