<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Project;
use App\Models\Party;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectReceivablesSeeder extends Seeder
{
    public function run(): void
    {
        $projectGemilang = Project::where('code', 'GEMILANG')->first();
        $projectCbt = Project::where('code', 'CBT-SMAN43')->first();

        $partyGemilang = Party::where('name', 'CV Gemilang Wijaya Perkasa')->first();
        $partyCbt = Party::where('name', 'SMAN 43 Jakarta')->first();

        DB::beginTransaction();
        try {
            // Piutang Gemilang: Rp12.000.000
            // Check if already exists
            $existingGemilang = Bill::where('type', 'receivable')
                ->where('party_id', $partyGemilang?->id)
                ->where('project_id', $projectGemilang?->id)
                ->where('amount', 12000000)
                ->first();

            if (!$existingGemilang) {
                Bill::create([
                    'type' => 'receivable',
                    'bill_number' => 'R-' . now()->format('Ymd') . '-GEMILANG',
                    'party_id' => $partyGemilang->id,
                    'project_id' => $projectGemilang->id,
                    'issue_date' => now()->subDays(30)->toDateString(), // Estimate: around when project started
                    'due_date' => now()->addDays(30)->toDateString(), // Due in 30 days
                    'description' => 'Sisa piutang project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa',
                    'amount' => 12000000,
                    'paid_amount' => 0,
                    'outstanding_amount' => 12000000,
                    'status' => 'unpaid',
                    'notes' => 'Piutang belum diterima per catatan internal',
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
                $this->command->info('Created receivable: Gemilang - Rp12.000.000');
            } else {
                $this->command->warn('Receivable Gemilang already exists, skipped.');
            }

            // Piutang CBT SMAN 43: Rp3.000.000
            $existingCbt = Bill::where('type', 'receivable')
                ->where('party_id', $partyCbt?->id)
                ->where('project_id', $projectCbt?->id)
                ->where('amount', 3000000)
                ->first();

            if (!$existingCbt) {
                Bill::create([
                    'type' => 'receivable',
                    'bill_number' => 'R-' . now()->format('Ymd') . '-CBT43',
                    'party_id' => $partyCbt->id,
                    'project_id' => $projectCbt->id,
                    'issue_date' => now()->subDays(10)->toDateString(), // Estimate: around project start
                    'due_date' => now()->addDays(30)->toDateString(),
                    'description' => 'Sisa piutang DP project Sistem CBT SMAN 43 Jakarta',
                    'amount' => 3000000,
                    'paid_amount' => 0,
                    'outstanding_amount' => 3000000,
                    'status' => 'unpaid',
                    'notes' => 'Piutang DP project CBT SMAN 43 Jakarta belum diterima',
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
                $this->command->info('Created receivable: CBT SMAN 43 - Rp3.000.000');
            } else {
                $this->command->warn('Receivable CBT SMAN 43 already exists, skipped.');
            }

            DB::commit();
            $this->command->info('Project receivables seeded successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}