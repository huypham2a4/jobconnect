<?php
// Sync modified files from laragon/www/jobconnect to jobConnect_OS

$sourceBase = 'E:/LTMNM/laragon/www/jobconnect';
$destBase = 'E:/LTMNM/jobConnect_OS';

$files = [
    'README.md',
    'routes/web.php',
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/EmployerController.php',
    'app/Http/Controllers/CandidateController.php',
    'app/Http/Controllers/JobController.php',
    'app/Http/Controllers/AuthController.php',
    'public/css/theme-rebirth.css',
    'public/js/jobconnect.js',
    'resources/views/components/alert.blade.php',
    'resources/views/components/input.blade.php',
    'resources/views/home.blade.php',
    'resources/views/jobs/index.blade.php',
    'resources/views/jobs/show.blade.php',
    'resources/views/employer/dashboard.blade.php',
    'resources/views/employer/jobs/index.blade.php',
    'resources/views/candidate/dashboard.blade.php',
    'resources/views/candidate/cvs.blade.php',
    'resources/views/admin/dashboard.blade.php',
    'resources/views/layouts/app.blade.php',
];

$copied = 0;
foreach ($files as $file) {
    $src = $sourceBase . '/' . $file;
    $dst = $destBase . '/' . $file;
    
    if (file_exists($src)) {
        $dir = dirname($dst);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        copy($src, $dst);
        $copied++;
        echo "Copied: $file\n";
    } else {
        echo "Source missing: $file\n";
    }
}

echo "Total synced: $copied files.\n";
