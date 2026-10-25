<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Project;
use App\Services\CashBalanceService;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports, CashBalanceService $balances)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = $validated['from'] ?? now()->startOfMonth()->toDateString();
        $to = $validated['to'] ?? now()->endOfMonth()->toDateString();

        if ($from > $to) {
            throw ValidationException::withMessages(['from' => 'Rentang tanggal tidak valid.']);
        }

        $totals = $reports->periodTotals($from, $to);
        $outstanding = $reports->outstandingTotals();
        $byMonth = $balances->getMonthlyTotals();

        $income = $totals['income'];
        $expense = $totals['expense'];
        $receivables = $outstanding['receivables'];
        $payables = $outstanding['payables'];
        $advances = $outstanding['advances'];
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('reports.index', compact('from', 'to', 'income', 'expense', 'receivables', 'payables', 'advances', 'byMonth', 'projects'));
    }

    public function exportPeriod(Request $request, ReportService $reports)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = $validated['from'] ?? now()->startOfMonth()->toDateString();
        $to = $validated['to'] ?? now()->endOfMonth()->toDateString();

        if ($from > $to) {
            throw ValidationException::withMessages(['from' => 'Rentang tanggal tidak valid.']);
        }

        $transactions = $reports->periodTransactions($from, $to);
        $totals = $reports->periodTotals($from, $to);

        $pdf = Pdf::loadView('reports.pdf.period', [
            'from' => $from,
            'to' => $to,
            'transactions' => $transactions,
            'income' => $totals['income'],
            'expense' => $totals['expense'],
            'net' => $totals['net'],
            'logoPath' => $this->logoPath(),
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Transaksi_Kas_'.$from.'_'.$to.'.pdf';

        return $this->downloadWithFooter($pdf, $filename);
    }

    public function exportMonthly(Request $request, ReportService $reports)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);

        $start = now()->setDate($year, $month, 1)->startOfMonth()->toDateString();
        $end = now()->setDate($year, $month, 1)->endOfMonth()->toDateString();

        $transactions = $reports->periodTransactions($start, $end);
        $totals = $reports->periodTotals($start, $end);

        $pdf = Pdf::loadView('reports.pdf.monthly', [
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'transactions' => $transactions,
            'income' => $totals['income'],
            'expense' => $totals['expense'],
            'net' => $totals['net'],
            'logoPath' => $this->logoPath(),
        ])->setPaper('a4', 'portrait');

        $filename = 'Ringkasan_Bulanan_'.$year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'.pdf';

        return $this->downloadWithFooter($pdf, $filename);
    }

    public function exportAnnual(Request $request, CashBalanceService $balances)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $year = (int) ($validated['year'] ?? now()->year);
        $monthly = $balances->getMonthlyTotals($year);

        $income = (int) array_sum(array_column($monthly, 'income'));
        $expense = (int) array_sum(array_column($monthly, 'expense'));

        $pdf = Pdf::loadView('reports.pdf.annual', [
            'year' => $year,
            'monthly' => $monthly,
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
            'logoPath' => $this->logoPath(),
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan_Kas_Tahunan_'.$year.'.pdf';

        return $this->downloadWithFooter($pdf, $filename);
    }

    public function exportProject(Request $request, ReportService $reports)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        $from = $validated['from'] ?? now()->startOfYear()->toDateString();
        $to = $validated['to'] ?? now()->endOfMonth()->toDateString();
        $projectId = $validated['project_id'] ?? null;

        if ($from > $to) {
            throw ValidationException::withMessages(['from' => 'Rentang tanggal tidak valid.']);
        }

        $transactions = CashTransaction::with(['category', 'party', 'project'])
            ->active()
            ->whereNotNull('project_id')
            ->whereBetween('transaction_date', [$from, $to])
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->orderBy('project_id')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $groups = $transactions->groupBy('project_id')->map(function ($rows) {
            return [
                'project' => $rows->first()->project,
                'income' => (int) $rows->where('type', 'income')->sum('amount'),
                'expense' => (int) $rows->where('type', 'expense')->sum('amount'),
                'count' => $rows->count(),
            ];
        });

        $pdf = Pdf::loadView('reports.pdf.project', [
            'from' => $from,
            'to' => $to,
            'projectId' => $projectId,
            'groups' => $groups,
            'transactions' => $transactions,
            'logoPath' => $this->logoPath(),
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Keuangan_per_Proyek_'.$from.'_'.$to.'.pdf';

        return $this->downloadWithFooter($pdf, $filename);
    }

    public function exportBills(Request $request, ReportService $reports)
    {
        $validated = $request->validate([
            'type' => ['nullable', 'in:receivable,payable'],
        ]);

        $type = $validated['type'] ?? 'receivable';
        $bills = $reports->bills($type);

        $pdf = Pdf::loadView('reports.pdf.bills', [
            'type' => $type,
            'bills' => $bills,
            'totalOutstanding' => (int) $bills->sum('outstanding_amount'),
            'logoPath' => $this->logoPath(),
        ])->setPaper('a4', 'portrait');

        $filename = ($type === 'receivable' ? 'Laporan_Piutang' : 'Laporan_Utang').'_'.now()->format('Y-m-d').'.pdf';

        return $this->downloadWithFooter($pdf, $filename);
    }

    public function exportReconciliation(Request $request, ReportService $reports)
    {
        $validated = $request->validate([
            'reconciliation_id' => ['nullable', 'integer', 'exists:reconciliations,id'],
        ]);

        $reconciliation = $reports->latestReconciliation($validated['reconciliation_id'] ?? null);

        if (! $reconciliation) {
            abort(404, 'Tidak ada data rekonsiliasi untuk diekspor.');
        }

        $pdf = Pdf::loadView('reports.pdf.reconciliation', [
            'reconciliation' => $reconciliation,
            'logoPath' => $this->logoPath(),
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan_Rekonsiliasi_'.$reconciliation->reconciliation_date->format('Y-m-d').'.pdf';

        return $this->downloadWithFooter($pdf, $filename);
    }

    private function logoPath(): ?string
    {
        $path = public_path('images/logo.webp');

        return file_exists($path) ? $path : null;
    }

    /**
     * Render the document and stamp a page footer on every page.
     */
    private function downloadWithFooter(\Barryvdh\DomPDF\PDF $pdf, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('DejaVu Sans');

        if ($font !== null) {
            $size = 8;
            $text = 'PT Integrasi Solusi Teknologi (INTRASOL)  |  Halaman {PAGE_NUM} dari {PAGE_COUNT}';
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $x = max(14, ($canvas->get_width() - $width) / 2);
            $y = $canvas->get_height() - 20;

            $canvas->page_text($x, $y, $text, $font, $size, [0.35, 0.35, 0.35]);
        }

        return $pdf->download($filename);
    }
}

