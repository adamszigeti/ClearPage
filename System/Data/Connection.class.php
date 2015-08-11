<?php

namespace System\Data
{
    use System\Config\Project;
    use System\Config;

    class Connection
    {
        /**
         * The type of the connection [only mysql - for now].
         * @var string
         */
        private $type;

        /**
         * The host of the connection.
         * @var string
         */
        private $host;

        /**
         * The database you want to access to.
         * @var string
         */
        private $db;

        /**
         * The username for the connection.
         * @var string
         */
        private $userName;

        /**
         * The password for the connection.
         * @var string
         */
        private $userPassword;

        /**
         * @param string $type         Optional. The type of the connection [mysql - for now].
         * @param string $host         Optional. The host of the connection.
         * @param string $db           Optional. The database you want to access to.
         * @param string $userName     Optional. The username for the connection.
         * @param string $userPassword Optional. The password for the connection.
         */
        public function __construct( $type = '', $host = '', $db = '', $userName = '', $userPassword = '' )
        {
            // If any parameter is null, then we need to load
            // the default connection setting for the parameter,
            // and build this object based on them.
            include_once Project::get()->projectInfoPath();

            $this->type = empty( $type ) ? $conn_type : $type;
            $this->host = empty( $host ) ? $conn_host : $host;
            $this->db = empty( $db ) ? $conn_dbName : $db;
            $this->userName = empty( $userName ) ? $conn_usrName : $userName;
            $this->userPassword = empty( $userPassword ) ? $conn_usrPass : $userPassword;
        }

        /**
         * @return string
         */
        public function getType()
        {
            return strtolower( $this->type );
        }

        /**
         * @return string
         */
        public function getHost()
        {
            return $this->host;
        }

        /**
         * @return string
         */
        public function getDb()
        {
            return $this->db;
        }

        /**
         * @return string
         */
        public function getUserName()
        {
            return $this->userName;
        }

        /**
         * @return string
         */
        public function getUserPassword()
        {
            return $this->userPassword;
        }
    }
}