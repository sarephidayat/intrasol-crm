<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = App\Models\CashTransaction::with(["project","category","party"])->orderBy("transaction_date")->get();
foreach ($items as $t) {
    echo $t->transaction_date->format("Y-m-d")."|".$t->type."|".$t->amount."|".($t->project ? $t->project->name : "-")."|".($t->category ? $t->category->name : "-")."|".$t->description.PHP_EOL;
}
