<?php

namespace System\Console\Format
{
    /**
     * Class Table
     * @package System\Console\Format
     */
    class Table
    {
        /**
         * The raw input array of items. Contains the headers, and
         * the contents of this table. Its values to be displayed.
         * @var array
         */
        private $items;

        /**
         * Contains the length of the longest value, associated
         * with a column number; in a form like: colNumber => maxLength.
         * @var array
         */
        private $longestValues;

        /**
         * Contains the number of columns, the table should display.
         * The header must contain all the columns to be displayed.
         * @var int
         */
        private $numberOfHeaders;

        /**
         * Contains the constructing/builded output.
         * @var string
         */
        private $builder;

        /**
         * Creates the instance.
         * @param array $table
         */
        public function __construct( array $table )
        {
            if ( !isset( $table[ 'headers' ] ) || !isset( $table[ 'content' ] ) )
            {
                echo 'Error: The array does not contain a header or a content!';
                exit;
            }

            $this->items = $table;
            $this->numberOfHeaders = count( $table[ 'headers' ] );
            $this->longestValues = $this->getLongestsByCol();
            $this->builder = '';
        }

        /**
         * Builds the table, and returns with it.
         * @return string
         */
        public function build()
        {
            $builder = "\r\n";
            $length = count( $this->items[ 'content' ] );
            for ( $i = 0; $i < $length; $i++ )
            {
                if ( $i === 0 )
                {
                    $builder .= $this->buildHeader( $this->items[ 'headers' ] );
                }
                $builder .= $this->buildRow( $this->items[ 'content' ][ $i ] );
            }
            return $builder.$this->buildRowBorder();
        }

        /**
         * Returns the length of the longest value by columns.
         * @return array
         */
        private function getLongestsByCol()
        {
            $longestsByCol = [ ];
            $numberOfContents = count( $this->items[ 'content' ] );
            for ( $j = 0; $j < $this->numberOfHeaders; $j++ )
            {
                $longestsByCol[ $j ] = strlen( $this->items[ 'headers' ][ 0 ] );

                for ( $k = 0; $k < $numberOfContents; $k++ )
                {
                    $valueLength = strlen( $this->items[ 'content' ][ $k ][ $j ] );
                    if ( !isset( $longestsByCol[ $j ] ) || $valueLength > $longestsByCol[ $j ] )
                    {
                        $longestsByCol[ $j ] = $valueLength;
                    }
                }
            }
            return $longestsByCol;
        }

        /**
         * Builds a horizontal border for the row.
         * @param bool $insideTable
         * @return string
         */
        private function buildRowBorder( $insideTable = false )
        {
            $lineWidth = array_sum( $this->longestValues ) + ( $this->numberOfHeaders * 2 + 2 );
            if ( $insideTable )
            {
                return "\t+".str_repeat( '-', $lineWidth )."+\n";
            }

            $fields = [ ];
            foreach ( $this->longestValues as &$value )
            {
                $fields[ ] = str_repeat( '-', $value );
            }
            return self::buildRow( $fields, '+' );
        }

        /**
         * Builds the header for the table.
         * @param array $headers
         * @return string
         */
        private function buildHeader( array &$headers )
        {
            $builder = '';
            $topBorder = $this->buildRowBorder( true );
            $border = $this->buildRowBorder();

            $builder .= $topBorder;

            // If the raw array contains a 'title' key, then we display
            // a titlebar at the top, with centered text.
            if ( isset( $this->items[ 'title' ] ) )
            {
                $floatFullLeft = (int)floor( ( strlen( $topBorder ) - 2 ) / 2 );
                $floatFullRight = (int)ceil( ( strlen( $topBorder ) - 2 ) / 2 );

                $floatBefore = $floatFullLeft - ( strlen( $this->items[ 'title' ] ) / 2 ) - 1;
                $floatAfter = $floatFullRight - ( strlen( $this->items[ 'title' ] ) / 2 ) - 1;

                $before = str_repeat( ' ', (int)floor( $floatBefore ) );
                $after = str_repeat( ' ', (int)ceil( $floatAfter ) );

                $builder .= "\t|".$before.$this->items[ 'title' ].$after."|\n";
                $builder .= $topBorder;
            }

            $builder .= self::buildRow( $headers );
            $builder .= $border;

            unset( $border );
            return $builder;
        }

        /**
         * Builds a content row for the table.
         * @param array  $row
         * @param string $separator
         * @return string
         */
        private function buildRow( array &$row, $separator = '|' )
        {
            $spacing = ( $separator === '+' ) ? '-' : ' ';
            $builder = "\t".$separator;
            $numberOfColumns = count( $row );
            for ( $i = 0; $i < $numberOfColumns; $i++ )
            {
                $builder .= $spacing.$row[ $i ].str_repeat( $spacing, $this->longestValues[ $i ] - strlen( $row[ $i ] ) );
                $builder .= $spacing.$separator;
            }
            return $builder."\n";
        }
    }
}