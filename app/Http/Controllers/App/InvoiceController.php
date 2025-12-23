<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Domains\Commerce\Models\Order;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends Controller
{
    public function download(Order $order): BinaryFileResponse|Response
    {
        // Ensure user can only download their own invoices
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items.orderable', 'user']);

        // Generate PDF
        $pdf = Pdf::loadView('filament.app.invoice', [
            'order' => $order,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("invoice-{$order->id}.pdf");
    }
}

