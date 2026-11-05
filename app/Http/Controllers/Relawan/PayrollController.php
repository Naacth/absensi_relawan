<?php

namespace App\Http\Controllers\Relawan;

use App\Http\Controllers\Controller;
use App\Models\PayrollDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PayrollController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $slips = PayrollDetail::with('payroll')
            ->where('user_id', $user->id)
            ->whereHas('payroll', fn($q) => $q->whereIn('status', ['approved', 'paid']))
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('relawan.payroll.index', compact('slips'));
    }

    public function slipPdf(PayrollDetail $detail)
    {
        $user = Auth::user();

        // Pastikan hanya bisa download slip miliknya sendiri
        if ($detail->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $detail->load(['payroll', 'user.division']);

        $pdf = Pdf::loadView('admin.payroll.slip_pdf', compact('detail'))
            ->setPaper('a5', 'landscape');

        $filename = 'Slip_Honor_' . Str::slug($detail->user->name) . '_' . $detail->payroll->period_start . '.pdf';
        return $pdf->download($filename);
    }
}

