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
            $dateFrom = $request->query('dateFrom');
            $dateTo = $request->query('dateTo');

            // Default to current month if no dates provided
            if (!$dateFrom || !$dateTo) {
                $dateFrom = now()->startOfMonth()->format('Y-m-d');
                $dateTo = now()->endOfMonth()->format('Y-m-d');
            }

            $query = StockTransaction::with(['product', 'user']);
            $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            $transactions = $query->orderBy('created_at', 'desc')->get();

            $pdf = Pdf::loadView('exports.stock-transactions-pdf', [
                'transactions' => $transactions,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
            return $pdf->download('stock-transactions-' . $dateFrom . '-to-' . $dateTo . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
