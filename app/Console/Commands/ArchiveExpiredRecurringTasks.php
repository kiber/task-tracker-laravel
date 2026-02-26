<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\RecurringTask;
use Illuminate\Console\Command;

class ArchiveExpiredRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:archive-expired-recurring-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive expired recurring tasks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetDate = today();

        $expired = RecurringTask::query()->whereNotNull('end_date')
            ->whereDate('end_date', '<', $targetDate)
            ->delete();

        if ($expired > 0) {
            $this->info('Archived ' . $expired . ' recurring tasks.');
        } else {
            $this->info('There are no archived recurring tasks.');
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
