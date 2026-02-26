<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TaskFrequency;
use App\Http\Requests\StoreRecurringTaskRequest;
use App\Http\Requests\UpdateRecurringTaskRequest;
use App\Models\Category;
use App\Models\RecurringTask;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class RecurringTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $recurringTasks = $request->user()->recurringTasks()
            ->with('category')
            ->latest()
            ->paginate();

        return view('recurring-tasks.index', [
            'recurringTasks' => $recurringTasks->toResourceCollection()->resolve(),
            'links' => fn () => $recurringTasks->links(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = $request->user()->categories()->orderBy('name')->pluck('name', 'uuid')->toArray();

        return view('recurring-tasks.create', [
            'categories' => $categories,
            'frequencies' => $this->frequencyOptions(),
            'weekdays' => $this->weekdayOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecurringTaskRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['category_id'] = $this->resolveCategoryId($request, $request->input('category_id'));
        $validatedData['frequency_config'] = $this->buildFrequencyConfig($validatedData);

        unset($validatedData['weekly_days'], $validatedData['monthly_day']);

        $request->user()->recurringTasks()->create($validatedData);

        return to_route('recurring-tasks.index')->with('success', 'Recurring task created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, RecurringTask $recurringTask)
    {
        $recurringTask->load('category');
        $categories = $request->user()->categories()->orderBy('name')->pluck('name', 'uuid')->toArray();

        return view('recurring-tasks.edit', [
            'recurringTask' => $recurringTask->toResource()->resolve(),
            'categories' => $categories,
            'frequencies' => $this->frequencyOptions(),
            'weekdays' => $this->weekdayOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecurringTaskRequest $request, RecurringTask $recurringTask)
    {
        $validatedData = $request->validated();
        $validatedData['category_id'] = $this->resolveCategoryId($request, $request->input('category_id'));
        $validatedData['frequency_config'] = $this->buildFrequencyConfig($validatedData);

        unset($validatedData['weekly_days'], $validatedData['monthly_day']);

        $recurringTask->update($validatedData);

        return to_route('recurring-tasks.index')->with('success', 'Recurring task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecurringTask $recurringTask)
    {
        $recurringTask->delete();

        return to_route('recurring-tasks.index')->with('success', 'Recurring task deleted successfully.');
    }

    /**
     * @return array<string, string>
     */
    private function frequencyOptions(): array
    {
        return [
            TaskFrequency::Daily->value => 'Daily',
            TaskFrequency::Weekdays->value => 'Weekdays',
            TaskFrequency::Weekly->value => 'Weekly',
            TaskFrequency::Monthly->value => 'Monthly',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function weekdayOptions(): array
    {
        return [
            'monday' => 'Mon',
            'tuesday' => 'Tue',
            'wednesday' => 'Wed',
            'thursday' => 'Thu',
            'friday' => 'Fri',
            'saturday' => 'Sat',
            'sunday' => 'Sun',
        ];
    }

    private function resolveCategoryId(Request $request, ?string $categoryUuid): ?int
    {
        if (!$categoryUuid) {
            return null;
        }

        $category = Category::where('uuid', $categoryUuid)->first();
        if (!$category || $request->user()->cannot('manage', $category)) {
            throw ValidationException::withMessages(['category_id' => 'The given category id does not exist.']);
        }

        return $category->id;
    }

    /**
     * @param array<string, mixed> $validatedData
     * @return array<string, mixed>|null
     */
    private function buildFrequencyConfig(array $validatedData): ?array
    {
        $frequency = Arr::get($validatedData, 'frequency');

        return match ($frequency instanceof TaskFrequency ? $frequency->value : $frequency) {
            TaskFrequency::Weekly->value => [
                'days' => array_values(Arr::get($validatedData, 'weekly_days', [])),
            ],
            TaskFrequency::Monthly->value => [
                'day' => (int) Arr::get($validatedData, 'monthly_day'),
            ],
            default => null,
        };
    }
}
