<?php

use \System\Http\Routes;

Routes::set( '/', 'DefaultController->index' );
Routes::set( 'demo/$allah', 'DefaultController->index' );
Routes::set( '$song', 'DefaultController->index' );
