<?php

namespace Sample\Models
{
    class Song
    {
        private $wildcard;

        public function __construct( $song = '' )
        {
            $this->wildcard = $song;
        }

        /**
         *
         */
        public function __toString()
        {
            return $this->wildcard;
        }
    }
}