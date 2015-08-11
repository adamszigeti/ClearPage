<?php

namespace System\Http
{
    use System\Config\Aliases;

    /**
     * Automatically loads the files requested by any class.
     *
     * @package System\Http
     */
    class Autoload
    {
        /**
         * All the known classes for this application.
         *
         * @var array
         */
        private static $aliases = [ ];

        /**
         * Loads the requested class.
         *
         * @param string $class
         */
        public static function load( $class )
        {
            if ( empty( self::$aliases ) )
            {
                require_once './System/Config.class.php';
                require_once './System/Config/Aliases.class.php';
                self::$aliases = Aliases::get()->entries();
            }

            // If we doesn't try to access to a framework class,
            // we need to prefix it with a 'Solutions' string, since
            // it is the location, we keep our project files in.
            $class = ( strpos( $class, 'System' ) === false ) ? 'Solutions\\'.$class : $class;

            $classParts = explode( '\\', $class );
            $className = end( $classParts );

            // If the class exists within the aliases, we will try
            // to include it once.
            if ( isset( self::$aliases[ $className ] ) )
            {
                $file = self::parseFileName( self::$aliases[ $className ] );

                // We only want to include the file, when we have it on our
                // server, because there is a chance that the user just deleted
                // the file, without changing the configuration.
                if ( file_exists( $file ) )
                {
                    require_once $file;
                }

                // If we does not have the file on our server, we should delete
                // it from our alases too, since we does not need it anymore.
                else
                {
                    Aliases::delete( [ $className => $class ] );
                }

                return;
            }

            // If the Data field doesn't contains the item, but it exists
            // in the filesystem, add it to the Data, and include it.
            else if ( file_exists( $classPath = self::parseFileName( $class ) ) )
            {
                Aliases::add( [ $className => $class ] );
                self::$aliases = Aliases::get()->entries();
                require_once $classPath;
                return;
            }

            echo '<strong>Fatal error:</strong> Cannot find class file: '.$classPath;
        }

        /**
         * Builds the file name from the given path.
         *
         * @param string $of The path to the file (namespace)
         * @return string
         */
        private static function parseFileName( $of )
        {
            return strtr( $of, [ '\\' => DIRECTORY_SEPARATOR ] ).'.class.php';
        }
    }
}