<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\CashTransaction;
use App\Models\Category;
use App\Models\Party;
use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportCashCsv extends Command
{
    protected $signature = 'import:cash-csv {file?} {--dry-run} {--force}';

    protected $description = 'Import Kas INTRASOL - Kas.csv with preview/validation and duplicate detection';

    public function handle(): int
    {
        $file = $this->argument('file') ?: base_path('Kas INTRASOL - Kas.csv');
        if (! file_exists($file)) {
            $this->error('CSV not found: '.$file);
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        if (! $dryRun && ! $this->option('force')) {
            if (! $this->confirm('This will import financial data. Continue?')) {
                return self::FAILURE;
            }
        }

        $rows = array_map('str_getcsv', file($file));
        $created = 0; $skipped = 0; $rejected = 0;
        $admin = User::first();

        DB::beginTransaction();
        try {
            foreach ($rows as $idx => $row) {
                if ($idx < 8) { $skipped++; continue; }
                if (count($row) < 6) { $rejected++; continue; }
                $date = trim($row[1] ?? '');
                $desc = trim($row[2] ?? '');
                $cat = trim($row[3] ?? '');
                $income = trim($row[4] ?? '');
                $expense = trim($row[5] ?? '');
                if ($date === '' || strtolower($date) === 'tanggal') { $skipped++; continue; }
                if ($desc === '' && $cat === '') { $skipped++; continue; }
                if (stripos($desc, 'Pindah bank') !== false || stripos($desc, 'Total') !== false || stripos($cat, 'REPORT') !== false) { $skipped++; continue; }
                if (preg_match('/\d{4}/', $date) === 0 && preg_match('/\d{2}\/\d{2}/', $date) === 0 && ! preg_match('/\d{1,2}\s+(Juli|Agustus|September|Oktober|November|Desember)/i', $date)) { $skipped++; continue; }
                $amountStr = $income !== '' ? $income : $expense;
                if ($amountStr === '') { $rejected++; continue; }
                $amount = (int) preg_replace('/[^0-9]/', '', $amountStr);
                if ($amount <= 0) { $rejected++; continue; }
                $type = $income !== '' ? 'income' : 'expense';
                $parsedDate = null;
                if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $date, $m)) {
                    $parsedDate = $m[3].'-'.$m[2].'-'.$m[1];
                } elseif (preg_match('/(\d{1,2})\s+Juli/i', $date, $m)) { $parsedDate = '2026-07-'.str_pad($m[1], 2, '0', STR_PAD_LEFT); }
                elseif (preg_match('/(\d{1,2})\s+Agustus/i', $date, $m)) { $parsedDate = '2026-08-'.str_pad($m[1], 2, '0', STR_PAD_LEFT); }
                elseif (preg_match('/(\d{1,2})\s+September/i', $date, $m)) { $parsedDate = '2026-09-'.str_pad($m[1], 2, '0', STR_PAD_LEFT); }
                else { $rejected++; continue; }

                $fingerprint = hash('sha256', $parsedDate.'|'.$desc.'|'.$amount.'|'.$type);
                if (CashTransaction::where('description', $desc)->where('amount', $amount)->whereDate('transaction_date', $parsedDate)->exists()) {
                    $skipped++; continue;
                }

                if ($dryRun) { $created++; continue; }

                $category = Category::firstOrCreate(
                    ['name' => $cat !== '' ? $cat : ($type === 'income' ? 'Other Income' : 'Other'), 'type' => $type],
                    ['is_active' => true]
                );

                $tx = CashTransaction::create([
                    'transaction_date' => $parsedDate,
                    'type' => $type,
                    'amount' => $amount,
                    'category_id' => $category->id,
                    'description' => $desc,
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ]);

                AuditLog::create([
                    'user_id' => $admin?->id,
                    'auditable_type' => CashTransaction::class,
                    'auditable_id' => $tx->id,
                    'action' => 'imported',
                    'before_values' => null,
                    'after_values' => $tx->toArray(),
                    'reason' => 'CSV import',
                    'ip_address' => null,
                    'user_agent' => 'artisan',
                    'created_at' => now(),
                ]);
                $created++;
            }
            if ($dryRun) { DB::rollBack(); } else { DB::commit(); }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Created: $created, Skipped: $skipped, Rejected: $rejected".($dryRun ? ' (dry-run)' : ''));
        return self::SUCCESS;
    }
}
