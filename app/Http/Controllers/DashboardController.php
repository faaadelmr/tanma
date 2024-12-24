<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use App\Models\User;
use App\Models\DailyReport;
use App\Models\ReportTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Calculate performance metrics
        $performanceMetrics = [
            'total_tasks' => DB::table('report_tasks')
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
            'active_users' => User::whereHas('daily_reports', function($query) {
                $query->whereMonth('report_date', Carbon::now()->month);
            })->count(),
            'completion_rate' => $this->calculateCompletionRate(),
            'top_performers' => $this->getTopPerformers(),
        ];


        // Get categories grouped by their tasks
        $categories = TaskCategory::withCount('tasks')->get();

    $groupedCategories = $categories->groupBy(function ($category) {
        return explode(' ', $category->name)[0];
    })->map(function ($group) {
        return $group->map(function ($category) {
            $category->metrics = [
                'batch_count' => $category->tasks->sum('batch_count'),
                'claim_count' => $category->tasks->sum('claim_count'),
                'sheet_count' => $category->tasks->sum('sheet_count'),
                'email' => $category->tasks->sum('email'),
                'form' => $category->tasks->sum('form')
            ];
            return $category;
        });
    });

        return view('dashboard', compact('performanceMetrics', 'groupedCategories'));
    }

    private function calculateCompletionRate()
{
    $totalReports = DB::table('daily_reports')->count();
    $approvedReports = DB::table('daily_reports')->where('is_approved', true)->count();

    return $totalReports > 0 ? round(($approvedReports / $totalReports) * 100) : 0;
}


private function calculateCategoryMetrics($category)
{
    $lastWeekTasks = $category->report_tasks()
        ->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])
        ->get();

    $previousWeekTasks = $category->report_tasks()
        ->whereBetween('created_at', [Carbon::now()->subWeeks(2), Carbon::now()->subWeek()])
        ->get();

    $trend = $previousWeekTasks->count() > 0
        ? round((($lastWeekTasks->count() - $previousWeekTasks->count()) / $previousWeekTasks->count()) * 100)
        : 0;

    return [
        'batch_count' => $lastWeekTasks->sum('batch_count'),
        'claim_count' => $lastWeekTasks->sum('claim_count'),
        'sheet_count' => $lastWeekTasks->sum('sheet_count'),
        'email_count' => $lastWeekTasks->sum('email'),
        'form_count' => $lastWeekTasks->sum('form'),
        'completion_rate' => $this->calculateCategoryCompletionRate($category),
        'trend' => $trend
    ];
}

private function calculateCategoryCompletionRate($category)
{
    $totalTasks = $category->report_tasks()->count();
    $approvedTasks = $category->report_tasks()
        ->whereHas('daily_report', function($query) {
            $query->where('is_approved', true);
        })
        ->count();

    return $totalTasks > 0 ? round(($approvedTasks / $totalTasks) * 100) : 0;
}

    private function getTopPerformers()
{
    return User::withCount(['daily_reports' => function($query) {
        $query->whereMonth('report_date', Carbon::now()->month);
    }])
    ->groupBy('users.id')  // Add this line to fix the HAVING clause issue
    ->having('daily_reports_count', '>', 0)
    ->orderBy('daily_reports_count', 'desc')
    ->limit(5)
    ->get()
    ->map(function($user) {
        return [
            'name' => $user->name,
            'total_tasks' => $this->getUserTaskCount($user),
            'completion_rate' => $this->calculateUserApprovalRate($user),
            'most_productive_category' => $this->getUserMostProductiveCategory($user)
        ];
    });
}
    private function getUserTaskCount($user)
    {
        return ReportTask::whereHas('daily_report', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    private function calculateUserApprovalRate($user)
    {
        $totalReports = $user->daily_reports()->count();
        $approvedReports = $user->daily_reports()->where('is_approved', true)->count();

        return $totalReports > 0 ? round(($approvedReports / $totalReports) * 100) : 0;
    }

    private function getUserMostProductiveCategory($user)
    {
        return ReportTask::join('daily_reports', 'report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.user_id', $user->id)
            ->select('task_category_id', DB::raw('count(*) as total'))
            ->groupBy('task_category_id')
            ->orderBy('total', 'desc')
            ->first()
            ?->taskCategory
            ?->name ?? 'N/A';
    }
}
