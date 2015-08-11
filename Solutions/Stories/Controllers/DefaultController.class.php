<?php

namespace Stories\Controllers
{

    use Stories\Models\Song;
    use Stories\Models\Users;
    use System\Components\View;

    class DefaultController
    {
        public function index( Song $song )
        {
            $user = Users::find( [ 'name' => 'Angra' ] );
            echo $user->email;

            echo '<br><br>';
            echo $song->name;

            // return View::prepare( 'pages/page', [ 'title' => 'Stories' ] );
        }
    }
}