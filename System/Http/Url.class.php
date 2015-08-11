<?php

namespace System\Http
{
    /**
     * Provides access to both the browser's requested URL, and the
     * user requested POST url request.
     *
     * @package System\Http
     */
    class Url
    {
        /**
         * By the singleton pattern, contains the only instance
         * of this class.
         *
         * @var Url
         */
        private static $instance;

        /**
         * Contains the results of the requested URL parsed array.
         *
         * @var array
         */
        private $url = [ ];

        /**
         * Contains the result array of the POST URL request.
         *
         * @var array
         */
        private $post = [ ];

        /**
         * By the singleton pattern, this returns with the one and only instance
         * of this class. If there is no instance yet, creates one.
         *
         * @return Url
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
         * Builds the object.
         */
        public function __construct()
        {
            $this->url = $this->parseURL();
            $this->post = $this->parsePost();
        }

        /**
         * Returns with the parsed URL request.
         *
         * @return array
         */
        public function url()
        {
            return $this->url;
        }

        /**
         * Returns with the parsed POST URL request.
         *
         * @return array
         */
        public function post()
        {
            return $this->post;
        }

        /**
         * Creates the result array from the requested url.
         *
         * @return array
         */
        private function parseURL()
        {
            // Cut the unnecessary '/' singns from both sides of the
            // requested url, and parse the "cleaned" string into an
            // array, that we can work with.
            $url = explode( '/', trim( $_SERVER[ 'REQUEST_URI' ], '/' ) );

            // Get the current working directory's full path, then cut
            // it into an array, by the filesystem's native directory
            // separator sign.
            $loc = explode( DIRECTORY_SEPARATOR, getcwd() );

            // Computing the difference between the two arrays, and
            // recalculate the array keys, since the array_diff won't
            // do it for us automatically.
            return array_values( array_diff( $url, $loc ) );
        }

        /**
         * Creates the result array from the POST data.
         *
         * @return array
         */
        private function parsePost()
        {
            return isset( $_POST[ 'url' ] ) ? explode( '/', trim( $_POST[ 'url' ], '/' ) ) : [ ];
        }
    }
}