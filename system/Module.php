<?php

/**
 * Created by PhpStorm.
 * User: Christian
 * Date: 2/5/2018
 * Time: 12:52 AM
 */
class Module
{
    protected $request;
    protected $session=null;
    protected $controller;
    protected $module;
    protected $dateNow;
    protected $timeNow;

    function __construct($controller,$module,Request $request=null)
    {
        if(get_parent_class($controller!="Controller"))
        {
            throw new Exception(get_class($controller)." is not a Controller.");
        }
        if(get_parent_class($module!="Module"))
        {
            throw new Exception(get_class($module)." is not a module.");
        }
        else
        {
            $this->controller=$controller;
        }

        /** save request obj */
        $this->request=$request;

        /** save the session object */
        if(is_null($this->session))
        {
            global $session;
            $this->session=$session;
        }

        /** save now date and time */
        global $today,$now;
        $this->dateNow=$today;
        $this->timeNow=$now;
    }

    /** method to communicate with a module by instatiating its object */
    public function communicateWithModule($module)
    {
        //spl_autoload_unregister("Autoloader::models");
        //spl_autoload_register('Autoloader::modules');
        $main=ucfirst($module)."MODULE";
        require_once(ROOT.DS."modules".DS.lcfirst($module).DS.$main.".php");
        return new $main($this->controller,$module,$this->request);
        //echo ROOT.DS."modules".DS.lcfirst($module).DS.$main.".php";
        //echo "####".ROOT.DS."modules".DS."bigData".DS."BigDataMODULE.php";
        //exit();
        //require_once(ROOT.DS."modules".DS."bigData".DS."BigDataMODULE.php");
    }

    /** method to load a db model */
    protected function loadModel($modelName,$sqlLanguage=null)
    {
        spl_autoload_unregister("Autoloader::modules");
        spl_autoload_register('Autoloader::models');

        require_once(ROOT.DS."models".DS.ucfirst($modelName).".php");
        if(is_null($sqlLanguage))
        {
            return new $modelName();
        }
        else
        {
            return new $modelName($sqlLanguage);
        }
    }

    /** method to save an object as of stdClass */
    public function saveObject($objectName,stdClass $object)
    {
        global $session;

        if($this->isObjectSaved($objectName))
        {
            /** if a version of this object has been saved */

            /** destroy old version of the object first */
            $this->destroyObject($objectName);

            /** now save the latest version of the object */
            $session->set($objectName,serialize($object));
        }
        else
        {
            $session->set($objectName,serialize($object));
        }
    }

    /** method to use a saved object */
    public function useObject($objectName,$saveCopy=false)
    {
        global $session;

        if($session->isValid($objectName))
        {
            $obj=$session->get($objectName);
            if(!$saveCopy)
            {
                $this->destroyObject($objectName); //destroy the object
            }

            return unserialize($obj);
        }
    }

    /** method to check if a certain object has been saved */
    protected function isObjectSaved($objectName)
    {
        global $session;

        if($session->isValid($objectName))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /** method to destroy an object */
    protected function destroyObject($objectName)
    {
        global $session;

        if($session->isValid($objectName))
        {
            $session->delete($objectName);
        }
    }

    /**
     * method for loading data for the UI
     * @param $dataName =>the name of the data
     * @param $data => the data either a string,number or an array.
     */
    public function loadData($dataName, $data)
    {
        $this->controller->saveData($dataName,$data);
    }
}