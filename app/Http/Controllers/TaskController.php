<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Category\GetCategories;
use App\Actions\Task\CreateTask;
use App\Actions\Task\UpdateTask;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController
{
    public function __construct(
        private readonly GetCategories $getCategories,
        private readonly CreateTask $createTask,
        private readonly UpdateTask $updateTask,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $category = null;
        if ($request->category_id) {
            $category = Category::where('uuid', $request->category_id)->first();
            if (! $category || $user->cannot('manage', $category)) {
                throw ValidationException::withMessages(['category' => 'The given category id does not exist.']);
            }
        }

        $categories = $this->getCategories->execute($user->id);
        array_unshift($categories, __('All categories'));

        $tasksQuery = $user->tasks()
            ->with('category')
            ->when($request->status === 'completed', fn (Builder $query) => $query->whereNotNull('completed_at'))
            ->when($request->status === 'incomplete', fn (Builder $query) => $query->whereNull('completed_at'))
            ->when($request->filled('category_id'), fn (Builder $query) => $query->where('category_id', $category?->id))
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('task_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('task_date', '<=', $request->date_to))
            ->latest();
        $tasks = $tasksQuery->paginate();

        return view('tasks.index', [
            'tasks' => $tasks->toResourceCollection()->resolve(),
            'links' => fn () => $tasks->links(),
            'categories' => $categories,
            'filters' => $request->only(['status', 'category_id', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = $this->getCategories->execute($request->user()->id);

        return view('tasks.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $this->createTask->execute($request->validated(), $request->user());

        return to_route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Task $task)
    {
        $task->load('category');
        $task = $task->toResource()->resolve();
        $categories = $this->getCategories->execute($request->user()->id);

        return view('tasks.edit', ['task' => $task, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->updateTask->execute($request->validated(), $request->user(), $task);

        return to_route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return to_route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    /**
     * Toggle completion status for the specified task.
     */
    public function toggleCompletion(Task $task)
    {
        $isCompleting = $task->completed_at === null;

        $task->update([
            'completed_at' => $isCompleting ? now() : null,
        ]);

        //        return back()->with('success', $isCompleting ? 'Task marked as completed.' : 'Task marked as incomplete.');
        return response()->json(['completed' => $isCompleting]);
    }
}
