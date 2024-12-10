<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\TaskCategory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['tasks', 'user'])
            ->orderBy('report_date', 'desc')
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        $categories = TaskCategory::all();
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_date' => 'required|date',
            'tasks' => 'required|array',
            'tasks.*.category_id' => 'required|exists:task_categories,id',
            'tasks.*.description' => 'required|string',
            'tasks.*.quantity' => 'required|numeric',
            'tasks.*.unit' => 'required|string'
        ]);

        $report = Report::create([

            'user_id' => auth()->id(),
            'report_date' => $validated['report_date'],
            'total_tasks' => count($validated['tasks'])
        ]);

        foreach ($validated['tasks'] as $task) {
            $report->tasks()->create($task);
        }

        return redirect()->route('reports.index')
            ->with('success', 'Report created successfully');
    }

    public function show(Report $report)
    {
        $report->load(['tasks', 'user']);
        return view('reports.show', compact('report'));
    }
}
