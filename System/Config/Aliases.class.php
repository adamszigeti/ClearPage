<?php

namespace System\Config
{
    use System\Config;

    /**
     * Allows to interact with the aliases the System can handle.
     *
     * @package System\Config
     */
    class Aliases extends Config
    {
        /**
         * By the singleton pattern, it contains the only instance
         * of this class.
         *
         * @var Aliases
         */
        private static $instance;

        /**
         * The location of the configuration file, which this class
         * belongs to.
         *
         * @var string
         */
        private $filePath = '';

        /**
         * The class names, paired with the namespace/file paths they can be accessed.
         *
         * @var array
         */
        private $aliases = [ ];

        /**
         * By the singleton pattern, returns the only instance of this class.
         * If no instance yet, creates one.
         *
         * @return Aliases
         */
        public static function get()
        {
            if ( empty( self::$instance ) )
            {
                self::$instance = new self;
            }
            return self::$instance;
        }

        /**
         * Constructs the object.
         */
        public function __construct()
        {
            $this->filePath = parent::$confDir.'aliases.php';
            $this->updateEntries();
        }

        /**
         * Returns with the array of configuration entities, which are
         * belongs to the given class.
         *
         * @return array
         */
        public function entries()
        {
            return $this->aliases;
        }

        /**
         * Updates the list (array) of configuration entities from the file, or
         * if the $items parameter is passed, updates the array from it.
         *
         * @param array $items Optional. The array of new items.
         */
        protected function updateEntries( array $items = [ ] )
        {
            if ( !empty( $items ) )
            {
                $this->aliases = $items;
                return;
            }
            $this->aliases = include $this->filePath;
        }
    }
}