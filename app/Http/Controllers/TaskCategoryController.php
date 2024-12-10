<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function index()
    {
        $categories = TaskCategory::all();
        return view('task-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('task-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_categories',
            'description' => 'nullable|string'
        ]);

        TaskCategory::create($validated);

        return redirect()->route('task-categories.index')
            ->with('success', 'Category created successfully');
    }

    public function edit(TaskCategory $taskCategory)
    {
        return view('task-categories.edit', compact('taskCategory'));
    }

    public function update(Request $request, TaskCategory $taskCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_categories,name,' . $taskCategory->id,
            'description' => 'nullable|string'
        ]);

        $taskCategory->update($validated);

        return redirect()->route('task-categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(TaskCategory $taskCategory)
    {
        $taskCategory->delete();
        return redirect()->route('task-categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
