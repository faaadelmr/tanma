<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('tasks')->latest()->get();
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        $taskCategories = TaskCategory::all();
        $userName=Auth::user()->name;
        return view('reports.create', compact('taskCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'tasks' => 'required|array',
            'tasks.*.category' => 'required|string|exists:task_categories,details',
            'tasks.*.batch' => 'nullable|integer',
            'tasks.*.claim' => 'nullable|integer',
            'tasks.*.email' => 'nullable|integer',
        ]);

        $report = Report::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
        ]);

        foreach ($validated['tasks'] as $taskData) {
            $report->tasks()->create($taskData);
        }

        return redirect()->route('reports.index')->with('success', 'Report created successfully');
    }

    public function show(Report $report)
    {
        $report->load('tasks');
        return view('reports.show', compact('report'));
    }


    public function edit(Report $report)
    {
        $taskCategories = TaskCategory::all();
        $report->load('tasks');
        return view('reports.edit', compact('report', 'taskCategories'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'tasks' => 'required|array',
            'tasks.*.id' => 'sometimes|exists:tasks,id', // Allow existing task IDs
            'tasks.*.category' => 'required|string|exists:task_categories,details',
            'tasks.*.batch' => 'nullable|integer',
            'tasks.*.claim' => 'nullable|integer',
            'tasks.*.email' => 'nullable|integer',
            'tasks_id' => 'nullable|interger',
        ]);

        $report->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
        ]);

        // Update and create tasks efficiently
        $existingTaskIds = [];
        foreach ($validated['tasks'] as $taskData) {
            if (isset($taskData['id'])) {
                $task = Task::find($taskData['id']);
                $task->update($taskData);
                $existingTaskIds[] = $task->id;
            } else {
                $report->tasks()->create($taskData);
            }
        }

        // Delete tasks that were removed in the edit form
        $report->tasks()->whereNotIn('id', $existingTaskIds)->delete();


        return redirect()->route('reports.index')->with('success', 'Report updated successfully');
    }


    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report deleted successfully');
    }
}
