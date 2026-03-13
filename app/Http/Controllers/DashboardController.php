<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();
        $yesterday = $today->copy()->subDay();
        $twoDaysAgo = $today->copy()->subDays(2);
        $lastSevenDaysStart = $today->copy()->subDays(6);

        $overdueCount = $user->tasks()
            ->whereNull('completed_at')
            ->whereDate('task_date', '<', $today)
            ->count();

        $completedTodayCount = $user->tasks()
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', $today)
            ->count();

        $completedLastSevenDaysCount = $user->tasks()
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', '>=', $lastSevenDaysStart)
            ->count();

        $tasksDueTodayCount = $user->tasks()
            ->whereDate('task_date', $today)
            ->count();

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
                'overdue' => $overdueCount,
                'completed_today' => $completedTodayCount,
                'completed_last_7_days' => $completedLastSevenDaysCount,
                'due_today' => $tasksDueTodayCount,
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
