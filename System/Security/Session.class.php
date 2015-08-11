<?php

namespace System\Security
{
    class Session
    {
        private function generateID()
        {
            return sha1( uniqid( '', true ).str_random( 25 ).microtime( true ) );
        }
    }
}