<?php

namespace Stories\Models
{
    class Song
    {
        public $name;

        public function __construct( $song = '' )
        {
            $this->name = $song;
        }
    }
}