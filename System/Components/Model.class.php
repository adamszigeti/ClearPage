<?php

namespace System\Components {

    use System\Data\Connection;
    use System\Data\Connections\Mysql;

    /**
     * Parent class of all models to be used in the
     * application.
     *
     * @package System\Components
     */
    class Model
    {
        /**
         * The mysql connection, we executing the queries on.
         *
         * @var Mysql
         */
        protected static $db;

        /**
         * The name of the database we want to manipulate.
         *
         * @var string
         */
        protected $db_table = '';

        /**
         * The primary key for the table of the database.
         *
         * @var string
         */
        protected $db_primaryKey = 'id';

        /**
         * The array of database field names, you allow to be refreshed
         * with a new value.
         *
         * @var string[]
         */
        protected $unlocked = [ ];

        /**
         * The array of database field names, you want to seal away from
         * outside manipulation - alas you don't want to be overwritten with
         * any other value.
         *
         * @var string[]
         */
        protected $locked = [ ];

        /**
         * Checks if the $db variable is empty, and if it is, instantiates a
         * new Mysql class.
         */
        private static function checkDB()
        {
            if ( empty( self::$db ) )
            {
                self::$db = new Mysql( new Connection() );
            }
        }

        /**
         * Parse the given Select method return value into an object.
         *
         * @param string $className The name of the class we want to instantiate.
         * @param array  $rows      The raw select return value.
         * @return array|bool
         */
        private static function parseToObject( $className, &$rows )
        {
            // Initializing the return value:
            $result = [ ];

            // Saving the length of the $rows array, so the for() cycle
            // will be faster:
            $length = count( $rows );
            for ( $i = 0; $i < $length; $i++ )
            {
                // Creating a new instance of the called class, and add
                // it to the array, we will return with:
                $result[ $i ] = new $className();

                // Iterating through all the elements of the given database
                // records, and set the appropiate property for each given field.
                foreach ( $rows[ $i ] as $key => $value )
                {
                    $result[ $i ]->$key = $value;
                }
            }

            return empty( $result ) ? false : $result;
        }

        /**
         * @param array $fields The array of the given terms, you want to find the
         *                      record by.
         *
         * @return object|bool
         */
        public static function find( array $fields )
        {
            // We check the DB connection:
            self::checkDB();

            /**
             * Stores the name of the called class, so we later will know, what
             * class should we instantiate.
             *
             * @var string $calledClass
             */
            $calledClass = get_called_class();

            $query = 'SELECT * FROM '.end( explode( '\\', $calledClass ) ).' WHERE ';
            foreach ( $fields as $field => $value )
            {
                $query .= $field.' = :'.$field;
            }

            $rows = self::$db->select( $query, $fields );

            $result = self::parseToObject( $calledClass, $rows );
            return ( count( $result ) > 1 ) ? $result : $result[ 0 ];
        }

        /**
         * Gets all the records of the database, and parse them into an array
         * of php objects.
         *
         * @return array
         */
        public static function getAll()
        {
            // We check the DB connection:
            self::checkDB();

            /**
             * Stores the name of the called class, so we later will know, what
             * class should we instantiate.
             *
             * @var string $calledClass
             */
            $calledClass = get_called_class();

            // Retrieving all the records in the given database table:
            $rows = self::$db->select( 'SELECT * FROM '.end( explode( '\\', $calledClass ) ) );

            return self::parseToObject( $calledClass, $rows );
        }

        /**
         * Gets a range of records, starting with the given index; and parse
         * them into php objects.
         *
         * @param int $startingFrom    The index of the offset of the query (this value is excluded!).
         * @param int $numberOfRecords The number of records you want to get.
         *
         * @return array
         */
        public static function getRange( $startingFrom, $numberOfRecords )
        {
            // We cjheck the DB connection:
            self::checkDB();

            /**
             * Stores the name of the called class, so we later will know, what
             * class should we instantiate.
             *
             * @var string $calledClass
             */
            $calledClass = get_called_class();

            $rows = self::$db->select( 'SELECT * FROM '.end( explode( '\\', $calledClass ) ).' ORDER BY id LIMIT '.$numberOfRecords.' OFFSET '.$startingFrom );

            return self::parseToObject( $calledClass, $rows );
        }

