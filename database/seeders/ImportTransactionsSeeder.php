<?php

namespace Database\Seeders;

use App\Models\CashTransaction;
use App\Models\Category;
use App\Models\Project;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ImportTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;

        $projectGemilang = Project::where('code', 'GEMILANG')->first();
        $projectCbt = Project::where('code', 'CBT-SMAN43')->first();
        $projectBuTiti = Project::where('code', 'BU-TITI')->first();

        $catProjectIncome = Category::where('name', 'Project Income')->where('type', 'income')->first();
        $catProject = Category::where('name', 'Project')->where('type', 'income')->first();
        $catProjectExpense = Category::where('name', 'Project Expense')->where('type', 'expense')->first();
        $catPengeluaranProject = Category::where('name', 'Pengeluaran Project')->where('type', 'expense')->first();
        $catGajiDeveloper = Category::where('name', 'Gaji Developer')->where('type', 'expense')->first();
        $catAdministrasi = Category::where('name', 'Administrasi')->where('type', 'expense')->first();
        $catOperational = Category::where('name', 'Operational')->where('type', 'expense')->first();
        $catLearning = Category::where('name', 'Learning')->where('type', 'expense')->first();
        $catOther = Category::where('name', 'Other')->where('type', 'expense')->first();
        $catSubscription = Category::where('name', 'Subscription')->where('type', 'expense')->first();

        $expenses = [
            ['date' => '2026-09-16', 'amount' => 34000, 'category' => $catPengeluaranProject, 'project' => $projectGemilang, 'description' => 'Bayar desain lanyard + ID card'],
            ['date' => '2026-09-17', 'amount' => 86000, 'category' => $catPengeluaranProject, 'project' => $projectGemilang, 'description' => 'Bayar konveksi lanyard'],
            ['date' => '2026-09-18', 'amount' => 46000, 'category' => $catOperational, 'project' => null, 'description' => 'Meeting'],
            ['date' => '2026-09-20', 'amount' => 300000, 'category' => $catOperational, 'project' => null, 'description' => 'Sponsor SwitchFest'],
            ['date' => '2026-09-20', 'amount' => 6750000, 'category' => $catGajiDeveloper, 'project' => $projectGemilang, 'description' => 'DP developer project CV Gemilang Wijaya Perkasa'],
            ['date' => '2026-09-20', 'amount' => 2000000, 'category' => $catGajiDeveloper, 'project' => $projectGemilang, 'description' => 'Gaji Dimas untuk pembuatan 100 desain'],
            ['date' => '2026-09-21', 'amount' => 5000, 'category' => $catOperational, 'project' => null, 'description' => 'Marketing INTRASOL ke HMJ TI'],
            ['date' => '2026-09-21', 'amount' => 450000, 'category' => $catGajiDeveloper, 'project' => $projectBuTiti, 'description' => 'Gaji tim support project Bu Titi'],
            ['date' => '2026-10-02', 'amount' => 50000, 'category' => $catLearning, 'project' => null, 'description' => 'Workshop pelatihan sales'],
            ['date' => '2026-10-19', 'amount' => 300000, 'category' => $catGajiDeveloper, 'project' => $projectCbt, 'description' => 'Bonus sales project Sistem CBT SMAN 43 Jakarta'],
            ['date' => '2026-10-19', 'amount' => 900000, 'category' => $catGajiDeveloper, 'project' => $projectCbt, 'description' => 'DP gaji developer project Sistem CBT SMAN 43 Jakarta'],
            ['date' => '2026-10-20', 'amount' => 55000, 'category' => $catAdministrasi, 'project' => null, 'description' => 'Pembuatan stempel PT INTRASOL'],
            ['date' => '2026-10-21', 'amount' => 244000, 'category' => $catSubscription, 'project' => null, 'description' => 'Iklan Dribbble dan Google Ads'],
            ['date' => '2026-10-23', 'amount' => 300000, 'category' => $catPengeluaranProject, 'project' => $projectGemilang, 'description' => 'Pembelian template project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa'],
            ['date' => '2026-10-24', 'amount' => 13044, 'category' => $catLearning, 'project' => $projectGemilang, 'description' => 'Pembelian domain untuk learning konfigurasi NAS project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa'],
        ];

        $incomes = [
            ['date' => '2026-09-19', 'amount' => 12000000, 'category' => $catProjectIncome, 'project' => $projectGemilang, 'description' => 'Project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa'],
            ['date' => '2026-10-19', 'amount' => 3000000, 'category' => $catProjectIncome, 'project' => $projectCbt, 'description' => 'DP project Sistem CBT SMAN 43 Jakarta'],
        ];

        $added = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($expenses as $data) {
                $exists = CashTransaction::where('transaction_date', $data['date'])
                    ->where('type', 'expense')
                    ->where('amount', $data['amount'])
                    ->where('description', $data['description'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $this->command->warn("SKIPPED (exists): {$data['date']} | expense | Rp{$data['amount']} | {$data['description']}");
                    continue;
                }

                $tx = CashTransaction::create([
                    'transaction_date' => $data['date'],
                    'type' => 'expense',
                    'amount' => $data['amount'],
                    'category_id' => $data['category']?->id,
                    'party_id' => null,
                    'project_id' => $data['project']?->id,
                    'description' => $data['description'],
                    'receipt_path' => null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);

                AuditLog::create([
                    'user_id' => $userId,
                    'auditable_type' => CashTransaction::class,
                    'auditable_id' => $tx->id,
                    'action' => 'created',
                    'before_values' => null,
                    'after_values' => $tx->toArray(),
                    'reason' => 'Import transaksi pengeluaran Sep-Okt 2026',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'CLI Seeder',
                    'created_at' => now(),
                ]);

                $added++;
                $this->command->info("ADDED: {$data['date']} | expense | Rp{$data['amount']} | {$data['description']}");
            }

            foreach ($incomes as $data) {
                $exists = CashTransaction::where('transaction_date', $data['date'])
                    ->where('type', 'income')
                    ->where('amount', $data['amount'])
                    ->where('description', $data['description'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $this->command->warn("SKIPPED (exists): {$data['date']} | income | Rp{$data['amount']} | {$data['description']}");
                    continue;
                }

                $tx = CashTransaction::create([
                    'transaction_date' => $data['date'],
                    'type' => 'income',
                    'amount' => $data['amount'],
                    'category_id' => $data['category']?->id,
                    'party_id' => null,
                    'project_id' => $data['project']?->id,
                    'description' => $data['description'],
                    'receipt_path' => null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);

                AuditLog::create([
                    'user_id' => $userId,
                    'auditable_type' => CashTransaction::class,
                    'auditable_id' => $tx->id,
                    'action' => 'created',
                    'before_values' => null,
                    'after_values' => $tx->toArray(),
                    'reason' => 'Import transaksi pemasukan Sep-Okt 2026',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'CLI Seeder',
                    'created_at' => now(),
                ]);

                $added++;
                $this->command->info("ADDED: {$data['date']} | income | Rp{$data['amount']} | {$data['description']}");
            }

            // Koreksi Modal Pak Saiful: Rp10.300.000 -> Rp9.300.000 (kurang Rp1.000.000)
            $modalTx = CashTransaction::where('transaction_date', '2026-03-01')
                ->where('type', 'income')
                ->where('amount', 10300000)
                ->where('description', 'Modal dari pak Syaiful')
                ->first();

            if ($modalTx) {
                $before = $modalTx->getAttributes();
                $modalTx->update([
                    'amount' => 9300000,
                    'updated_by' => $userId,
                ]);

                AuditLog::create([
                    'user_id' => $userId,
                    'auditable_type' => CashTransaction::class,
                    'auditable_id' => $modalTx->id,
                    'action' => 'updated',
                    'before_values' => $before,
                    'after_values' => $modalTx->fresh()->getAttributes(),
                    'reason' => 'Koreksi modal Pak Saiful dari Rp10.300.000 menjadi Rp9.300.000 (pengurangan Rp1.000.000)',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'CLI Seeder',
                    'created_at' => now(),
                ]);

                $this->command->info("MODAL CORRECTED: 2026-03-01 | income | Rp10.300.000 -> Rp9.300.000 | {$modalTx->description}");
            } else {
                $this->command->error('Modal transaction not found for correction!');
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info("\n=== SUMMARY ===");
        $this->command->info("Transaksi ditambahkan: {$added}");
        $this->command->info("Transaksi dilewati (sudah ada): {$skipped}");
        $this->command->info("Koreksi modal: 1 transaksi");
    }
}