<?php

$project = dirname(__DIR__);
$process = proc_open(
    [PHP_BINARY, $project.'/vendor/bin/phpunit', '--configuration', $project.'/phpunit.xml', '--testdox'],
    [0 => STDIN, 1 => STDOUT, 2 => STDERR],
    $pipes,
    $project,
);

exit(is_resource($process) ? proc_close($process) : 1);
