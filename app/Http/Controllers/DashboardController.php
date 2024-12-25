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
        ];

        // Group categories
        $categories = TaskCategory::all();
        $groupedCategories = $categories->groupBy(function ($category) {
            return explode(' ', $category->name)[0];
        });

        return view('dashboard', compact('categories','performanceMetrics', 'groupedCategories'));
    }

    
    public function getChartData($period)
{
    $query = DailyReport::with(['tasks.category']);
    
    switch ($period) {
        case 'day':
            $reports = $query->whereDate('report_date', Carbon::today())
                           ->orderBy('report_date', 'asc')
                           ->get();
            break;

        case 'week':
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            
            $dates = collect();
            for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
                $dates->push($date->format('Y-m-d'));
            }
            
            $reports = $query->whereBetween('report_date', [$startOfWeek, $endOfWeek])
                           ->orderBy('report_date', 'asc')
                           ->get();
            
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

        case 'month':
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            $dates = collect();
            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $dates->push($date->format('Y-m-d'));
            }
            
            $reports = $query->whereMonth('report_date', Carbon::now()->month)
                           ->orderBy('report_date', 'asc')
                           ->get();
            
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
            
            $months = collect();
            for ($date = $startOfYear->copy(); $date->lte($endOfYear); $date->addMonth()) {
                $months->push($date->format('Y-m'));
            }
            
            $reports = $query->whereYear('report_date', Carbon::now()->year)
                           ->orderBy('report_date', 'asc')
                           ->get();
            
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
            break;
    }

    // Fill in actual data using DOR date when available
    foreach ($reports as $report) {
        foreach ($report->tasks as $task) {
            $categoryGroup = explode(' ', $task->category->name)[0];
            
            // Use DOR date if category has DOR and task_date exists
            $date = ($task->category->has_dor_date && $task->task_date) 
                ? Carbon::parse($task->task_date)->format($period === 'year' ? 'Y-m' : 'Y-m-d')
                : $report->report_date->format($period === 'year' ? 'Y-m' : 'Y-m-d');
            
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

    return response()->json($chartData);
}


    public function getCustomChartData($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        
        $query = DailyReport::with(['tasks.category']);
        
        // Get all dates in range
        $dates = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }
        
        $reports = $query->whereBetween('report_date', [$startDate, $endDate])
                        ->orderBy('report_date', 'asc')
                        ->get();
        
        $chartData = [];
        
        // Initialize data structure for all dates
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
        
        return response()->json($chartData);
    }

    public function processChartData($data, $selectedCategory) 
{
    $filteredData = [];
    
    foreach ($data as $category => $categoryData) {
        if ($selectedCategory === 'all' || $category === $selectedCategory) {
            $filteredData[$category] = $categoryData;
        }
    }
    
    return $filteredData;
}


private function createDatasets($labels, $filteredData, $totals) 
{
    return [
        [
            'label' => "Batch (Total: {$totals['batch']})",
            'borderColor' => '#9333EA',
            'backgroundColor' => '#9333EA20',
            'data' => $this->calculateDataPoints($labels, $filteredData, 'batch_count'),
            'tension' => 0.4,
            'fill' => true
        ],
        // Similar entries for claim, sheet, email, form
    ];
}

private function calculateDataPoints($labels, $filteredData, $field) 
{
    return array_map(function($date) use ($filteredData, $field) {
        return array_sum(
            array_column(
                array_filter($filteredData, fn($item) => $item['date'] === $date),
                $field
            )
        );
    }, $labels);
}




}

