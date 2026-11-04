<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Asset;
use App\Models\StockTransaction;

class ReportController extends Controller
{
    // ၁။ Stock Summary Report ထုတ်ရန်
    public function stockSummaryPdf()
    {
        $assets = Asset::with(['category', 'warehouse', 'items'])->get();
        
        $data = [
            'assets' => $assets,
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = Pdf::loadView('reports.stock-summary', $data);
        
        return $pdf->stream('stock-summary-report.pdf');
    }

    // ၂။ Monthly/Yearly Movement Report ထုတ်ရန်
    public function movementReportPdf(Request $request)
    {
        $type = $request->input('type', 'monthly');
        
        $query = StockTransaction::with(['asset', 'user']);
        
        if ($type === 'monthly') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } else {
            $query->whereYear('created_at', now()->year);
        }

        $transactions = $query->get();

        $data = [
            'transactions' => $transactions,
            'type' => $type,
            'date' => now()->format('Y-m-d'),
        ];

        $pdf = Pdf::loadView('reports.movement-report', $data);
        
        return $pdf->stream($type . '-movement-report.pdf');
    }
}