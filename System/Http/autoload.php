<?php

include_once '/System/Http/Autoload.class.php';
spl_autoload_register( function ( $class ) { \System\Http\Autoload::load( $class ); } );