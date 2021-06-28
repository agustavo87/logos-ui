<?php

use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('i-test', function () {
    $sources = app(SourcesRepository::class);
    $source = $sources->getLike('title', 'novias')[0]->toArray();
    dd($source);
})->purpose('Test an ongoing piece of code');
