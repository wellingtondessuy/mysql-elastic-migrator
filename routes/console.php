<?php

use Illuminate\Support\Facades\Schedule;
use App\Migrator\Application;

Schedule::call(new Application)->everyMinute();
