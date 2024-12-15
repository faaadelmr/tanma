<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Scan Dokumen AdMedika',
                'has_batch' => true,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Scan Dokumen Provider',
                'has_batch' => true,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Scan Dokumen Isomedik',
                'has_batch' => true,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Membuka Amplop dan COP Dokumen AdMedika',
                'has_batch' => false,
                'has_claim' => false,
                'has_time_range' => true,
                'has_sheets' => false,
            ],
            [
                'name' => 'Membuka Dokumen Provider',
                'has_batch' => false,
                'has_claim' => false,
                'has_time_range' => true,
                'has_sheets' => false,
            ],
            [
                'name' => 'Registrasi Dokumen AdMedika',
                'has_batch' => true,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Registrasi Dokumen Provider',
                'has_batch' => true,
                'has_claim' => true,
                'has_time_range' => true,
                'has_sheets' => false,
            ],
            [
                'name' => 'Registrasi Klaim Softcopy',
                'has_batch' => true,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Jumlah angka di scan',
                'has_batch' => false,
                'has_claim' => false,
                'has_time_range' => false,
                'has_sheets' => true,
            ],
            [
                'name' => 'Jumlah angka scanner',
                'has_batch' => false,
                'has_claim' => false,
                'has_time_range' => false,
                'has_sheets' => true,
            ],
            [
                'name' => 'Input Reject Reguler REL 2',
                'has_batch' => false,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Input Reject Softcopy REL 2',
                'has_batch' => false,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Follow Up Email',
                'has_batch' => false,
                'has_claim' => true,
                'has_time_range' => false,
                'has_sheets' => false,
            ],
            [
                'name' => 'Cek Notifikasi Return dan Reject',
                'has_batch' => false,
                'has_claim' => false,
                'has_time_range' => true,
                'has_sheets' => false,
            ],
        ];

        foreach ($categories as $category) {
            TaskCategory::create($category);
        }
    }
}
