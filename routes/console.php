<?php

Schedule::command('auth:clear-resets')->daily();
Schedule::command('app:generate-recurring-tasks')
    ->everyMinute()
    ->withoutOverlapping();
Schedule::command('app:generate-expired-recurring-tasks')
    ->dailyAt('00:15')
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/expired-recurring-tasks.log'));
