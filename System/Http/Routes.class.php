<?php

namespace System\Http
{
    use System\Http\Routes\Route;
    use System\Kernel;

    /**
     * Class Route
     *
     * @package System\Http
     */
    class Routes
    {
        /**
         * Contains all the saved routes to the project.
         *
         * @var Route[]
         */
        private static $routes;

        /**
         * Tells if we already loaded a controller.
         *
         * @var boolean
         */
        private static $loadedOnce;

        /**
         * Checks if the given path is matching the requested URL.
         * Also, if the user gives a name to the route, it will be saved for later use.
         *
         * @param string $path   The path, the route is answering.
         * @param string $calls  The controller (and method), which the route calls. Format: 'ControllerName->method'.
         * @param string $name   Optional. The name of the specified route, on which the route can be accessed.
         * @param string $method Optional. The specified request. Possible requests are: [url|post]. Default: url.
         */
        public static function set( $path, $calls, $name = '', $method = 'url' )
        {
            $route = new Route( $path, $calls, $method );

            // If the name is not empty, the user wants to save
            // the route to later use. In this case, we also want
            // to access it with the given name later.
            if ( !empty( $name ) )
            {
                self::$routes[ $name ] = $route;
            }

            // We only proceed from this point, when we didn't load
            // a controller yet. If we did, we have nothing to do
            // with this route anymore.
            if ( !self::$loadedOnce )
            {
                self::checkUrlMatch( $route );
            }
        }

        /**
         * Returns a path associated with the specified name.
         *
         * @param string $named The name of the route which path should be returned.
         * @return string|false
         */
        public static function get( $named )
        {
            return isset( self::$routes[ $named ] ) ? self::$routes[ $named ]->originalPath() : false;
        }

        /**
         * Compares the two path, and only proceed when requirements are met.
         *
         * @param Route $route The route curerntly being checked.
         */
        private static function checkUrlMatch( Route $route )
        {
            $path = $route->path();
            $method = $route->method();

            $url = Url::get()->$method();
            $urlLength = count( $url );
            $pathLength = count( $path );

            // We only want to proceed, if we have exactly as many parts
            // in the parsed URL, as the path has. If it is not the case,
            // then we have nothing to do with this route anymore.
            if ( $urlLength !== $pathLength )
            {
                return;
            }

            /**
             * The variable that contains all the variables to be passed
             * to the loader.
             *
             * @var array
             */
            $params = [ ];

            // Cycle through all the elements, while checking for variables,
            // the user defined in the route. If there is such a variable,
            // we add it to the parameters, which we will pass to the loader.
            for ( $i = 0; $i < $urlLength; $i++ )
            {
                // If the given parts are not matching, then we need to check
                // if the path is waiting a variable, or not. If not, then this
                // path is not the path the browser requested.
                if ( $url[ $i ] !== $path[ $i ] )
                {
                    // If the route's part is not a variable, our work is done
                    // here, since this route can't be the one we are looking for.
                    if ( $path[ $i ][ 0 ] !== '$' )
                    {
                        return;
                    }

                    // If the route's path's part is starting with a '$', then
                    // we are dealing with a variable. So we just add it to the
                    // array that contains it all.
                    $varname = substr( $path[ $i ], 1, strlen( $path[ $i ] ) - 1 );
                    $params[ $varname ] = $url[ $i ];
                }
            }

            $result = Kernel::load( '\\App\\Controllers\\'.$route->calls(), $params );

            // If we get an array as result, we assume it is meant to be a JSON
            // object, so we just parse it:
            if ( is_array( $result ) )
            {
                echo json_encode( $result );
            }

            // Any other case, we just print out the result.
            else
            {
                echo $result;
            }

            self::$loadedOnce = true;
        }

    }
}