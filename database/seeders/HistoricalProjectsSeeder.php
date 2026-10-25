<?php

namespace Database\Seeders;

use App\Models\Party;
use App\Models\Project;
use App\Models\CashTransaction;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricalProjectsSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // ===========================================
            // 1. CREATE/GET PARTIES (CLIENTS)
            // ===========================================
            
            $partyUndip = Party::firstOrCreate(
                ['name' => 'UNDIP'],
                [
                    'role' => 'client',
                    'primary_pic_name' => 'Santoso',
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => 'Universitas Diponegoro',
                    'notes' => 'Klien project Web SDGs UNDIP / Pasca Sarjana KPI',
                    'is_active' => true,
                ]
            );

            $partyS2Kpi = Party::firstOrCreate(
                ['name' => 'S2 KPI'],
                [
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => null,
                    'notes' => 'Klien project Web S2 KPI',
                    'is_active' => true,
                ]
            );

            $partyFisika = Party::firstOrCreate(
                ['name' => 'Fisika UNDIP'],
                [
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => 'Jurusan Fisika, UIN Walisongo',
                    'notes' => 'Klien project Web Pisin Competition / Website Fisika',
                    'is_active' => true,
                ]
            );

            $partyBuTiti = Party::firstOrCreate(
                ['name' => 'Bu Titi - Web Administrasi'],
                [
                    'role' => 'client',
                    'primary_pic_name' => 'Bu Titi',
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => null,
                    'notes' => 'Klien project Web Administrasi',
                    'is_active' => true,
                ]
            );

            $partyGemilang = Party::firstOrCreate(
                ['name' => 'CV Gemilang Wijaya Perkasa'],
                [
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => null,
                    'notes' => 'Klien project Sistem Website + Company Profile',
                    'is_active' => true,
                ]
            );

            $partyCbt = Party::firstOrCreate(
                ['name' => 'SMAN 43 Jakarta'],
                [
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => 'SMAN 43 Jakarta',
                    'notes' => 'Klien project Sistem CBT',
                    'is_active' => true,
                ]
            );

            // Update existing projects with party_id
            $projectGemilang = Project::where('code', 'GEMILANG')->first();
            if ($projectGemilang && !$projectGemilang->party_id) {
                $projectGemilang->update(['party_id' => $partyGemilang->id]);
            }

            $projectCbt = Project::where('code', 'CBT-SMAN43')->first();
            if ($projectCbt && !$projectCbt->party_id) {
                $projectCbt->update(['party_id' => $partyCbt->id]);
            }

            $projectBuTiti = Project::where('code', 'BU-TITI')->first();
            if ($projectBuTiti && !$projectBuTiti->party_id) {
                $projectBuTiti->update(['party_id' => $partyBuTiti->id]);
            }

            // ===========================================
            // 2. CREATE MISSING HISTORICAL PROJECTS
            // ===========================================
            
            $projects = [
                [
                    'code' => 'UNDIP-SDGS',
                    'name' => 'Web SDGs UNDIP / Pasca Sarjana KPI',
                    'party_id' => $partyUndip->id,
                    'description' => 'Website SDGs UNDIP dan Website Pasca Sarjana KPI UIN Walisongo',
                    'status' => 'completed',
                    'is_active' => false,
                ],
                [
                    'code' => 'UNDIP-S2KPI',
                    'name' => 'Web S2 KPI',
                    'party_id' => $partyS2Kpi->id,
                    'description' => 'Website S2 KPI UIN Walisongo',
                    'status' => 'completed',
                    'is_active' => false,
                ],
                [
                    'code' => 'UNDIP-FISIKA',
                    'name' => 'Web Pisin Competition / Website Fisika UNDIP',
                    'party_id' => $partyFisika->id,
                    'description' => 'Website Pisin Competition dan Website Fisika UIN Walisongo (fisika.pisinxi.or.id)',
                    'status' => 'completed',
                    'is_active' => false,
                ],
                [
                    'code' => 'BU-TITI-WEB',
                    'name' => 'Web Administrasi Bu Titi',
                    'party_id' => $partyBuTiti->id,
                    'description' => 'Website Administrasi untuk Bu Titi',
                    'status' => 'completed',
                    'is_active' => false,
                ],
            ];

            foreach ($projects as $projData) {
                Project::firstOrCreate(
                    ['code' => $projData['code']],
                    $projData
                );
            }

            // ===========================================
            // 3. CONNECT HISTORICAL TRANSACTIONS TO PROJECTS
            // ===========================================
            
            $projectUndip = Project::where('code', 'UNDIP-SDGS')->first();
            $projectS2Kpi = Project::where('code', 'UNDIP-S2KPI')->first();
            $projectFisika = Project::where('code', 'UNDIP-FISIKA')->first();
            $projectBuTitiWeb = Project::where('code', 'BU-TITI-WEB')->first();

            $updates = [
                // UNDIP SDGS / Pasca Sarjana KPI
                ['desc' => 'Project Website Pasca Sarjana KPI UIN Walisongo', 'project_id' => $projectUndip?->id],
                ['desc' => 'DP Project Undip', 'project_id' => $projectUndip?->id],
                ['desc' => 'DP Gaji Developer Project Undip', 'project_id' => $projectUndip?->id],
                ['desc' => 'cicilan pelunasan project undip', 'project_id' => $projectUndip?->id],
                ['desc' => 'cicilan gaji developer project undip', 'project_id' => $projectUndip?->id],
                ['desc' => 'Pelunasan project undip [Santoso]', 'project_id' => $projectUndip?->id],
                ['desc' => 'Bayar pelunasan gaji karyawan [yudi]', 'project_id' => $projectUndip?->id],

                // S2 KPI
                ['desc' => 'Bayar Gaji Developer project s2 kpi', 'project_id' => $projectS2Kpi?->id],
                ['desc' => 'Fee 100 design UI/UX', 'project_id' => $projectS2Kpi?->id],

                // Fisika UNDIP
                ['desc' => 'Project website fisika uin walisongo', 'project_id' => $projectFisika?->id],
                ['desc' => 'Beli domain web fisika pisinxi.or.id', 'project_id' => $projectFisika?->id],
                ['desc' => 'Gaji developer sales', 'project_id' => $projectFisika?->id], // Could be related

                // Bu Titi Web Administrasi
                ['desc' => 'Pelunasan project web administrasi [Bu titi]', 'project_id' => $projectBuTitiWeb?->id],
                ['desc' => 'Bayar gaji developer project [Bu titi]', 'project_id' => $projectBuTitiWeb?->id],
                ['desc' => 'Hosting + Domain Website Administrasi bu titi', 'project_id' => $projectBuTitiWeb?->id],
            ];

            foreach ($updates as $update) {
                if ($update['project_id']) {
                    CashTransaction::where('description', $update['desc'])
                        ->whereNull('project_id')
                        ->update(['project_id' => $update['project_id'], 'updated_by' => 1]);
                }
            }

            // ===========================================
            // 4. CONNECT SEPTEMBER EXPENSES TO GEMILANG
            // ===========================================
            // These were added in the import but may need party_id
            $sepExpenses = [
                'Bayar desain lanyard + ID card',
                'Bayar konveksi lanyard',
            ];
            foreach ($sepExpenses as $desc) {
                CashTransaction::where('description', $desc)
                    ->where('project_id', $projectGemilang->id)
                    ->whereNull('party_id')
                    ->update(['party_id' => $partyGemilang->id, 'updated_by' => 1]);
            }

            // ===========================================
            // 5. CONNECT CBT TRANSACTIONS
            // ===========================================
            $cbtDesc = [
                'Bonus sales project Sistem CBT SMAN 43 Jakarta',
                'DP gaji developer project Sistem CBT SMAN 43 Jakarta',
                'DP project Sistem CBT SMAN 43 Jakarta',
            ];
            foreach ($cbtDesc as $desc) {
                CashTransaction::where('description', $desc)
                    ->where('project_id', $projectCbt->id)
                    ->whereNull('party_id')
                    ->update(['party_id' => $partyCbt->id, 'updated_by' => 1]);
            }

            // ===========================================
            // 6. CONNECT BU TITI EXISTING TRANSACTION
            // ===========================================
            CashTransaction::where('description', 'Gaji tim support project Bu Titi')
                ->where('project_id', $projectBuTiti->id)
                ->whereNull('party_id')
                ->update(['party_id' => $partyBuTiti->id, 'updated_by' => 1]);

            DB::commit();
            $this->command->info('Historical projects and transactions connected successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}