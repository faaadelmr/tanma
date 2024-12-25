<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\TaskCategory;
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

        // Get all categories
        $categories = TaskCategory::orderBy('name')->get();
        
        return view('dashboard', compact('categories', 'performanceMetrics'));
    }

    public function getChartData($period)
    {
        $query = DailyReport::with(['tasks.category']);
        $chartData = [];
        
        // Set date range based on period
        switch ($period) {
                
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $dateFormat = 'l';
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $dateFormat = 'M-d';
                break;

            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $dateFormat = 'M';
                break;

            default:
                return response()->json(['error' => 'Invalid period'], 400);
        }

        // Initialize data structure for each category
        $categories = TaskCategory::all();
        $dates = collect();
        
        // Generate date range
        for ($date = $startDate->copy(); $date->lte($endDate); $period === 'year' ? $date->addMonth() : $date->addDay()) {
            $dates->push($date->format($dateFormat));
        }

        // Initialize chart data structure
        foreach ($categories as $category) {
            $chartData[$category->name] = [];
            foreach ($dates as $date) {
                $chartData[$category->name][] = [
                    'date' => $date,
                    'batch_count' => 0,
                    'claim_count' => 0,
                    'sheet_count' => 0,
                    'email' => 0,
                    'form' => 0
                ];
            }
        }

        // Get reports for the period
        $reports = $query->whereBetween('report_date', [$startDate, $endDate])
                        ->orderBy('report_date', 'asc')
                        ->get();

        // Fill in the actual data
        foreach ($reports as $report) {
            foreach ($report->tasks as $task) {
                $date = ($task->category->has_dor_date && $task->task_date) 
                    ? Carbon::parse($task->task_date)->format($dateFormat)
                    : $report->report_date->format($dateFormat);
                
                $dateIndex = collect($chartData[$task->category->name])->search(function($item) use($date) {
                    return $item['date'] === $date;
                });
                
                if ($dateIndex !== false) {
                    $chartData[$task->category->name][$dateIndex]['batch_count'] += $task->batch_count ?? 0;
                    $chartData[$task->category->name][$dateIndex]['claim_count'] += $task->claim_count ?? 0;
                    $chartData[$task->category->name][$dateIndex]['sheet_count'] += $task->sheet_count ?? 0;
                    $chartData[$task->category->name][$dateIndex]['email'] += $task->email ?? 0;
                    $chartData[$task->category->name][$dateIndex]['form'] += $task->form ?? 0;
                }
            }
        }

        return response()->json($chartData);
    }

    public function getCustomChartData($start, $end)
    {
        try {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $query = DailyReport::with(['tasks.category']);
        $chartData = [];
        
        // Generate date range
        $dates = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        // Initialize data structure
        foreach (TaskCategory::all() as $category) {
            $chartData[$category->name] = [];
            foreach ($dates as $date) {
                $chartData[$category->name][] = [
                    'date' => $date,
                    'batch_count' => 0,
                    'claim_count' => 0,
                    'sheet_count' => 0,
                    'email' => 0,
                    'form' => 0
                ];
            }
        }

        // Get and process reports
        $reports = $query->whereBetween('report_date', [$startDate, $endDate])
                        ->orderBy('report_date', 'asc')
                        ->get();

        foreach ($reports as $report) {
            foreach ($report->tasks as $task) {
                $date = ($task->category->has_dor_date && $task->task_date) 
                    ? Carbon::parse($task->task_date)->format('Y-m-d')
                    : $report->report_date->format('Y-m-d');
                
                $dateIndex = collect($chartData[$task->category->name])->search(function($item) use($date) {
                    return $item['date'] === $date;
                });
                
                if ($dateIndex !== false) {
                    $chartData[$task->category->name][$dateIndex]['batch_count'] += $task->batch_count ?? 0;
                    $chartData[$task->category->name][$dateIndex]['claim_count'] += $task->claim_count ?? 0;
                    $chartData[$task->category->name][$dateIndex]['sheet_count'] += $task->sheet_count ?? 0;
                    $chartData[$task->category->name][$dateIndex]['email'] += $task->email ?? 0;
                    $chartData[$task->category->name][$dateIndex]['form'] += $task->form ?? 0;
                }
            }
        }

        return response()->json($chartData);
    }
}