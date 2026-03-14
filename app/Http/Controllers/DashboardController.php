<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();
        $yesterday = $today->copy()->subDay();
        $twoDaysAgo = $today->copy()->subDays(2);

        $stats = $user->tasks()
            ->toBase()
            ->selectRaw(
                'COUNT(CASE WHEN task_date = ? THEN 1 END) as tasks_today,
                COUNT(CASE WHEN task_date = ? AND completed_at IS NOT NULL THEN 1 END) as completed_today,
                COUNT(CASE WHEN task_date < ? AND completed_at IS NULL THEN 1 END) as overdue,
                COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as total_pending',
                [$today, $today, $today]
            )
            ->first();

        $recentOverdueTasks = $user->tasks()
            ->with('category')
            ->whereNull('completed_at')
            ->whereDate('task_date', '<', $today)
            ->whereDate('task_date', '>=', $twoDaysAgo)
            ->orderBy('task_date')
            ->get();

        $todayTasks = $user->tasks()
            ->with('category')
            ->whereDate('task_date', $today)
            ->orderBy('task_date')
            ->get();

        return view('dashboard', [
            'stats' => [
                'overdue' => (int) $stats->overdue,
                'completed_today' => (int) $stats->completed_today,
                'total_pending' => (int) $stats->total_pending,
                'due_today' => (int) $stats->tasks_today,
            ],
            'recentOverdueTasks' => $recentOverdueTasks->toResourceCollection()->resolve(),
            'todayTasks' => $todayTasks->toResourceCollection()->resolve(),
            'dateLabels' => [
                'today' => $today->format('M d, Y'),
                'yesterday' => $yesterday->format('M d, Y'),
                'two_days_ago' => $twoDaysAgo->format('M d, Y'),
            ],
        ]);
    }
}
