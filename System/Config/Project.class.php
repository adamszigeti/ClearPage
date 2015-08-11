<?php

namespace System\Config
{
    use System\Config;
    use System\Config\Projects;

    class Project extends Config
    {
        /*
         * @var Project
         */
        private static $instance;

        /**
         * @var string
         */
        private $name;
        
        /**
         * @var string
         */
        private $projectInfo;

        /**
         * @var
         */
        private $defaultConnection;

        /**
         * The constructor.
         */
        public function __construct()
        {
            $this->name = Projects::get()->active();
            $this->projectInfo = './Solutions/'.$this->name.'/project.info.php';
        }

        /**
         * By the singleton pattern, returns the only instance of this class.
         * If there is no instance yet, creates one.
         */
        public static function get()
        {
            if ( empty( $instance ) )
            {
                $instance = new Project();
            }
            return $instance;
        }

        /**
         * Returns with the current project's path for it's configuration file.
         *
         * @return string
         */
        public function projectInfoPath()
        {
            return $this->projectInfo;
        }

        /**
         * Returns with the array of configuration entities.
         *
         * @return array
         */
        public function entries()
        {
            // TODO: Implement entries() method.
        }

        /**
         * Updates the list (array) of configuration entities from the file, or
         * if the $items parameter is passed, updates the array from it.
         *
         * @param array $items Optional. The array of new items.
         */
        protected function updateEntries( array $items = [ ] )
        {
            // TODO: Implement updateEntries() method.
        }
    }
}