<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\TaskCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Calculate performance metrics
        $performanceMetrics = [
            'total_reports' => DailyReport::count(),
            'reports_this_month' => DailyReport::whereMonth('report_date', now()->month)->count(),
            'active_users' => User::whereHas('daily_reports')->count(),
            'top_performers' => $this->getTopPerformers()
        ];

        // Group categories
        $categories = TaskCategory::all();
        $groupedCategories = $categories->groupBy(function ($category) {
            return explode(' ', $category->name)[0];
        });

        return view('dashboard', compact('performanceMetrics', 'groupedCategories'));
    }

    private function getTopPerformers()
    {
        $users = User::with(['daily_reports.tasks.category'])
            ->whereHas('daily_reports', function ($query) {
                $query->whereMonth('report_date', now()->month);
            })
            ->get();

        $performers = $users->map(function ($user) {
            $reports = $user->daily_reports;
            
            // Calculate metrics
            $totalTasks = $reports->flatMap->tasks->count();
            $completedTasks = $reports->flatMap->tasks->filter->end_time->count();
            $averageScore = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
            $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
            
            // Get most productive category
            $categoryCount = $reports->flatMap->tasks
                ->groupBy('task_category_id')
                ->map->count();
            
            $mostProductiveCategory = $categoryCount->isNotEmpty() 
                ? TaskCategory::find($categoryCount->keys()->first())->name 
                : 'N/A';

            return [
                'name' => $user->name,
                'average_score' => number_format($averageScore, 1),
                'completion_rate' => number_format($completionRate, 1),
                'most_productive_category' => $mostProductiveCategory
            ];
        })
        ->sortByDesc('average_score')
        ->take(5)
        ->values()
        ->all();

        return $performers;
    }

    public function getChartData($period)
    {
        $query = DailyReport::with(['tasks.category']);
        
        // Apply date filtering based on period
        switch ($period) {
            case 'week':
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();
                
                // Get all dates in the week
                $dates = collect();
                for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
                    $dates->push($date->format('Y-m-d'));
                }
                
                $reports = $query->whereBetween('report_date', [$startOfWeek, $endOfWeek])
                                ->orderBy('report_date', 'asc')
                                ->get();
                
                // Initialize data structure for all days
                foreach ($dates as $date) {
                    foreach (TaskCategory::all() as $category) {
                        $categoryGroup = explode(' ', $category->name)[0];
                        if (!isset($chartData[$categoryGroup])) {
                            $chartData[$categoryGroup] = [];
                        }
                        
                        // Check if data already exists for this date
                        $existingData = collect($chartData[$categoryGroup])->firstWhere('date', $date);
                        
                        if (!$existingData) {
                            $chartData[$categoryGroup][] = [
                                'date' => $date,
                                'batch_count' => 0,
                                'claim_count' => 0,
                                'sheet_count' => 0,
                                'email' => 0,
                                'form' => 0
                            ];
                        }
                    }
                }
            
                // Fill in actual data
                foreach ($reports as $report) {
                    $date = $report->report_date->format('Y-m-d');
                    foreach ($report->tasks as $task) {
                        $categoryGroup = explode(' ', $task->category->name)[0];
                        
                        $dateIndex = collect($chartData[$categoryGroup])->search(function($item) use($date) {
                            return $item['date'] === $date;
                        });
                        
                        if ($dateIndex !== false) {
                            $chartData[$categoryGroup][$dateIndex]['batch_count'] += $task->batch_count ?? 0;
                            $chartData[$categoryGroup][$dateIndex]['claim_count'] += $task->claim_count ?? 0;
                            $chartData[$categoryGroup][$dateIndex]['sheet_count'] += $task->sheet_count ?? 0;
                            $chartData[$categoryGroup][$dateIndex]['email'] += $task->email ?? 0;
                            $chartData[$categoryGroup][$dateIndex]['form'] += $task->form ?? 0;
                        }
                    }
                }
                break;            
                case 'month':
                    $startOfMonth = Carbon::now()->startOfMonth();
                    $endOfMonth = Carbon::now()->endOfMonth();
                    
                    // Get all dates in the month
                    $dates = collect();
                    for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                        $dates->push($date->format('Y-m-d'));
                    }
                    
                    $reports = $query->whereMonth('report_date', Carbon::now()->month)
                                    ->orderBy('report_date', 'asc')
                                    ->get();
                    
                    // Initialize data for all days in month
                    foreach ($dates as $date) {
                        foreach (TaskCategory::all() as $category) {
                            $categoryGroup = explode(' ', $category->name)[0];
                            if (!isset($chartData[$categoryGroup])) {
                                $chartData[$categoryGroup] = [];
                            }
                            $chartData[$categoryGroup][] = [
                                'date' => $date,
                                'batch_count' => 0,
                                'claim_count' => 0,
                                'sheet_count' => 0,
                                'email' => 0,
                                'form' => 0
                            ];
                        }
                    }
                    break;                
                    case 'year':
                        $startOfYear = Carbon::now()->startOfYear();
                        $endOfYear = Carbon::now()->endOfYear();
                        
                        // Get all months in the year
                        $months = collect();
                        for ($date = $startOfYear->copy(); $date->lte($endOfYear); $date->addMonth()) {
                            $months->push($date->format('Y-m'));
                        }
                        
                        $reports = $query->whereYear('report_date', Carbon::now()->year)
                                        ->orderBy('report_date', 'asc')
                                        ->get();
                        
                        // Initialize data structure for all months
                        foreach ($months as $month) {
                            foreach (TaskCategory::all() as $category) {
                                $categoryGroup = explode(' ', $category->name)[0];
                                if (!isset($chartData[$categoryGroup])) {
                                    $chartData[$categoryGroup] = [];
                                }
                                
                                $chartData[$categoryGroup][] = [
                                    'date' => $month,
                                    'batch_count' => 0,
                                    'claim_count' => 0,
                                    'sheet_count' => 0,
                                    'email' => 0,
                                    'form' => 0
                                ];
                            }
                        }
                    
                        // Fill in actual data
                        foreach ($reports as $report) {
                            $month = $report->report_date->format('Y-m');
                            foreach ($report->tasks as $task) {
                                $categoryGroup = explode(' ', $task->category->name)[0];
                                
                                $monthIndex = collect($chartData[$categoryGroup])->search(function($item) use($month) {
                                    return $item['date'] === $month;
                                });
                                
                                if ($monthIndex !== false) {
                                    $chartData[$categoryGroup][$monthIndex]['batch_count'] += $task->batch_count ?? 0;
                                    $chartData[$categoryGroup][$monthIndex]['claim_count'] += $task->claim_count ?? 0;
                                    $chartData[$categoryGroup][$monthIndex]['sheet_count'] += $task->sheet_count ?? 0;
                                    $chartData[$categoryGroup][$monthIndex]['email'] += $task->email ?? 0;
                                    $chartData[$categoryGroup][$monthIndex]['form'] += $task->form ?? 0;
                                }
                            }
                        }
                        break;
                    
        }

        $reports = $query->get();
        // Group data by category
        $chartData = [];
        
        foreach ($reports as $report) {
            $date = $report->report_date->format('Y-m-d');
            
            // Group and sum all tasks for each date
            foreach ($report->tasks as $task) {
                $categoryGroup = explode(' ', $task->category->name)[0];
                
                if (!isset($chartData[$categoryGroup])) {
                    $chartData[$categoryGroup] = [];
                }
                
                // Check if entry for this date already exists
                $existingIndex = collect($chartData[$categoryGroup])->search(function ($item) use ($date) {
                    return $item['date'] === $date;
                });
                
                if ($existingIndex !== false) {
                    // Add to existing totals
                    $chartData[$categoryGroup][$existingIndex]['batch_count'] += $task->batch_count ?? 0;
                    $chartData[$categoryGroup][$existingIndex]['claim_count'] += $task->claim_count ?? 0;
                    $chartData[$categoryGroup][$existingIndex]['sheet_count'] += $task->sheet_count ?? 0;
                    $chartData[$categoryGroup][$existingIndex]['email'] += $task->email ?? 0;
                    $chartData[$categoryGroup][$existingIndex]['form'] += $task->form ?? 0;
                } else {
                    // Create new entry
                    $chartData[$categoryGroup][] = [
                        'date' => $date,
                        'batch_count' => $task->batch_count ?? 0,
                        'claim_count' => $task->claim_count ?? 0,
                        'sheet_count' => $task->sheet_count ?? 0,
                        'email' => $task->email ?? 0,
                        'form' => $task->form ?? 0
                    ];
                }
            }
        }
        return response()->json($chartData);
    }
}
