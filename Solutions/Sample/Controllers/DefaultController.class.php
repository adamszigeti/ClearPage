<?php

namespace Sample\Controllers
{
    use Sample\Models\Song;
    use System\Components\View;

    class DefaultController
    {
        public function index( Song $song )
        {
            return View::prepare( 'pages/page', [ 'msg' => (string)$song ] );
        }
    }
}