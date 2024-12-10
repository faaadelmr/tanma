// resources/views/reports/index.blade.php
{{-- @extends('layouts.app')

@section('content') --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Tugas Harian') }}
        </h2>
    </x-slot>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Reports List</h2>
        <a href="{{ route('reports.create') }}" class="btn btn-primary">Create New Report</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Tasks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{{ $report->name }}</td>
                        <td>{{ $report->date }}</td>
                        <td>
                            @foreach($report->tasks as $task)
                                <div>
                                    {{ $task->category }}
                                    @if($task->batch) Batch: {{ $task->batch }}@endif
                                    @if($task->claim) Claim: {{ $task->claim }}@endif
                                    @if($task->email) Email: {{ $task->email }}@endif
                                </div>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('reports.show', $report) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('reports.edit', $report) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline">
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
