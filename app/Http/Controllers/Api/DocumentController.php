<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function invoice($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        $pdf = Pdf::loadView('invoice', compact('order'));
        return $pdf->download('invoice-' . $order->unique_order . '.pdf');
    }
}
