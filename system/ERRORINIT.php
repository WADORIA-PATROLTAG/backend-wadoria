<?php

/**
 * Created by PhpStorm.
 * User: Chris Tenday
 * Date: 28/09/2018
 * Time: 12:50
 */
class ERRORINIT extends Exception
{

    public function __construct($str="",$message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}