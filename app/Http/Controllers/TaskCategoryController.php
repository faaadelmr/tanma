<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function index()
    {
        $categories = TaskCategory::withCount('tasks')->get(); // Eager load task count
        return view('task-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('task-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|unique:task_categories,label', // Ensure label is unique
            'details' => 'required|string',
            'fields' => 'required|array',
            'fields.*' => 'string|in:batch,claim,email',
        ]);

        $value = strtolower(str_replace(' ', '_', $validated['label']));

        TaskCategory::create([
            'value' => $value,
            'label' => $validated['label'],
            'details' => $validated['details'],
            'fields' => $validated['fields'],
        ]);

        return redirect()->route('task-categories.index')->with('success', 'Category added successfully');
    }

    public function edit(TaskCategory $taskCategory)
    {
        return view('task-categories.edit', compact('taskCategory'));
    }

    public function update(Request $request, TaskCategory $taskCategory)
    {
        $validated = $request->validate([
            'label' => 'required|string|unique:task_categories,label,' . $taskCategory->id, // Unique label, ignoring current category
            'details' => 'required|string',
            'fields' => 'required|array',
            'fields.*' => 'string|in:batch,claim,email',
        ]);

        $value = strtolower(str_replace(' ', '_', $validated['label']));

        $taskCategory->update([
            'value' => $value,
            'label' => $validated['label'],
            'details' => $validated['details'],
            'fields' => $validated['fields'],
        ]);

        return redirect()->route('task-categories.index')->with('success', 'Category updated successfully');
    }


    public function destroy(TaskCategory $taskCategory)
    {
        if ($taskCategory->tasks()->count() > 0) {
            return redirect()->route('task-categories.index')->with('error', 'Cannot delete category with associated tasks.');
        }

        $taskCategory->delete();
        return redirect()->route('task-categories.index')->with('success', 'Category deleted successfully');
    }
}
