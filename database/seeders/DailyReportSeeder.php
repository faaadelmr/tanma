<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\ReportTask;
use App\Models\TaskCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailyReportSeeder extends Seeder
{
    public function run(): void
    {
        // Generate reports for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Create reports for each day
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $report = DailyReport::create([
                'user_id' => rand(1, 5), // Assuming you have 5 users
                'report_date' => $date->format('Y-m-d'),
            ]);

            // Get all task categories
            $categories = TaskCategory::all();

            // Randomly select 5-15 tasks for each day
            $selectedCategories = $categories->random(rand(5, 15));

            foreach ($selectedCategories as $category) {
                $startTime = null;
                $endTime = null;
                $batchCount = null;
                $claimCount = null;
                $sheetCount = null;

                // Generate appropriate random values based on task type
                if ($category->has_time_range) {
                    $startHour = rand(8, 16);
                    $startMinute = rand(0, 59);
                    $duration = rand(30, 180); // 30 minutes to 3 hours

                    $startTime = sprintf('%02d:%02d', $startHour, $startMinute);
                    $endTime = Carbon::createFromFormat('H:i', $startTime)
                        ->addMinutes($duration)
                        ->format('H:i');
                }

                if ($category->has_batch) {
                    $batchCount = rand(1, 200);
                }

                if ($category->has_claim) {
                    $claimCount = rand(1, 500);
                }

                if ($category->has_sheets) {
                    $sheetCount = rand(100, 2000);
                }

                ReportTask::create([
                    'daily_report_id' => $report->id,
                    'task_category_id' => $category->id,
                    'task_date' => $date->format('Y-m-d'),
                    'batch_count' => $batchCount,
                    'claim_count' => $claimCount,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'sheet_count' => $sheetCount
                ]);
            }
        }
    }
}
