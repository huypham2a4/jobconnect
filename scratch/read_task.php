<?php
$path = 'E:/LTMNM/jobConnect_OS/documents/task.txt';
if (file_exists($path)) {
    echo file_get_contents($path);
} else {
    echo "task.txt not found at $path\n";
    // Check other locations
    $candidates = glob('E:/LTMNM/*task*.txt');
    foreach ($candidates as $c) echo "Found: $c\n";
}
