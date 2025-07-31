<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;

class StockTransactionExportController extends Controller
{
    public function test()
    {
        return 'Controller works!';
    }

    public function exportPdf(Request $request)
    {
        try {
            // Get all transactions without date filter
            $transactions = StockTransaction::with(['product', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            $pdf = Pdf::loadView('exports.stock-transactions-pdf', [
                'transactions' => $transactions,
            ]);

            return $pdf->download('stock-transactions-all.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
