<?php

namespace System\Data\Connections {

    use System\Data\Connection;
    use PDO;
    use PDOStatement;
    
    /**
     * Collection of methods, which lets you interact with your
     * MySQL database.
     *
     * Class Mysql
     * @package System\Data\Connections
     */
    class Mysql
    {
        /**
         * The array containing all the queries we want to execute.
         *
         * @var PDOStatement[] $con
         */
        private $queries;

        /**
         * The connection, we want to execute the queries on.
         *
         * @var PDO $connection
         */
        private $connection;

        /**
         * @param Connection $dbConnection An instance of a connection.
         */
        public function __construct( Connection &$dbConnection )
        {
            // If the connection object is present, and it's type is
            // "mysql", we instantiate this class, otherwise we display
            // an error.
            if ( !empty( $dbConnection ) && $dbConnection->getType() === 'mysql' )
            {
                $this->connection = new PDO( 'mysql:host='.$dbConnection->getHost().';dbname='.$dbConnection->getDb().';charset=utf8', $dbConnection->getUserName(), $dbConnection->getUserPassword() );
                $this->queries = [ ];
                return;
            }

            // Throwing error, if one of the statements checked above
            // is false.
            // TODO: Change this to an actual error-handling class-call!
            echo '<strong>Error:</strong> incorrect connection type given for MySQL!';
            exit;
        }

        /**
         * Prepares and executes a simple query on the database, with no
         * expected return value (such as "INSERT", "UPDATE", and "DELETE").
         *
         * @param string $query  The query you want to run on the database.
         * @param string[]  $values Optional. The array of values, you want to resolve the query with.
         *
         * @return bool
         */
        public function query( $query, array $values = [ ] )
        {
            // If the query string is empty, we won't execute anything,
            // since we have no SQL statement to work with.
            if ( empty( $query ) )
            {
                return false;
            }

            // If the second parameter is missing, it means that the user
            // just want to execute a simple query, and there is no need
            // for it's resolving.
            if ( empty( $values ) )
            {
                return $this->connection->query( $query ) ? true : false;
            }

            // If both the parameters are present, then the user wants
            // to execute a query, with it's parameters resolved. So,
            // we will do just that, with PDO:
            $sqlCommand = $this->connection->prepare( $query );
            return $sqlCommand->execute( $values ) ? true : false;
        }

        /**
         * Returns with the array of fetched mysql rows.
         *
         * @param string $query  The query you want to run on the database.
         * @param array  $values Optional. The array of values, you want to resolve the query with.
         *
         * @return array
         */
        public function select( $query, array $values = null )
        {
            // If the query string is empty, we won't execute anything,
            // since we have no SQL statement to work with.
            if ( empty( $query ) )
            {
                return false;
            }

            $pdoRow = $this->connection->prepare( $query );
            $pdoRow->execute( $values );

            // Since one way or another, we already have a PDO object in
            // the $pdoRow variable, we can just fetch it, and return it:
            return $pdoRow->fetchAll( PDO::FETCH_ASSOC );
        }
    }
}