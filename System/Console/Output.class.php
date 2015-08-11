<?php

namespace System\Console
{
    /**
     * Class Output
     * @package System\Console
     */
    abstract class Output
    {
        /**
         * Prints out a line to the Console, with stylings.
         * @param $string
         */
        public static function line( $string )
        {
            echo "\r\n\t$string\r\n";
        }

        /**
         * Prints out Data in a form of a table to the Console.
         * @param array $items
         */
        public static function table( array $items )
        {
            echo Format::toTable( $items );
        }
    }
}