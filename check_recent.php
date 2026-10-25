<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$descs = [
  "Project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa",
  "DP project Sistem CBT SMAN 43 Jakarta",
  "Bayar desain lanyard + ID card",
  "Bayar konveksi lanyard",
  "Meeting",
  "Sponsor SwitchFest",
  "DP developer project CV Gemilang Wijaya Perkasa",
  "Gaji Dimas untuk pembuatan 100 desain",
  "Marketing INTRASOL ke HMJ TI",
  "Gaji tim support project Bu Titi",
  "Workshop pelatihan sales",
  "Bonus sales project Sistem CBT SMAN 43 Jakarta",
  "DP gaji developer project Sistem CBT SMAN 43 Jakarta",
  "Pembuatan stempel PT INTRASOL",
  "Iklan Dribbble dan Google Ads",
  "Pembelian template project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa",
  "Pembelian domain untuk learning konfigurasi NAS project Sistem Website + Company Profile CV Gemilang Wijaya Perkasa",
];
$items = App\Models\CashTransaction::whereIn("description", $descs)->with(["project","category"])->orderBy("transaction_date")->get();
foreach($items as $t){
  echo $t->transaction_date->format("Y-m-d")."|".$t->type."|".$t->amount."|".($t->project?$t->project->name:"-")."|".$t->category->name.PHP_EOL;
}
echo "TOTAL:".$items->count().PHP_EOL;

