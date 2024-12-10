<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'value' => 'scan',
                'label' => 'Scan Document',
                'fields' => ['batch', 'claim'],
                'details' => 'Scanning Document'
            ],
            [
                'value' => 'input_header',
                'label' => 'Input Header',
                'fields' => ['claim'],
                'details' => 'Input Header Document'
            ],
            // Add other categories...
        ];

        foreach ($categories as $category) {
            TaskCategory::create($category);
        }
    }
}
