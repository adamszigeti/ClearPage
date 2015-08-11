<?php

namespace System\Security
{
    /**
     * Gives and validates tokens.
     *
     * @package System\Security
     */
    class Token
    {
        /**
         * By the singleton pattern, it contains the one
         * and only instance of this class.
         *
         * @var Token
         */
        private static $instance;

        /**
         * This variable stores all the tokens, which were
         * already given for various
         *
         * @var array
         */
        private $tokensGiven = [ ];

        /**
         * @return Token
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
        public function newToken()
        {

        }
    }
}