        /**
         * Saves the object into the database.
         *
         * @return bool
         */
        public function save()
        {
            // We check the database connection:
            self::checkDB();
            /**
             * We need all the properties of the given model, to update the database
             * with the new values. So, we compute and store the difference between
             * the child object's properties, and the parent's (this class') properties.
             *
             * @var array $properties
             */
            $properties = array_diff_key( get_object_vars( $this ), get_class_vars( __CLASS__ ) );
            
            // If there is an "id" field, it means that this class was instantiated by
            // some sort of a query. So, there has to be a record in the DB, and we want
            // to update that. So it will be an update query.
            if ( isset( $this->id ) )
            {
                $query = $this->forgeUpdateQuery( $properties );
            }

            // If no "id" field was found, then it means we doesn't have any record about
            // this instance we could update. So, it will be an insert query.
            else
            {
                $query = $this->forgeInsertQuery( $properties );
            }

            return self::$db->query( $query[ 0 ], $query[ 1 ] );
        }

        /**
         * Update a database record, based on the given properties.
         *
         * @param array $properties The array of filtered properties, containing the values.
         * @return array
         */
        private function forgeUpdateQuery( &$properties )
        {
            $query = 'UPDATE '.$this->dbTable.' SET ';

            /**
             * The array of the new values to be escaped.
             * @var array $valuesArr
             */
            $valuesArr = [ ];

            // Iterate through the array of the right properties, and decide whether
            // it should, or should not be inserted; then we forge the query.
            foreach ( $properties as $name => &$value )
            {
                if ( $this->isFieldFillable( $name ) )
                {
                    $query .= $name.' = ?, ';
                    $valuesArr[] = (string)$value;
                }
            }

            $query = rtrim( $query, ', ' ).' WHERE '.$this->db_primaryKey.' = '.$this->{$this->db_primaryKey}.';';
            return [ $query, $valuesArr ];
        }

        /**
         * Inserts a new record to the specified table.
         *
         * @param array $properties The array of filtered properties, containing the values.
         * @return array
         */
        private function forgeInsertQuery( &$properties )
        {
            $query = 'INSERT INTO '.$this->db_table.' ( ';
            $values = 'VALUES ( ';

            /**
             * The array of the new values to be escaped.
             * @var array $valuesArr
             */
            $valuesArr = [ ];

            // Iterate through the array of the right properties, and decide whether
            // it should, or should not be inserted; then we forge the query.
            foreach ( $properties as $name => &$value )
            {
                if ( $this->isFieldFillable( $name ) )
                {
                    $query .= $name.', ';
                    $values .= '?, ';

                    $valuesArr[] = (string)$value;
                }
            }

            $query = rtrim( $query, ', ' ).' ) '.rtrim( $values, ', ' ).' );';
            return [ $query, $valuesArr ];
        }

        /**
         * Checks if the specified field is fillable, or not.
         *
         * @param string $fieldName The name of the field to be judged.
         * @return bool
         */
        private function isFieldFillable( $fieldName )
        {
            // If the user has set the array of the locked fields, we want to
            // make sure we don't update those (black-list). So, if there is an
            // entry about this field in the list, we return false.
            if ( !empty( $this->locked ) && in_array( $fieldName, $this->locked ) )
            {
                return false;
            }

            // If the user has set the array of the updatable fields, we want to
            // make sure that we does not update anything, except those has been
            // declared (white-list). So, if here is no entry about this very field
            // int he list, we doesn't save anything (return false).
            else if ( !empty( $this->unlocked ) && !in_array( $this->unlocked, $fieldName ) )
            {
                return false;
            }

            // Everything is OK, we can save.
            return true;
        }
    }
}