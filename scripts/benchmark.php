#!/usr/bin/env php
<?php

$args = require_once dirname ( __FILE__ ) . '/_script_core.php';

// global or site specific class path?
if ( array_key_exists ( 'site', $args ) ) {
    $outPath = Scoop\Config::get_site_class_path_by_name ( $args['site'] );
} else {
    $outPath = Scoop\Config::get_shared_class_path ();
}

$starttime = microtime( true);
$startmem = memory_get_usage();
$rows = \DB\JorPw\Test::fetch();
$fetchMemIncrease = memory_get_usage() - $startmem;
$fetchTime = microtime(true)  - $starttime;
echo "fetch: $fetchMemIncrease bytes $fetchTime seconds" . PHP_EOL;

$startLoop = microtime( true);
$startLoopMem = memory_get_usage();
foreach ( $rows as $row ) {
    $one = 1;
}
$endmem = memory_get_usage();
$loopEndTime = microtime( true);
$loopMemIncrease = $endmem - $startLoopMem;
$endLoopTime = $loopEndTime - $startLoop;

echo "loop: $loopMemIncrease bytes $endLoopTime seconds" . PHP_EOL;

$endmem = $endmem - $startmem;
$endTime =  $loopEndTime - $starttime;
echo "total: $endmem bytes $endTime seconds" . PHP_EOL;

$json = json_encode($rows);

$One = 1;
