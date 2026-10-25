<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$s = app(App\Services\CashBalanceService::class);
$totals = $s->getMonthlyTotals();
foreach ($totals as $m => $v) {
    if ($v['income'] || $v['expense']) {
        echo $m.': in='.$v['income'].' out='.$v['expense'].' net='.$v['net'].PHP_EOL;
    }
}
echo 'BALANCE='.$s->getCurrentBalance().PHP_EOL;
