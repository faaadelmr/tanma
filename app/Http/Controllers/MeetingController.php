<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingFile;
use App\Models\MeetingTopic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with(['topics.files'])
            ->whereBetween('meeting_date', [now(), now()->addWeeks(4)])
            ->orderBy('meeting_date')
            ->paginate(1);

        return view('meetings.index', compact('meetings'));
    }


    public function generateMeetings()
    {
        $startDate = now();
        $endOfCentury = Carbon::create(2100, 12, 31);
        $generatedCount = 0;

        while ($startDate <= $endOfCentury) {
            $nextFriday = $startDate->copy()->next(Carbon::FRIDAY);
            $meeting = Meeting::firstOrCreate(['meeting_date' => $nextFriday]);

            if ($meeting->wasRecentlyCreated) {
                $generatedCount++;
            }

            $startDate = $nextFriday;
        }

        return response()->json([
            'success' => true,
            'message' => "{$generatedCount} meetings generated until year 2100"
        ]);
    }


    public function storeTopic(Meeting $meeting, Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'files.*' => 'nullable|file|max:10240'
    ]);

    $topic = $meeting->topics()->create([
        'title' => $validated['title'],
        'description' => $request->description,
        'is_completed' => false,
        'user_id' => Auth::user()->id
    ]);

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $path = $file->store('meeting-files', 'public');
            $topic->files()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $file->getMimeType()
            ]);
        }
    }

    return back()->with('success', 'Topic added successfully');
}


public function toggleComplete(MeetingTopic $topic)
{
    $topic->update(['is_completed' => !$topic->is_completed]);

    return response()->json([
        'success' => true,
        'message' => $topic->is_completed ? 'Topic marked as completed' : 'Topic marked as pending'
    ]);
}

public function continueTopic(MeetingTopic $topic)
{
    $topic->update(['is_completed' => 2]);

    $nextMeeting = Meeting::where('meeting_date', '>', $topic->meeting->meeting_date)
        ->orderBy('meeting_date')
        ->first();

    if (!$nextMeeting) {
        // Generate next meeting if none exists
        $nextFriday = $topic->meeting->meeting_date->copy()->addWeek()->next(Carbon::FRIDAY);
        $nextMeeting = Meeting::create(['meeting_date' => $nextFriday]);
    }

    $newTopic = $topic->replicate();
    $newTopic->meeting_id = $nextMeeting->id;
    $newTopic->continued_from_id = $topic->id;
    $newTopic->is_completed = false;
    $newTopic->save();

    // Copy files to new topic if any
    foreach ($topic->files as $file) {
        $newTopic->files()->create([
            'filename' => $file->filename,
            'path' => $file->path,
            'type' => $file->type
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Topic continued to next meeting'
    ]);
}
}

