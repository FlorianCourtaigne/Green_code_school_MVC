<?php

// Tac's job
namespace Recycle\Controller;

abstract class AbstractAPIController
{
    public function __construct()
    {
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST,GET,PUT,DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
    }
}
