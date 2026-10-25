<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $income = [
            ['name' => 'Project Income', 'type' => 'income'],
            ['name' => 'Other Income', 'type' => 'income'],
            ['name' => 'Owner Capital', 'type' => 'income'],
        ];

        $expense = [
            ['name' => 'Operational', 'type' => 'expense'],
            ['name' => 'Project Expense', 'type' => 'expense'],
            ['name' => 'Subscription', 'type' => 'expense'],
            ['name' => 'Transport', 'type' => 'expense'],
            ['name' => 'Salary', 'type' => 'expense'],
            ['name' => 'Other', 'type' => 'expense'],
        ];

        foreach (array_merge($income, $expense) as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']],
                ['is_active' => true]
            );
        }
    }
}
