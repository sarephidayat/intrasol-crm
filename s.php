<?php
require 'vendor/autoload.php';
\ = require 'bootstrap/app.php';
\ = \->make('Illuminate\Contracts\Console\Kernel');
\->bootstrap();
\ = Illuminate\Support\Facades\DB::table('cash_transactions')->whereBetween('transaction_date', ['2026-09-01','2026-10-31'])->orderBy('transaction_date')->get();
foreach(\ as \){ echo \->id.'|'.\->transaction_date.'|'.\->type.'|'.\->amount.'|'.\->description.PHP_EOL; }

