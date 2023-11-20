<?php

/**
 * Created by PhpStorm.
 * User: Chris Tenday
 * Date: 28/09/2018
 * Time: 12:51
 */
class Errors extends ERRORINIT
{
    private $moduleObj;
    protected $message;
    protected $code=200; /** error code default value */

    public function __construct(Module $obj,$message,$code=200)
    {
        parent::__construct($message);
        $this->message=$message;
        $this->moduleObj=$obj;
        $this->code=$code;
        $this->loadErrorMessage();
    }

    /**
     * this method loads the error message to the UI
     */
    private function loadErrorMessage()
    {
        http_response_code($this->code);
        $this->moduleObj->loadData("error",$this->message);
    }
}