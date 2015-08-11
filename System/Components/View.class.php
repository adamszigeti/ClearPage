<?php

namespace System\Components
{
    use System\Config\Projects;

    /**
     * Class View
     * @package System\Components
     */
    class View
    {
        /**
         * Contains the parsed DOM of the given file.
         *
         * @var array
         */
        private static $dom = [ ];

        private static $header;

        private static $content;

        private static $footer;

        /**
         *
         *
         * @param string $page The page we would like to load.
         * @param array  $vars Optional. The values, the requested page requires.
         */
        public static function prepare( $page, array $vars = [ ] )
        {
            $activeFolder = './Solutions/'.Projects::get()->active().'/Views/';

            self::loadHeader( $activeFolder, $vars );
            self::loadContent( $activeFolder, $vars );

            return self::$header;
        }

        /**
         * Loads the header file into a variable.
         *
         * @param string $activeFolder The views folder.
         * @param array  $vars         The variables that we want to pass to the view.
         */
        private static function loadHeader( $activeFolder, array &$vars )
        {
            ob_start();
            extract( $vars );
            include $activeFolder.'head.master.php';
            self::$header = strtr( ob_get_contents(), [
                'href="/' => 'href="'.$activeFolder,
                'src="/'  => 'src="'.$activeFolder
            ] );
            ob_end_clean();
        }

        private static function loadContent( $activeFolder, array &$vars )
        {
            ob_start();
            extract( $vars );
            include $activeFolder.'head.master.php';
            self::$header = strtr( ob_get_contents(), [
                'href="/' => 'href="'.$activeFolder,
                'src="/'  => 'src="'.$activeFolder
            ] );
            ob_end_clean();
        }
    }
}