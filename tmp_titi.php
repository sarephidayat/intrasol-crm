<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
foreach(App\Models\CashTransaction::with(["project"])->where("description","like","%titi%")->orWhere("description","like","%Titi%")->orderBy("transaction_date")->get() as $t){ echo $t->id."|".$t->transaction_date->format("Y-m-d")."|".$t->type."|".$t->amount."|".($t->project?\$t->project->name:"-")."|".$t->description.PHP_EOL; }
