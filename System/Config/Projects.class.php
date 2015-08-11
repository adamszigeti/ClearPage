<?php

namespace System\Config
{
    use System\Config;

    /**
     * Class Solutions
     * @package System\Config
     */
    class Projects extends Config
    {
        /**
         * @var Projects
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
         * This array contains the paths for the Solutions, and
         * also the currently active project.
         *
         * @var array
         */
        private $projects = [ ];

        /**
         * By the singleton pattern, returns with the class' only
         * instance. If it does not exist yet, creates a new one.
         *
         * @return Projects
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
         *
         */
        public function __construct()
        {
            $this->filePath = parent::$confDir.'solutions.php';
            $this->updateEntries();
        }

        /**
         * Returns with the array of configuration entities.
         *
         * @return array
         */
        public function entries()
        {
            return $this->projects;
        }

        /**
         * Returns the currently active project's name.
         *
         * @return string
         */
        public function active()
        {
            return $this->projects[ $this->projects[ 'active' ] ];
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
                $this->projects = $items;
                return;
            }
            $this->projects = include $this->filePath;
        }
    }
}