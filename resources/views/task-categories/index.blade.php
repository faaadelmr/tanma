// resources/views/task-categories/index.blade.php
{{-- @extends('layouts.app')

@section('content') --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Task Categories') }}
        </h2>
    </x-slot>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Task Categories</h2>
        <a href="{{ route('task-categories.create') }}" class="btn btn-primary">Add New Category</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Value</th>
                    <th>Details</th>
                    <th>Fields</th>
                    <th>Tasks Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->label }}</td>
                        <td>{{ $category->value }}</td>
                        <td>{{ $category->details }}</td>
                        <td>{{ implode(', ', $category->fields) }}</td>
                        <td>{{ $category->tasks_count }}</td>
                        <td>
                            <a href="{{ route('task-categories.edit', $category) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('task-categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
