<?php

namespace Database\Seeders;

use App\Models\Party;
use App\Models\Project;
use App\Models\Category;
use App\Models\CashTransaction;
use App\Models\Bill;
use App\Models\Reconciliation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FullDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding full application data...');

        DB::beginTransaction();
        try {
            // ===========================================
            // 1. PARTIES (6 clients)
            // ===========================================
            $parties = [
                [
                    'name' => 'CV Gemilang Wijaya Perkasa',
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => null,
                    'notes' => 'Klien project Sistem Website + Company Profile',
                    'is_active' => true,
                ],
                [
                    'name' => 'SMAN 43 Jakarta',
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => 'SMAN 43 Jakarta',
                    'notes' => 'Klien project Sistem CBT',
                    'is_active' => true,
                ],
                [
                    'name' => 'Bu Titi - Web Administrasi',
                    'role' => 'client',
                    'primary_pic_name' => 'Bu Titi',
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => null,
                    'notes' => 'Klien project Web Administrasi',
                    'is_active' => true,
                ],
                [
                    'name' => 'UNDIP',
                    'role' => 'client',
                    'primary_pic_name' => 'Santoso',
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => 'Universitas Diponegoro',
                    'notes' => 'Klien project Web SDGs UNDIP / Pasca Sarjana KPI',
                    'is_active' => true,
                ],
                [
                    'name' => 'S2 KPI',
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => null,
                    'notes' => 'Klien project Web S2 KPI',
                    'is_active' => true,
                ],
                [
                    'name' => 'Fisika UNDIP',
                    'role' => 'client',
                    'primary_pic_name' => null,
                    'primary_pic_email' => null,
                    'primary_pic_phone' => null,
                    'address' => 'Jurusan Fisika, UIN Walisongo',
                    'notes' => 'Klien project Web Pisin Competition / Website Fisika',
                    'is_active' => true,
                ],
            ];

            $partyMap = [];
            foreach ($parties as $partyData) {
                $party = Party::firstOrCreate(
                    ['name' => $partyData['name']],
                    $partyData
                );
                $partyMap[$partyData['name']] = $party->id;
            }

            // ===========================================
            // 2. PROJECTS (7 projects)
            // ===========================================
            $projects = [
                [
                    'code' => 'GEMILANG',
                    'name' => 'Sistem Website + Company Profile CV Gemilang Wijaya Perkasa',
                    'party_name' => 'CV Gemilang Wijaya Perkasa',
                    'description' => 'Project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa',
                    'status' => 'active',
                    'is_active' => true,
                ],
                [
                    'code' => 'CBT-SMAN43',
                    'name' => 'Sistem CBT SMAN 43 Jakarta',
                    'party_name' => 'SMAN 43 Jakarta',
                    'description' => 'Project Sistem CBT SMAN 43 Jakarta',
                    'status' => 'active',
                    'is_active' => true,
                ],
                [
                    'code' => 'BU-TITI',
                    'name' => 'Project Bu Titi',
                    'party_name' => 'Bu Titi - Web Administrasi',
                    'description' => 'Project support Bu Titi',
                    'status' => 'active',
                    'is_active' => true,
                ],
                [
                    'code' => 'UNDIP-SDGS',
                    'name' => 'Web SDGs UNDIP / Pasca Sarjana KPI',
                    'party_name' => 'UNDIP',
                    'description' => 'Website SDGs UNDIP dan Website Pasca Sarjana KPI UIN Walisongo',
                    'status' => 'completed',
                    'is_active' => false,
                ],
                [
                    'code' => 'UNDIP-S2KPI',
                    'name' => 'Web S2 KPI',
                    'party_name' => 'S2 KPI',
                    'description' => 'Website S2 KPI UIN Walisongo',
                    'status' => 'completed',
                    'is_active' => false,
                ],
                [
                    'code' => 'UNDIP-FISIKA',
                    'name' => 'Web Pisin Competition / Website Fisika UNDIP',
                    'party_name' => 'Fisika UNDIP',
                    'description' => 'Website Pisin Competition dan Website Fisika UIN Walisongo (fisika.pisinxi.or.id)',
                    'status' => 'completed',
                    'is_active' => false,
                ],
                [
                    'code' => 'BU-TITI-WEB',
                    'name' => 'Web Administrasi Bu Titi',
                    'party_name' => 'Bu Titi - Web Administrasi',
                    'description' => 'Website Administrasi untuk Bu Titi',
                    'status' => 'completed',
                    'is_active' => false,
                ],
            ];

            $projectMap = [];
            foreach ($projects as $projData) {
                $partyId = $partyMap[$projData['party_name']] ?? null;
                unset($projData['party_name']);
                $projData['party_id'] = $partyId;

                $project = Project::firstOrCreate(
                    ['code' => $projData['code']],
                    $projData
                );
                $projectMap[$projData['code']] = $project->id;
            }

            // ===========================================
            // 3. CASH TRANSACTIONS (46 transactions)
            // ===========================================
            $userId = User::first()?->id ?? 1;

            // Helper to get category ID by name and type
            $getCat = function ($name, $type) {
                return Category::where('name', $name)->where('type', $type)->value('id');
            };

            $catModal = $getCat('Modal', 'income');
            $catPartner = $getCat('Partner', 'expense');
            $catAdmin = $getCat('Administrasi', 'expense');
            $catProjectIncome = $getCat('Project Income', 'income');
            $catProject = $getCat('Project', 'income');
            $catGajiDev = $getCat('Gaji Developer', 'expense');
            $catLearning = $getCat('Learning', 'expense');
            $catPengeluaranProject = $getCat('Pengeluaran Project', 'expense');
            $catGathering = $getCat('Gathering', 'expense');
            $catOperational = $getCat('Operational', 'expense');
            $catSubscription = $getCat('Subscription', 'expense');
            $catOther = $getCat('Other', 'expense');
            $catProjectExpense = $getCat('Project Expense', 'expense');

            $transactions = [
                // Existing historical transactions (29)
                ['date' => '2026-03-01', 'type' => 'income', 'amount' => 9300000, 'category_id' => $catModal, 'project_id' => null, 'party_id' => null, 'description' => 'Modal dari pak Syaiful'],
                ['date' => '2026-03-15', 'type' => 'expense', 'amount' => 6800000, 'category_id' => $catPartner, 'project_id' => null, 'party_id' => null, 'description' => 'Partner Learning Odoo'],
                ['date' => '2026-06-10', 'type' => 'expense', 'amount' => 1500000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'DP Legal Perusahaan'],
                ['date' => '2026-06-17', 'type' => 'income', 'amount' => 1200000, 'category_id' => $catProject, 'project_id' => $projectMap['UNDIP-SDGS'], 'party_id' => $partyMap['UNDIP'], 'description' => 'Project Website Pasca Sarjana KPI UIN Walisongo'],
                ['date' => '2026-06-18', 'type' => 'expense', 'amount' => 840000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['UNDIP-S2KPI'], 'party_id' => null, 'description' => 'Bayar Gaji Developer project s2 kpi'],
                ['date' => '2026-06-18', 'type' => 'expense', 'amount' => 1000000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['UNDIP-S2KPI'], 'party_id' => null, 'description' => 'Fee 100 design UI/UX'],
                ['date' => '2026-07-02', 'type' => 'expense', 'amount' => 20000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Print notaris + materai'],
                ['date' => '2026-07-03', 'type' => 'income', 'amount' => 7500000, 'category_id' => $catProject, 'project_id' => $projectMap['UNDIP-SDGS'], 'party_id' => $partyMap['UNDIP'], 'description' => 'DP Project Undip'],
                ['date' => '2026-07-03', 'type' => 'expense', 'amount' => 7500000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['UNDIP-SDGS'], 'party_id' => null, 'description' => 'DP Gaji Developer Project Undip'],
                ['date' => '2026-07-05', 'type' => 'expense', 'amount' => 51000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Design logo'],
                ['date' => '2026-07-14', 'type' => 'expense', 'amount' => 50000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Print Minuta A3'],
                ['date' => '2026-07-18', 'type' => 'expense', 'amount' => 11000, 'category_id' => $catLearning, 'project_id' => null, 'party_id' => null, 'description' => 'Beli Domain web.id dan biz.id'],
                ['date' => '2026-07-18', 'type' => 'expense', 'amount' => 15500, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Kirim Paket Minuta'],
                ['date' => '2026-07-28', 'type' => 'income', 'amount' => 450000, 'category_id' => $catProject, 'project_id' => $projectMap['UNDIP-FISIKA'], 'party_id' => $partyMap['Fisika UNDIP'], 'description' => 'Project website fisika uin walisongo'],
                ['date' => '2026-07-28', 'type' => 'expense', 'amount' => 50000, 'category_id' => $catPengeluaranProject, 'project_id' => $projectMap['UNDIP-FISIKA'], 'party_id' => null, 'description' => 'Beli domain web fisika pisinxi.or.id'],
                ['date' => '2026-07-28', 'type' => 'expense', 'amount' => 280000, 'category_id' => $catGajiDev, 'project_id' => null, 'party_id' => null, 'description' => 'Gaji developer sales'],
                ['date' => '2026-08-27', 'type' => 'income', 'amount' => 667500, 'category_id' => $catModal, 'project_id' => null, 'party_id' => null, 'description' => 'Iuran pelunasan pembuatan PT'],
                ['date' => '2026-08-28', 'type' => 'expense', 'amount' => 1000000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Pelunasan PT Intrasol'],
                ['date' => '2026-09-01', 'type' => 'income', 'amount' => 4000000, 'category_id' => $catProject, 'project_id' => $projectMap['UNDIP-SDGS'], 'party_id' => $partyMap['UNDIP'], 'description' => 'cicilan pelunasan project undip'],
                ['date' => '2026-09-02', 'type' => 'expense', 'amount' => 2000000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['UNDIP-SDGS'], 'party_id' => null, 'description' => 'cicilan gaji developer project undip'],
                ['date' => '2026-09-03', 'type' => 'income', 'amount' => 3000000, 'category_id' => $catProject, 'project_id' => $projectMap['BU-TITI-WEB'], 'party_id' => $partyMap['Bu Titi - Web Administrasi'], 'description' => 'Pelunasan project web administrasi [Bu titi]'],
                ['date' => '2026-09-05', 'type' => 'expense', 'amount' => 1000000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['BU-TITI-WEB'], 'party_id' => null, 'description' => 'Bayar gaji developer project [Bu titi]'],
                ['date' => '2026-09-06', 'type' => 'income', 'amount' => 3500000, 'category_id' => $catProject, 'project_id' => $projectMap['UNDIP-SDGS'], 'party_id' => $partyMap['UNDIP'], 'description' => 'Pelunasan project undip [Santoso]'],
                ['date' => '2026-09-06', 'type' => 'expense', 'amount' => 600000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['UNDIP-SDGS'], 'party_id' => null, 'description' => 'Bayar pelunasan gaji karyawan [yudi]'],
                ['date' => '2026-09-06', 'type' => 'expense', 'amount' => 221000, 'category_id' => $catGathering, 'project_id' => null, 'party_id' => null, 'description' => 'Makan makan di wss'],
                ['date' => '2026-09-06', 'type' => 'expense', 'amount' => 660000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Pengembalian iuran pelunasan PT'],
                ['date' => '2026-09-07', 'type' => 'expense', 'amount' => 6000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Print kebutuhan pembuatan rekening'],
                ['date' => '2026-09-12', 'type' => 'expense', 'amount' => 575760, 'category_id' => $catProject, 'project_id' => $projectMap['BU-TITI-WEB'], 'party_id' => null, 'description' => 'Hosting + Domain Website Administrasi bu titi'],
                ['date' => '2026-09-12', 'type' => 'expense', 'amount' => 848932, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Hosting + Domain Intrasol'],

                // Imported Sep-Oct 2026 transactions (17)
                ['date' => '2026-09-16', 'type' => 'expense', 'amount' => 34000, 'category_id' => $catPengeluaranProject, 'project_id' => $projectMap['GEMILANG'], 'party_id' => $partyMap['CV Gemilang Wijaya Perkasa'], 'description' => 'Bayar desain lanyard + ID card'],
                ['date' => '2026-09-17', 'type' => 'expense', 'amount' => 86000, 'category_id' => $catPengeluaranProject, 'project_id' => $projectMap['GEMILANG'], 'party_id' => $partyMap['CV Gemilang Wijaya Perkasa'], 'description' => 'Bayar konveksi lanyard'],
                ['date' => '2026-09-18', 'type' => 'expense', 'amount' => 46000, 'category_id' => $catOperational, 'project_id' => null, 'party_id' => null, 'description' => 'Meeting'],
                ['date' => '2026-09-19', 'type' => 'income', 'amount' => 12000000, 'category_id' => $catProjectIncome, 'project_id' => $projectMap['GEMILANG'], 'party_id' => $partyMap['CV Gemilang Wijaya Perkasa'], 'description' => 'Project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa'],
                ['date' => '2026-09-20', 'type' => 'expense', 'amount' => 300000, 'category_id' => $catOperational, 'project_id' => null, 'party_id' => null, 'description' => 'Sponsor SwitchFest'],
                ['date' => '2026-09-20', 'type' => 'expense', 'amount' => 6750000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['GEMILANG'], 'party_id' => null, 'description' => 'DP developer project CV Gemilang Wijaya Perkasa'],
                ['date' => '2026-09-20', 'type' => 'expense', 'amount' => 2000000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['GEMILANG'], 'party_id' => null, 'description' => 'Gaji Dimas untuk pembuatan 100 desain'],
                ['date' => '2026-09-21', 'type' => 'expense', 'amount' => 5000, 'category_id' => $catOperational, 'project_id' => null, 'party_id' => null, 'description' => 'Marketing INTRASOL ke HMJ TI'],
                ['date' => '2026-09-21', 'type' => 'expense', 'amount' => 450000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['BU-TITI'], 'party_id' => null, 'description' => 'Gaji tim support project Bu Titi'],
                ['date' => '2026-10-02', 'type' => 'expense', 'amount' => 50000, 'category_id' => $catLearning, 'project_id' => null, 'party_id' => null, 'description' => 'Workshop pelatihan sales'],
                ['date' => '2026-10-19', 'type' => 'expense', 'amount' => 300000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['CBT-SMAN43'], 'party_id' => null, 'description' => 'Bonus sales project Sistem CBT SMAN 43 Jakarta'],
                ['date' => '2026-10-19', 'type' => 'expense', 'amount' => 900000, 'category_id' => $catGajiDev, 'project_id' => $projectMap['CBT-SMAN43'], 'party_id' => null, 'description' => 'DP gaji developer project Sistem CBT SMAN 43 Jakarta'],
                ['date' => '2026-10-19', 'type' => 'income', 'amount' => 3000000, 'category_id' => $catProjectIncome, 'project_id' => $projectMap['CBT-SMAN43'], 'party_id' => $partyMap['SMAN 43 Jakarta'], 'description' => 'DP project Sistem CBT SMAN 43 Jakarta'],
                ['date' => '2026-10-20', 'type' => 'expense', 'amount' => 55000, 'category_id' => $catAdmin, 'project_id' => null, 'party_id' => null, 'description' => 'Pembuatan stempel PT INTRASOL'],
                ['date' => '2026-10-21', 'type' => 'expense', 'amount' => 244000, 'category_id' => $catSubscription, 'project_id' => null, 'party_id' => null, 'description' => 'Iklan Dribbble dan Google Ads'],
                ['date' => '2026-10-23', 'type' => 'expense', 'amount' => 300000, 'category_id' => $catPengeluaranProject, 'project_id' => $projectMap['GEMILANG'], 'party_id' => null, 'description' => 'Pembelian template project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa'],
                ['date' => '2026-10-24', 'type' => 'expense', 'amount' => 13044, 'category_id' => $catLearning, 'project_id' => $projectMap['GEMILANG'], 'party_id' => null, 'description' => 'Pembelian domain untuk learning konfigurasi NAS project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa'],
            ];

            foreach ($transactions as $txData) {
                CashTransaction::firstOrCreate(
                    [
                        'transaction_date' => $txData['date'],
                        'type' => $txData['type'],
                        'amount' => $txData['amount'],
                        'description' => $txData['description'],
                    ],
                    array_merge($txData, [
                        'receipt_path' => null,
                        'archived_at' => null,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ])
                );
            }

            // ===========================================
            // 4. RECEIVABLES (2 bills)
            // ===========================================
            $receivables = [
                [
                    'type' => 'receivable',
                    'bill_number' => 'R-20261025-GEMILANG',
                    'party_name' => 'CV Gemilang Wijaya Perkasa',
                    'project_code' => 'GEMILANG',
                    'issue_date' => '2026-09-25',
                    'due_date' => '2026-10-25',
                    'description' => 'Sisa piutang project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa',
                    'amount' => 12000000,
                    'paid_amount' => 0,
                    'outstanding_amount' => 12000000,
                    'status' => 'unpaid',
                    'notes' => 'Piutang belum diterima per catatan internal',
                ],
                [
                    'type' => 'receivable',
                    'bill_number' => 'R-20261025-CBT43',
                    'party_name' => 'SMAN 43 Jakarta',
                    'project_code' => 'CBT-SMAN43',
                    'issue_date' => '2026-10-15',
                    'due_date' => '2026-11-15',
                    'description' => 'Sisa piutang DP project Sistem CBT SMAN 43 Jakarta',
                    'amount' => 3000000,
                    'paid_amount' => 0,
                    'outstanding_amount' => 3000000,
                    'status' => 'unpaid',
                    'notes' => 'Piutang DP project CBT SMAN 43 Jakarta belum diterima',
                ],
            ];

            foreach ($receivables as $billData) {
                Bill::firstOrCreate(
                    ['bill_number' => $billData['bill_number']],
                    array_merge($billData, [
                        'party_id' => $partyMap[$billData['party_name']],
                        'project_id' => $projectMap[$billData['project_code']],
                        'attachment_path' => null,
                        'archived_at' => null,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ])
                );
            }

            // ===========================================
            // 5. RECONCILIATIONS (4 records)
            // ===========================================
            $systemBalance = 8055264; // Current calculated balance

            $reconciliations = [
                [
                    'reconciliation_date' => '2026-10-20',
                    'system_balance' => $systemBalance,
                    'actual_bank_balance' => 7900000,
                    'difference' => -155264,
                    'notes' => 'Rekonsiliasi minggu lalu - ada selisih kurang',
                    'status' => 'completed',
                    'completed_at' => '2026-10-20 10:00:00',
                ],
                [
                    'reconciliation_date' => '2026-10-23',
                    'system_balance' => $systemBalance,
                    'actual_bank_balance' => 8100000,
                    'difference' => 44736,
                    'notes' => 'Rekonsiliasi akhir bulan Oktober',
                    'status' => 'completed',
                    'completed_at' => '2026-10-23 15:30:00',
                ],
                [
                    'reconciliation_date' => '2026-10-25',
                    'system_balance' => $systemBalance,
                    'actual_bank_balance' => 8500000,
                    'difference' => 444736,
                    'notes' => 'Test rekonsiliasi manual',
                    'status' => 'completed',
                    'completed_at' => '2026-10-25 14:00:00',
                ],
                [
                    'reconciliation_date' => '2026-10-25',
                    'system_balance' => $systemBalance,
                    'actual_bank_balance' => 8055264,
                    'difference' => 0,
                    'notes' => 'Draft rekonsiliasi hari ini',
                    'status' => 'draft',
                    'completed_at' => null,
                ],
            ];

            foreach ($reconciliations as $recData) {
                Reconciliation::firstOrCreate(
                    [
                        'reconciliation_date' => $recData['reconciliation_date'],
                        'actual_bank_balance' => $recData['actual_bank_balance'],
                    ],
                    array_merge($recData, [
                        'bank_statement_id' => null,
                        'created_by' => $userId,
                    ])
                );
            }

            DB::commit();
            $this->command->info('Full data seeded successfully!');
            $this->command->info('Parties: ' . count($parties));
            $this->command->info('Projects: ' . count($projects));
            $this->command->info('Transactions: ' . count($transactions));
            $this->command->info('Receivables: ' . count($receivables));
            $this->command->info('Reconciliations: ' . count($reconciliations));

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}