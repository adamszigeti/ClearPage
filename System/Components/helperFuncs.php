<?php

use System\Components\View;

function view( $page, array $vars = [ ] )
{
    return View::prepare( $page, $vars );
}