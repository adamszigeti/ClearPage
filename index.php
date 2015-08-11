<?php

$start = microtime( true );

header( 'Content-Type: text/html; charset=utf-8' );
include_once './System/Http/autoload.php';

use \System\Config\Projects;

include_once './Solutions/'.Projects::get()->active().'/routes.php';

$end = microtime( true );
?>
