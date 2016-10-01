<?php

namespace App;

class Token
{
    public $decoded;

    public function hydrate($decoded)
    {
        $this->decoded = $decoded;
    }
    
    public function getUsername()
    {
        return $this->decoded->sub;
    }

    public function getUserId()
    {
        return $this->decoded->userId;
    }
}
