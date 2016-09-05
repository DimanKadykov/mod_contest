<?php

class ContestDb
{
    private $db;

    public function __construct()
    {
    }


    protected function getDb()
    {
        return $this->db;
    }
}