<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('devices:mark-offline')
    ->everySecond()
    ->withoutOverlapping();
