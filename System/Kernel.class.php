<?php

namespace System
{
    use Exception;
    use ReflectionClass;
    use ReflectionMethod;
    use System\Config;
    use System\Config\Projects;
    
    /**
     * Responsible for the automatic loading of the controllers, and
     * resolving all their dependencies, defined in their constructors',
     * or methods' parameters.
     *
     * @package System
     */
    class Kernel
    {
        /**
         * @var array The parameters, that were left after resolving all the dependencies.
         */
        private static $parameters = [ ];

        /**
         * Intelligently loads an action. The action to be executed should be
         * passed in a form of: 'ClassName->methodName', $arrayOfParameters.
         *
         * @param string $action     The action, to be executed.
         * @param array  $parameters Optional. The parameters, which the program can use to build the class/method.
         * @return mixed
         */
        public static function load( $action, array $parameters = [ ] )
        {
            self::$parameters = &$parameters;
            extract( self::parseAction( $action ) );

            $obj = self::loadClass( $className );

            if ( !is_null( $methodName ) )
            {
                $obj = self::loadMethod( $obj, $methodName );
            }

            return $obj;
        }

        /**
         * Instantiates a given class, with an optional array of parameters.
         *
         * @param string $className The name of the class to be instantiated.
         * @return Object
         */
        private static function loadClass( $className )
        {
            // We replace the "App" keyword in the namespace to the active
            // solution's name.
            $className = strtr( $className, [ 'App' => Projects::get()->active() ] );
            try
            {
                $reflector = new ReflectionClass( $className );
            }
            catch ( Exception $e )
            {
                echo $e->getMessage();
                exit;
            }

            // We get the constructor.
            $constructor = $reflector->getConstructor();

            // If the constructor is null, that means we have no dependencies
            // to resolve, and we can just instantiate the class.
            if ( is_null( $constructor ) )
            {
                return $reflector->newInstance();
            }

            // If there is constructor, we need to resolve all its
            // dependencies (if any).
            $args = self::resolveParameters( $constructor->getParameters() );

            // Finally, instantiate the class.
            return $reflector->newInstanceArgs( $args );
        }

        /**
         * Intelligently loads a method.
         *
         * @param object $instance   The object, we want to perform the call on.
         * @param string $methodName The name of the method, we want to access to.
         * @return Object
         */
        private static function loadMethod( $instance, $methodName )
        {
            try
            {
                $reflector = new ReflectionMethod( $instance, $methodName );
            }
            catch ( Exception $e )
            {
                echo $e->getMessage();
                exit;
            }

            // We try to resolve the method's parameters.
            $args = self::resolveParameters( $reflector->getParameters() );

            // We are done and loaded, returning to the main flow:
            return $reflector->invokeArgs( $instance, $args );
        }

        /**
         * Extracts an action into class and method.
         *
         * @param string $action The action expression to be parsed.
         * @return array
         */
        private static function parseAction( $action )
        {
            $actionBuilder = explode( '->', $action );
            $className = $actionBuilder[ 0 ];
            $methodName = isset( $actionBuilder[ 1 ] ) ? $actionBuilder[ 1 ] : null;

            return compact( 'className', 'methodName' );
        }

        /**
         * Resolves the parameters by the dependencies.
         *
         * @param array $dependencies The parameters, the given function has.
         * @return array
         */
        private static function resolveParameters( array $dependencies )
        {
            /**
             * @var array $args The array of resolved parameters to pass on the main class.
             */
            $args = [ ];
            foreach ( $dependencies as $param )
            {
                $dependency = $param->getClass();

                // If the $dependency is null, then it expects a primitive (such
                // as string, int, etc.). If that's the case, we check if we has
                // any value for this parameter.
                if ( is_null( $dependency ) )
                {
                    $args[ ] = self::resolvePrimitive( $param );
                }

                // If the dependency is not null, then it is an object, that
                // needs to be resolved. In this case, we call the loadClass()
                // method recrusively, until all the dependencies are resolved.
                else
                {
                    $args[ ] = self::loadClass( $param->getClass()->name );
                }
            }

            return $args;
        }

        /**
         * Resolves any primitive dependency.
         *
         * @param \ReflectionParameter $dependency The actual dependency we want to resolve.
         * @return mixed
         */
        private static function resolvePrimitive( \ReflectionParameter $dependency )
        {
            // If we have a value for this primitive dependency, then we just
            // use it, and take it out from our array of parameters, we does not
            // need it anymore.
            if ( isset( self::$parameters[ $dependency->name ] ) )
            {
                $val = self::$parameters[ $dependency->name ];
                unset( self::$parameters[ $dependency->name ] );
                return $val;
            }

            // If there is a default value avaiable, then we just use that to
            // instantiate, since we doesn't have any other value for it.
            else if ( $dependency->isDefaultValueAvailable() )
            {
                return $dependency->getDefaultValue();
            }

            // If we have no value, or default value for this dependency, we just
            // throw an error.
            echo '<strong>Fatal error:</strong> Primitive dependency cannot be resolved!';
            exit;
        }
    }
}
