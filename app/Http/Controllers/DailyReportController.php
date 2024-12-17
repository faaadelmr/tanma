<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyReport;
use App\Models\TaskCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class DailyReportController extends Controller
{
    public function index()
    {
        $reports = DailyReport::with(['user', 'tasks.category'])
            ->latest('report_date')
            ->paginate(10);

        return view('daily-reports.index', compact('reports'));
    }

    public function create()
    {
        $categories = TaskCategory::all();
        return view('daily-reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'report_date' => 'required|date',
            'tasks' => 'required|array',
            'tasks.*.category_id' => 'required|exists:task_categories,id',
            'tasks.*.date' => 'nullable|date',
            'tasks.*.batch_count' => 'nullable|integer',
            'tasks.*.claim_count' => 'nullable|integer',
            'tasks.*.start_time' => 'nullable|date_format:H:i',
            'tasks.*.end_time' => 'nullable|date_format:H:i',
            'tasks.*.sheet_count' => 'nullable|integer',
            'tasks.*.email' => 'nullable|integer',
            'tasks.*.form' => 'nullable|integer',
        ]);

        $report = DailyReport::create([
            'user_id' => Auth::id(),
            'report_date' => $validatedData['report_date']
        ]);

        $report->tasks()->createMany(
            collect($validatedData['tasks'])->map(function ($task) {
                return [
                    'task_category_id' => $task['category_id'],
                    'task_date' => $task['date'] ?? null,
                    'batch_count' => $task['batch_count'] ?? null,
                    'claim_count' => $task['claim_count'] ?? null,
                    'start_time' => $task['start_time'] ?? null,
                    'end_time' => $task['end_time'] ?? null,
                    'sheet_count' => $task['sheet_count'] ?? null,
                    'email' => $task['email'] ?? null,
                    'form' => $task['form'] ?? null,
                ];
            })
        );

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report created successfully');
    }

    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load(['user', 'tasks.category']);
        return view('daily-reports.show', compact('dailyReport'));
    }

    public function edit(DailyReport $dailyReport)
    {
        $categories = TaskCategory::all();
        $dailyReport->load(['tasks.category']);
        return view('daily-reports.edit', compact('dailyReport', 'categories'));
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        $validatedData = $request->validate([
            'report_date' => 'required|date',
            'tasks' => 'required|array',
            'tasks.*.category_id' => 'required|exists:task_categories,id',
            'tasks.*.date' => 'nullable|date',
            'tasks.*.batch_count' => 'nullable|integer',
            'tasks.*.claim_count' => 'nullable|integer',
            'tasks.*.start_time' => 'nullable|date_format:H:i',
            'tasks.*.end_time' => 'nullable|date_format:H:i',
            'tasks.*.sheet_count' => 'nullable|integer',
            'email' => 'nullable|integer',
            'form' => 'nullable|integer '
        ]);

        $dailyReport->update([
            'report_date' => $validatedData['report_date']
        ]);

        // Delete existing tasks
        $dailyReport->tasks()->delete();

        // Create new tasks
        $dailyReport->tasks()->createMany(
            collect($validatedData['tasks'])->map(function ($task) {
                return [
                    'task_category_id' => $task['category_id'],
                    'task_date' => $task['date'] ?? null,
                    'batch_count' => $task['batch_count'] ?? null,
                    'claim_count' => $task['claim_count'] ?? null,
                    'start_time' => $task['start_time'] ?? null,
                    'end_time' => $task['end_time'] ?? null,
                    'sheet_count' => $task['sheet_count'] ?? null,
                    'email' => $task['email'] ?? null,
                    'form' => $task['form'] ?? null,
                ];
            })
        );

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report updated successfully');
    }

    public function destroy(DailyReport $dailyReport)
    {
        $dailyReport->tasks()->delete();
        $dailyReport->delete();

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report deleted successfully');
    }

    public function dashboard(Request $request)
{
    $dateRange = $request->get('date_range', 'week');
    $selectedCategory = $request->get('category');

    $endDate = now();
    $startDate = match($dateRange) {
        'week' => now()->subWeek(),
        'month' => now()->subMonth(),
        'three_months' => now()->subMonths(3),
        'six_months' => now()->subMonths(6),
        'year' => now()->subYear(),
        default => now()->subWeek(),
    };

    $currentPeriodData = DailyReport::with(['tasks.category'])
        ->whereBetween('report_date', [$startDate, $endDate])
        ->get();

    $previousStartDate = (clone $startDate)->subDays($startDate->diffInDays($endDate));
    $previousPeriodData = DailyReport::with(['tasks.category'])
        ->whereBetween('report_date', [$previousStartDate, $startDate])
        ->get();

    $metrics = $this->calculateMetrics($currentPeriodData, $previousPeriodData);
    $chartData = $this->prepareChartData($currentPeriodData);

    if ($selectedCategory) {
        $categoryData = $this->prepareCategoryData($currentPeriodData, $selectedCategory);
        return response()->json($categoryData);
    }

    return view('dashboard', compact('metrics', 'chartData', 'dateRange'));
}

