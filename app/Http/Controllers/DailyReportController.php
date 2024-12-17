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
        $selectedDate = Carbon::parse($request->get('date', now()));

        $categories = TaskCategory::all();
        $comparisons = [];

        foreach ($categories as $category) {
            // Get current day total
            $currentTotal = DailyReport::whereDate('report_date', $selectedDate)
                ->with(['tasks' => function($query) use ($category) {
                    $query->where('task_category_id', $category->id);
                }])
                ->get()
                ->flatMap->tasks
                ->sum(function ($task) {
                    return ($task->claim_count ?? 0) +
                       ($task->sheet_count ?? 0) +
                       ($task->email ?? 0) +
                       ($task->form ?? 0);
                });

            // Get previous periods totals
            $previousDay = $this->getPeriodTotal($category, $selectedDate->copy()->subDay());
            $previousWeek = $this->getPeriodTotal($category, $selectedDate->copy()->subWeek());
            $previousMonth = $this->getPeriodTotal($category, $selectedDate->copy()->subMonth());

            // Calculate percentage changes
            $comparisons[$category->name] = [
                'current_total' => $currentTotal,
                'day_change' => $this->calculatePercentageChange($currentTotal, $previousDay),
                'week_change' => $this->calculatePercentageChange($currentTotal, $previousWeek),
                'month_change' => $this->calculatePercentageChange($currentTotal, $previousMonth)
            ];
        }

        $chartData = [];
        foreach($comparisons as $index => $data) {
            $chartData[$index] = $this->getInitialChartData($index, 'week');
        }

        return view('dashboard', compact('comparisons', 'selectedDate', 'chartData'));
    }

    private function getInitialChartData($categoryId, $range)
    {
        $endDate = now();
        $startDate = match($range) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'three_months' => now()->subMonths(3),
            'six_months' => now()->subMonths(6),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        $data = DailyReport::with(['tasks' => function($query) use ($categoryId) {
            $query->where('task_category_id', $categoryId);
        }])
        ->whereBetween('report_date', [$startDate, $endDate])
        ->orderBy('report_date')
        ->get();

        $chartData = [
            'labels' => $data->pluck('report_date')->map->format('Y-m-d'),
            'datasets' => [
                [
                    'label' => 'Total Tasks',
                    'data' => $data->map(function($report) {
                        return $report->tasks->sum(function($task) {
                            return ($task->claim_count ?? 0) +
                               ($task->sheet_count ?? 0) +
                               ($task->email ?? 0) +
                               ($task->form ?? 0);
                        });
                    }),
                ],
            ],
        ];

        return $chartData;
    }
private function getPeriodTotal($category, $date)
{
    return DailyReport::whereDate('report_date', $date)
        ->with(['tasks' => function($query) use ($category) {
            $query->where('task_category_id', $category->id);
        }])
        ->get()
        ->flatMap->tasks
        ->sum(function ($task) {
            return ($task->claim_count ?? 0) +
                   ($task->sheet_count ?? 0) +
                   ($task->email ?? 0) +
                   ($task->form ?? 0);
        });
}

private function calculatePercentageChange($current, $previous)
{
    if ($previous == 0) return $current > 0 ? 100 : 0;
    return round((($current - $previous) / $previous) * 100, 2);
}

public function getChartData($categoryId, $range)
{
    $endDate = now();
    $startDate = match($range) {
        'week' => now()->subWeek(),
        'month' => now()->subMonth(),
        'three_months' => now()->subMonths(3),
        'six_months' => now()->subMonths(6),
        'year' => now()->subYear(),
        default => now()->subWeek(),
    };

    $data = DailyReport::with(['tasks' => function($query) use ($categoryId) {
        $query->where('task_category_id', $categoryId);
    }])
    ->whereBetween('report_date', [$startDate, $endDate])
    ->orderBy('report_date')
    ->get();

    $chartData = [
        'labels' => $data->pluck('report_date')->map->format('Y-m-d'),
        'datasets' => [
            [
                'label' => 'Total Tasks',
                'data' => $data->map(function($report) {
                    return $report->tasks->sum(function($task) {
                        return ($task->claim_count ?? 0) +
                               ($task->sheet_count ?? 0) +
                               ($task->email ?? 0) +
                               ($task->form ?? 0);
                    });
                }),
                'borderColor' => 'rgb(59, 130, 246)',
                'tension' => 0.1
            ]
        ]
    ];

    return response()->json($chartData);
}


}
