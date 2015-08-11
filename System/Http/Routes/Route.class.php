<?php

namespace System\Http\Routes
{
    /**
     * Class Route
     * @package System\Http\Routes
     */
    class Route
    {
        /**
         * Contains the originally passed route path.
         *
         * @var string
         */
        private $originalPath = '';

        /**
         * Contains the path, this route is answering to.
         *
         * @var array
         */
        private $path = [ ];

        /**
         * Contains the controller and the method, this route fires.
         *
         * @var string
         */
        private $calls;

        /**
         * Contains the method, this route can be accessed on.
         *
         * @var string
         */
        private $method;

        /**
         * Builds the object.
         *
         * @param string $path
         * @param string $calls
         * @param string $method
         */
        public function __construct( $path, $calls, $method )
        {
            $this->originalPath = $path;
            $this->path = self::parsePath( $path );
            $this->calls = $calls;
            $this->method = $method;
        }

        /**
         * @param $path
         * @return array
         */
        private static function parsePath( $path )
        {
            $path = array_filter( explode( '/', trim( $path, '/' ) ) );
            return empty( $path ) ? [ ] : $path;
        }

        /**
         * Returns with the parts of this route's path.
         *
         * @return array
         */
        public function path()
        {
            return $this->path;
        }

        /**
         * Returns the controller and method this route calls.
         *
         * @return string
         */
        public function calls()
        {
            return $this->calls;
        }

        /**
         * Returns the method, which this method can be fired on.
         *
         * @return string
         */
        public function method()
        {
            return $this->method;
        }

        /**
         * Returns with the original path of this route.
         *
         * @return string
         */
        public function originalPath()
        {
            return $this->originalPath;
        }
    }
}