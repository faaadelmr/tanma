<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyReport;
use App\Models\TaskCategory;
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
}
