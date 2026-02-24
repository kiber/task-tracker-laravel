<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


Schedule::command('auth:clear-resets')->daily();
