<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\TaskFrequency;
use App\Models\RecurringTask;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RecurringTask
 */
class RecurringTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $frequency = $this->frequency instanceof TaskFrequency
            ? $this->frequency
            : TaskFrequency::from((string) $this->frequency);

        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->uuid,
                'name' => $this->category?->name,
            ]),
            'frequency' => $frequency->value,
            'frequency_label' => $this->frequencyLabel($frequency),
            'frequency_details' => $this->frequencyDetails($frequency),
            'weekly_days' => $frequency === TaskFrequency::Weekly
                ? ($this->frequency_config['days'] ?? [])
                : [],
            'monthly_day' => $frequency === TaskFrequency::Monthly
                ? ($this->frequency_config['day'] ?? null)
                : null,
            'start_date' => new DateTimeResource($this->start_date, includeTime: false)->resolve($request),
            'end_date' => new DateTimeResource($this->end_date, includeTime: false)->resolve($request),
            'created_at' => new DateTimeResource($this->created_at)->resolve($request),
            'updated_at' => new DateTimeResource($this->updated_at)->resolve($request),
        ];
    }

    private function frequencyLabel(TaskFrequency $frequency): string
    {
        return match ($frequency) {
            TaskFrequency::Daily => 'Daily',
            TaskFrequency::Weekdays => 'Weekdays',
            TaskFrequency::Weekly => 'Weekly',
            TaskFrequency::Monthly => 'Monthly',
        };
    }

    private function frequencyDetails(TaskFrequency $frequency): string
    {
        return match ($frequency) {
            TaskFrequency::Daily => 'Every day',
            TaskFrequency::Weekdays => 'Mon to Fri',
            TaskFrequency::Weekly => $this->weeklyFrequencyDetails(),
            TaskFrequency::Monthly => 'Day '.($this->frequency_config['day'] ?? '-'),
        };
    }

    private function weeklyFrequencyDetails(): string
    {
        $labels = [
            'monday' => 'Mon',
            'tuesday' => 'Tue',
            'wednesday' => 'Wed',
            'thursday' => 'Thu',
            'friday' => 'Fri',
            'saturday' => 'Sat',
            'sunday' => 'Sun',
        ];

        $days = array_filter(
            array_map(fn (mixed $day): string => $labels[$day] ?? '', $this->frequency_config['days'] ?? [])
        );

        return $days ? implode(', ', $days) : '-';
    }
}
