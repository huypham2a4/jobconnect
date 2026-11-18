<?php

namespace Database\Seeders;

use App\Models\Cv;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoCvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['sample_cv_nguyen_van_an.pdf', 'sample_cv_bich_ngoc.pdf'] as $filename) {
            $path = 'cvs/'.$filename;
            if (Cv::where('file_path', $path)->exists() && Storage::disk('public')->missing($path)) {
                Storage::disk('public')->put($path, file_get_contents(database_path('seeders/fixtures/'.$filename)));
            }
        }
    }
}
