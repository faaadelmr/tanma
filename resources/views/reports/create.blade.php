// resources/views/reports/create.blade.php
{{-- @extends('layouts.app')

@section('content') --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Report Baru') }}
        </h2>
    </x-slot>

<div class="container">
    <h2>Create New Report</h2>

    <form action="{{ route('reports.store') }}" method="POST" id="reportForm">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Report Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Report Date</label>
            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date') }}" required>
            @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div id="tasksContainer">
            <h4>Tasks</h4>
            <div class="task-entry mb-3">
                <select name="tasks[0][category]" class="form-select category-select" required>
                    <option value="">Select Category</option>
                    @foreach($taskCategories as $category)
                        <option value="{{ $category->details }}" data-fields="{{ json_encode($category->fields) }}">
                            {{ $category->details }}
                        </option>
                    @endforeach
                </select>
                <div class="fields-container mt-2"></div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3" onclick="addTaskEntry()">Add Another Task</button>
        <button type="submit" class="btn btn-primary">Create Report</button>
    </form>
</div>

<script>
let taskCount = 1;

function addTaskEntry() {
    const container = document.createElement('div');
    container.className = 'task-entry mb-3';
    container.innerHTML = `
        <hr>
        <select name="tasks[${taskCount}][category]" class="form-select category-select" required>
            <option value="">Select Category</option>
            @foreach($taskCategories as $category)
                <option value="{{ $category->details }}" data-fields="{{ json_encode($category->fields) }}">
                    {{ $category->details }}
                </option>
            @endforeach
        </select>
        <div class="fields-container mt-2"></div>
    `;
    document.getElementById('tasksContainer').appendChild(container);
    taskCount++;
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('category-select')) {
        const fieldsContainer = e.target.nextElementSibling;
        const selectedOption = e.target.options[e.target.selectedIndex];
        const fields = JSON.parse(selectedOption.dataset.fields);

        fieldsContainer.innerHTML = fields.map(field => `
            <div class="mb-2">
                <label class="form-label">${field.charAt(0).toUpperCase() + field.slice(1)}</label>
                <input type="number" name="tasks[${e.target.name.match(/\d+/)[0]}][${field}]" class="form-control">
            </div>
        `).join('');
    }
});
</script>
</x-app-layout>
