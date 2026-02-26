<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\TaskFrequency;
use App\Models\RecurringTask;
use App\Models\Task;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-recurring-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring tasks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetDate = today();

        $recurringTasksQuery = RecurringTask::query()
//            ->active()
            ->where(fn(Builder $query) => $query->whereNull('start_date')->orWhere('start_date', '<=', $targetDate))
            ->where(fn(Builder $query) => $query->whereNull('end_date')->orWhere('end_date', '>=', $targetDate))
            ->whereDoesntHave('tasks', fn(Builder $query) => $query->whereDate('task_date', $targetDate));

        $totalRecurringTasks = $recurringTasksQuery->count();
        if (!$totalRecurringTasks) {
            $this->info('No recurring tasks found.');
            return self::FAILURE;
        }

        $this->info('Processing ' . $totalRecurringTasks . ' recurring task templates...');

        $created = 0;
        $skipped = 0;

        $recurringTasksQuery->chunkById(
            500,
            function (Collection $recurringTasks) use ($targetDate, &$skipped, &$created) {
                try {
                    $insertTasksBatch = [];
                    foreach ($recurringTasks as $recurringTask) {
                        try {
                            if (!$this->isRecurringTaskDue($recurringTask, $targetDate)) {
                                $skipped++;
                                continue;
                            }

                            $now = new DateTime();
                            $insertTasksBatch[] = [
                                'uuid' => (string) Str::uuid7(),
                                'user_id' => $recurringTask->user_id,
                                'category_id' => $recurringTask->category_id,
                                'recurring_task_id' => $recurringTask->id,
                                'title' => $recurringTask->title,
                                'description' => $recurringTask->description,
                                'task_date' => $targetDate,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        } catch (Exception $exception) {
                            report($exception);
                        }
                    }

                    if ($insertTasksBatch) {
                        Task::insert($insertTasksBatch);
                        $created += count($insertTasksBatch);
                    }
                } catch (Exception $exception) {
                    report($exception);
                }
            }
        );

        $this->info('Created ' . $created . ' recurring tasks.');

        if ($skipped > 0) {
            $this->warn('Skipped ' . $created . ' recurring tasks.');
        }

        $this->newLine();

        return self::SUCCESS;
    }

    private function isRecurringTaskDue(mixed $recurringTask, Carbon $targetDate): bool
    {
        return match ($recurringTask->frequency) {
            TaskFrequency::Daily => true,
            TaskFrequency::Weekdays => $targetDate->isWeekday(),
            TaskFrequency::Weekly => $this->isWeeklyRecurringTaskDue($recurringTask, $targetDate),
            TaskFrequency::Monthly => $this->isMonthlyRecurringTaskDue($recurringTask, $targetDate),
            default => false,
        };
    }

    private function isWeeklyRecurringTaskDue(mixed $recurringTask, Carbon $targetDate): bool
    {
        $config = $recurringTask->frequency_config;
        if (!$config || !isset($config['days']) || !is_array($config['days'])) {
            return false;
        }

        return in_array(strtolower($targetDate->englishDayOfWeek), $config['days']);
    }

    private function isMonthlyRecurringTaskDue(mixed $recurringTask, Carbon $targetDate): bool
    {
        $config = $recurringTask->frequency_config;
        if (!$config || !isset($config['day'])) {
            return false;
        }

        $dayOfMonth = min($config['day'], $targetDate->daysInMonth);

        return $targetDate->day === $dayOfMonth;
    }
}
