<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('auth:clear-resets')->hourly();

Schedule::command('demo:reset')->daily()->when(fn () => config('billify.demo_mode'));
