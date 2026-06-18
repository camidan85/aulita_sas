<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Corre cada minuto; el comando procesa cada escuela cuando llega su hora de corte.
Schedule::command('asistencia:detectar-faltas')
    ->everyMinute()
    ->withoutOverlapping();
