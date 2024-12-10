// resources/views/task-categories/edit.blade.php
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Task Category</h2>

    <form action="{{ route('task-categories.update', $taskCategory) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="label" class="form-label">Label</label>
            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $taskCategory->label) }}" required>
            @error('label')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="details" class="form-label">Details</label>
            <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" required>{{ old('details', $taskCategory->details) }}</textarea>
            @error('details')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Fields</label>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="fields[]" value="batch" id="field_batch" {{ in_array('batch', old('fields', $taskCategory->fields)) ? 'checked' : '' }}>
                <label class="form-check-label" for="field_batch">Batch</label>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="fields[]" value="claim" id="field_claim" {{ in_array('claim', old('fields', $taskCategory->fields)) ? 'checked' : '' }}>
                <label class="form-check-label" for="field_claim">Claim</label>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="fields[]" value="email" id="field_email" {{ in_array('email', old('fields', $taskCategory->fields)) ? 'checked' : '' }}>
                <label class="form-check-label" for="field_email">Email</label>
            </div>
            @error('fields')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Category</button>
    </form>
</div>
