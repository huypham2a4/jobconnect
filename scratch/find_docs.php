<?php
$dirs = [
    'E:/LTMNM',
    'E:/LTMNM/jobConnect_OS',
    'E:/LTMNM/laragon/www/jobconnect',
    'E:/LTMNM/Nhom9_DeTai13_v2_nosrc',
];

foreach ($dirs as $d) {
    if (is_dir($d)) {
        echo "=== Scanning $d ===\n";
        $files = glob($d . '/*.{docx,doc,txt,pdf}', GLOB_BRACE);
        foreach ($files as $f) {
            echo "  " . basename($f) . " (" . filesize($f) . " bytes)\n";
        }
        $sub = glob($d . '/*/*.{docx,doc,txt,pdf}', GLOB_BRACE);
        foreach ($sub as $f) {
            echo "  " . substr($f, strlen($d) + 1) . " (" . filesize($f) . " bytes)\n";
        }
    }
}
