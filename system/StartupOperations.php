<?php

/**
 * Created by PhpStorm.
 * User: Chris Tenday
 * Date: 02/04/2018
 * Time: 10:41
 */
class StartupOperations
{
   private $controller;
   private $module;
   private $request;

   function __construct($controller,$module,$request)
   {
       $this->controller=$controller;
       $this->module=$module;
       $this->request=$request;
   }

    public function start()
    {
        $module=ucfirst($this->module)."MODULE";
        //echo ROOT.DS."modules".DS.lcfirst($this->module).DS.ucfirst($this->module)."MODULE.php";
        //exit();
        require_once(ROOT.DS."modules".DS.lcfirst($this->module).DS.ucfirst($this->module)."MODULE.php");
        try
        {

            $classModule=new $module($this->controller,$this->module,$this->request);
            if(in_array('startup',get_class_methods($classModule)))
            {
                /** execute the startup() method of the module */
                $classModule->startup();
            }
        }catch (Errors $e)
        {

        }
   }

   /**
     * method to update events status when they are passed.
     */
   private function updateEvents()
   {

   }

   /** method to load a module for start-up operation */
   private function loadModule($Controller,$module)
   {
       $main=ucfirst($module)."MODULE";
       require_once(ROOT.DS."modules".DS.lcfirst($module).DS.$main.".php");
       return new $main($Controller,$module,$this->request);
   }
}