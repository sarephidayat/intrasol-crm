<?php

namespace Database\Seeders;

use App\Models\CashTransaction;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixProjectConnectionsSeeder extends Seeder
{
    public function run(): void
    {
        $projectGemilang = Project::where('code', 'GEMILANG')->first();
        $projectFisika = Project::where('code', 'UNDIP-FISIKA')->first();

        DB::beginTransaction();
        try {
            // Connect Gemilang September expenses (lanyard)
            $gemilangExpenses = [
                'Bayar desain lanyard + ID card',
                'Bayar konveksi lanyard',
            ];
            foreach ($gemilangExpenses as $desc) {
                $updated = CashTransaction::where('description', $desc)
                    ->whereNull('project_id')
                    ->update(['project_id' => $projectGemilang?->id, 'updated_by' => 1]);
                if ($updated) {
                    $this->command->info("Connected: {$desc} -> GEMILANG");
                }
            }

            // The "Gaji developer sales" on 2026-07-28 - check if it's really for Fisika
            // Description is generic, might be general company expense. Let's leave it for now
            // or disconnect if it was incorrectly assigned
            // Actually, the description "Gaji developer sales" doesn't clearly indicate Fisika project
            // Let's disconnect it from Fisika since it's ambiguous
            $disconnected = CashTransaction::where('description', 'Gaji developer sales')
                ->where('project_id', $projectFisika?->id)
                ->update(['project_id' => null, 'updated_by' => 1]);
            if ($disconnected) {
                $this->command->warn("Disconnected ambiguous: Gaji developer sales from Fisika project");
            }

            DB::commit();
            $this->command->info('Project connections fixed.');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}