private function calculateMetrics($currentData, $previousData)
{
    $metrics = [];
    $categories = TaskCategory::all();

    foreach ($categories as $category) {
        // Calculate current period totals
        $currentTotal = $currentData->flatMap->tasks
            ->where('task_category_id', $category->id)
            ->sum(function ($task) {
                return collect([
                    $task->claim_count,
                    $task->sheet_count,
                    $task->email,
                    $task->form
                ])->sum();
            });

        // Calculate previous period totals
        $previousTotal = $previousData->flatMap->tasks
            ->where('task_category_id', $category->id)
            ->sum(function ($task) {
                return collect([
                    $task->claim_count,
                    $task->sheet_count,
                    $task->email,
                    $task->form
                ])->sum();
            });

        // Enhanced percentage change calculation
        if ($previousTotal > 0) {
            $percentageChange = (($currentTotal - $previousTotal) / $previousTotal) * 100;
        } elseif ($previousTotal === 0 && $currentTotal > 0) {
            $percentageChange = (($currentTotal - $previousTotal) / 1) * 100;
        } elseif ($previousTotal === 0 && $currentTotal === 0) {
            $percentageChange = 0;
        } elseif ($previousTotal > 0 && $currentTotal === 0) {
            $percentageChange = -100;
        } else {
            $percentageChange = 0;
        }        // Round to 1 decimal place for more precision
        $roundedPercentage = round($percentageChange, 1);

        $metrics[$category->name] = [
            'current_total' => $currentTotal,
            'previous_total' => $previousTotal,
            'percentage_change' => $roundedPercentage,
            'trend' => $currentTotal >= $previousTotal ? 'up' : 'down',
            'category_id' => $category->id
        ];
    }

    return $metrics;
}


private function prepareChartData($data)
{
    $categories = TaskCategory::all();
    $dates = $data->pluck('report_date')->sort()->unique();

    $datasets = [];
    foreach ($categories as $category) {
        $categoryData = [];
        foreach ($dates as $date) {
            $total = $data->where('report_date', $date)
                ->flatMap->tasks
                ->where('task_category_id', $category->id)
                ->sum(function ($task) {
                    return collect([

                        $task->claim_count,
                        $task->sheet_count,
                        $task->email,
                        $task->form
                    ])->sum();
                });
            $categoryData[] = $total;
        }

        $datasets[] = [
            'label' => $category->name,
            'data' => $categoryData
        ];
    }

    return [
        'labels' => $dates->map->format('Y-m-d')->values()->toArray(),
        'datasets' => $datasets
    ];
}

private function prepareCategoryData($data, $categoryId)
{
    $category = TaskCategory::find($categoryId);
    $dates = $data->pluck('report_date')->sort()->unique();

    $categoryData = [];
    foreach ($dates as $date) {
        $total = $data->where('report_date', $date)
            ->flatMap->tasks
            ->where('task_category_id', $categoryId)
            ->sum(function ($task) {
                return collect([

                    $task->claim_count,
                    $task->sheet_count,
                    $task->email,
                    $task->form
                ])->sum();
            });
        $categoryData[] = $total;
    }

    return [
        'labels' => $dates->map->format('Y-m-d')->values()->toArray(),
        'datasets' => [[
            'label' => $category->name,
            'data' => $categoryData
        ]]
    ];
}
}
