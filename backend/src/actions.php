<?php

namespace cbos;

class actions
{
    public $hasura;


    public function __construct($hasura)
    {
        $this->hasura = $hasura;
    }

    public function get_hello()
    {
        // print_r($this->hasura);
        return '** hello actions **';
    }

}