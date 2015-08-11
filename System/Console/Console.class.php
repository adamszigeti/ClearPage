<?php

namespace System\Console
{
    /**
     * Class Console
     * @package System\Console
     */
    class Console
    {
        /**
         * All the arguments that were given to this
         * class (without the filename).
         * @array
         */
        protected $rawInput;

        /**
         * Initializes the fields, and call the
         * proper methods, according to the arguments.
         */
        public function __construct()
        {
            if ( !empty( $_SERVER[ 'argv' ] ) )
            {
                array_shift( $_SERVER[ 'argv' ] );
            }
            $this->rawInput = ( !empty( $_SERVER[ 'argv' ] ) ) ? $_SERVER[ 'argv' ] : [ ];

            if ( empty( $this->rawInput ) )
            {
                return $this->help();
            }
            $method = $this->rawInput[ 0 ];
            return $this->$method( $this->rawInput );
        }

        /**
         * List out all the possible commands, this
         * class is capable of executing.
         */
        public function help()
        {
            echo Output::table( [ 'title'   => 'Teszt Tablazat',
                                  'headers' => [ 'id', 'name', 'email' ],
                                  'content' => [
                                      [ '0', 'Veto Bloodlust', 'veto@example.com' ],
                                      [ '1', 'Akrone c\'Rillah', 'akrone@example.com' ],
                                      [ '2', 'Eiliana c\'Rillah', 'eiliana@example.com' ]
                                  ]
            ] );
        }

        public function test( array $args )
        {
            print_r( $args );
        }
    }
}