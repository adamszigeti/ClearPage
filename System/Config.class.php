<?php

namespace System
{
    /**
     * Defines the obligatory methods, and
     *
     * @package System\Config
     */
    abstract class Config
    {
        protected static $instances = [ ];

        /**
         * Contains the directory, containing the configuration
         * files.
         *
         * @var string
         */
        protected static $confDir = './System/Config/Data/';

        /**
         * By the singleton pattern, returns the only instance of this class.
         * If there is no instance yet, creates one.
         */
        abstract public static function get();

        /**
         * Returns with the array of configuration entities.
         *
         * @return array
         */
        abstract public function entries();

        /**
         * Updates the list (array) of configuration entities from the file, or
         * if the $items parameter is passed, updates the array from it.
         *
         * @param array $items Optional. The array of new items.
         */
        abstract protected function updateEntries( array $items = [ ] );

        /**
         * Adds a new value to any configuration file.
         *
         * @param array $newValues The associative array of new values.
         */
        public static function add( array $newValues )
        {
            if ( !empty( $newValues ) )
            {
                $newArr = array_merge( static::get()->entries(), $newValues );
                self::updateFile( $newArr );
                return;
            }

            echo '<strong>Fatal error:</strong> The parameter can\'t be empty!<br>';
            exit;
        }

        /**
         * Deletes a value from a configuration file.
         *
         * @param array $values The associative array of values to be deleted.
         */
        public static function delete( array $values )
        {
            // Check if we were given something in that parameter,
            // because if not, then we don't want to do anything.
            if ( !empty( $values ) )
            {
                $newArr = array_diff_assoc( static::get()->entries(), $values );
                self::updateFile( $newArr );
                return;
            }

            // Print out an error message
            // TODO: Change it to use a custom ERROR class!
            echo '<strong>Fatal error:</strong> The parameter can\'t be empty!';
            exit;
        }

        /**
         * Update a given configuration file.
         *
         * @param array $newArr The array of changed properties to be written in the configuration file.
         */
        private static function updateFile( array $newArr )
        {
            $className = ( new \ReflectionClass( static::get() ) )->getShortName();
            $whatPath = self::$confDir.strtolower( $className ).'.php';
            if ( file_exists( $whatPath ) )
            {
                // Sort the array by keys:
                ksort( $newArr );

                // Build the string that will be written to the file:
                $content = "<?php\r\n\r\nreturn [\r\n\r\n";
                foreach ( $newArr as $key => &$value )
                {
                    $content .= "\t'$key' => '$value',\r\n";
                }
                $content .= "\t\r\n];";

                // Write content to the given configuration file:
                file_put_contents( $whatPath, $content );

                // Update the variable:
                static::get()->updateEntries( $newArr );
                return;
            }

            var_dump( file_exists( $whatPath ) );

            echo '<br/><strong>Fatal error:</strong> File does not exist ('.$whatPath.')!';
            exit;
        }
    }
}