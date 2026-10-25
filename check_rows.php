<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\CashTransaction::where('transaction_date', '>=', '2026-07-28')
    ->where('transaction_date', '<=', '2026-08-28')
    ->orderBy('transaction_date')->orderBy('id')->get() as $t) {
    echo $t->transaction_date->format('d/m/Y').' | '.$t->description.' | '.$t->type.' | '.$t->amount.PHP_EOL;
}

