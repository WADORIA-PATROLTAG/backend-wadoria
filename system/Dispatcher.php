<?php

/**
 * Created by PhpStorm.
 * User: Christian
 * Date: 2/4/2018
 * Time: 11:08 PM
 */
class Dispatcher
{
    private $request;
    private $session;

   function __construct()
   {
       global $session;
       $this->session=$session;
       //make request
       $this->request=new Request();

       $controller=$this->loadController($this->request->controller);
       $classMethods=(get_class_methods(get_class($controller)));
       if(count($this->request->params)<1)
       {
           if(in_array($this->request->controller,$classMethods))
           {
               $method=$this->request->controller;
               $controller->$method();
           }

       }
       else
       {
           if(count($this->request->params)>0)
           {
               $method=$this->request->params[0];

               try
               {
                   $this->request->params=remove_by_key($this->request->params,0);
                   if(count($this->request->params)<2)
                   {
                       $controller->$method();
                   }
                   else
                   {
                       $params=array_slice($this->request->params,1);
                       $controller->$method($params);
                   }
               }
               catch (Errors $e1)
               {

               }
               catch (Exception $e)
               {
                   echo $e->getMessage();
                   http_response_code(404);
                 $this->loadController("InvalidController");
               }
           }
       }
   }

    /**
     * method for loading a controller
     */
    private function loadController($controller)
    {
        if($this->isControllerExist($controller))
        {
            $controller=ucfirst($controller);
            require_once (ROOT.DS."controllers".DS.$controller.".php");

            /** handle all uncaught errors */
            try
            {
                return new $controller($this->request,$controller,$this->session);
            }catch (Exception $e)
            {
                echo $e->getMessage();
                exit(); /** stop the execution */
            }

        }
        else
        {

            /*if(!$this->isControllerExist("InvalidController"))
            {
                throw new Exception("Please create a controller
                 named:InvalidController for handling execution when a non-valid controller is used");
            }*/
            $controller="IndexController";
            require_once(ROOT.DS."controllers".DS.$controller.".php");
            return new $controller($this->request,$controller,$this->session);
        }
    }
    /** */

    /**
     * method for checking whether a controller exist
     * @param $controller => the controller to check
     * @return bool True|False
     */
    private function isControllerExist($controller)
    {
        $controller=ucfirst($controller).".php";
        $allControllers=array();
        $handle=opendir(ROOT.DS."controllers");
        while($file=readdir($handle))
        {
            $allControllers[]=$file;
        }
        if(in_array($controller,$allControllers))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}