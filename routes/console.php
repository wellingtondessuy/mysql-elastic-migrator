<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use App\Migrator\Application;

Schedule::call(new Application)->everyMinute();
