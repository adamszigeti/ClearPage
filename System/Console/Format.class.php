<?php

namespace System\Console
{
    use System\Console\Format\Table;

    /**
     * Contains all the plain text formatting options.
     * @package System\Console
     */
    class Format
    {
        /**
         * Converts an array to a table.
         * @param array $table
         * @return string
         */
        public static function toTable( array &$table )
        {
            return ( new Table( $table ) )->build();
        }
    }
}