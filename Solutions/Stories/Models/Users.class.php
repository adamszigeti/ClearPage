<?php

namespace Stories\Models
{
    use System\Components\Model;

    class Users extends Model
    {
        protected $db_table = 'users';
        protected $locked = [ 'id' ];

        protected $id;
        protected $password;

        public $name;
        public $email;

        public function __construct()
        {

        }

        public function __toString()
        {
            return $this->name;
        }
    }
}