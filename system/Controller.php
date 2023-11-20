<?php

/**
 * Created by PhpStorm.
 * User: Christian
 * Date: 2/5/2018
 * Time: 12:16 AM
 */
class Controller
{
   protected $session;
   protected $request;
   protected $Controller;
   public $viewsLoaded=array();
   private $uiData=array(); /** storing all data for the UI */

   function __construct($request,$Controller,$session)
   {
       $this->request=$request;
       $this->Controller=$Controller;
       $this->session=$session;
   }

   protected function loadModule($Controller,$module)
   {
       spl_autoload_unregister("Autoloader::system");
       spl_autoload_register('Autoloader::modules');
      /** execute start-up operations */
      $this->startupOperations($Controller,$module,$this->request);
      $main=ucfirst($module)."MODULE";
      require_once(ROOT.DS."modules".DS.lcfirst($module).DS.$main.".php");

      return new $main($Controller,$module,$this->request);
   }

    /**
     * method to execute start-up operations when the system starts
     */
   private function startupOperations($Controller,$module,$request)
   {
       require_once(SYSTEM.DS."StartupOperations.php");
       $operations=new StartupOperations($Controller,$module,$request);
       $operations->start();
   }

   protected function loadModel($modelName,$sqlLanguage=null)
   {
       require_once(ROOT.DS."Models".DS.ucfirst($modelName).".php");
       if(is_null($sqlLanguage))
       {
           return new $modelName();
       }
       else
       {
           return new $modelName($sqlLanguage);
       }
   }

   protected function redirect($url)
   {
       $url=trim($url,'/');
       //$url=BASE_URL.'/'.$url;
       header("location:".'/'.$url);
   }

   /** method to save an object as of stdClass */
   protected function saveObject($objectName,stdClass $object)
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

   protected function getObject()
   {
       if($this->session->isValid("object"))
       {
           return unserialize($this->session->get("object"));
       }
       else
       {
           return null;
       }
   }

   protected function saveRoute($url=null)
   {
       if(is_null($url))
       {
           $url=$_SERVER['REQUEST_URI'];
       }

       $this->session->set("route",$url);
   }

   protected function isRouteSaved()
   {
       if($this->session->isValid("route"))
       {
           return true;
       }
       else
       {
           return false;
       }
   }

   protected function continueRoute()
   {
       if(!$this->session->isValid("route"))
       {
           throw new Exception("Route does not exist.");
       }
       $route=$this->session->get("route");
       $this->session->delete("route");
       $this->redirect($route);
   }

   protected function getRoute()
   {
       if(!$this->session->isValid('route'))
       {
           return null;
       }

       $route=$this->session->get('route');
       $this->session->delete('route');
       return $route;
   }

    /**
     * @param $viewName
     * @param $view
     * @param null $data
     */
   public function saveView($viewName, $view, $data=null)
   {
      $this->viewsLoaded[$viewName]=array("ref"=>ROOT.DS."modules".DS.$view,"data"=>$data);
      if(!is_null($data))
      {
          if(!is_array($data))
          {
              throw new Exception("Data should be sent as array.");
          }
          else
          {
             $this->viewsLoaded[$viewName]['data']=$data;
          }
      }
      else
      {
          $this->viewsLoaded[$viewName]['data']=array(null);
      }
   }

    /**
     * this method is used for catching up when a non-existent method is
     * called
     * @param $name
     * @param $arguments
     */
   public function __call($name, $arguments)
   {
       http_response_code(404);
       $this->saveData("error","Url Invalid.");
       exit(); /** stop everything */
   }

    /**
     * method for saving data loaded from a module
     * @param $dataName => data name
     * @param $data => data in string,number or array
     */
   public function saveData($dataName, $data)
   {
       $this->uiData[$dataName]=$data;
   }

    /**
     * method for loading a page template
     * @param $templateName =>name of the template
     */
    public function loadPageTemplate($templateName)
   {
       require_once(ROOT.DS."pages".DS."templates".DS.$templateName.".html");
   }

    /**
     * method for dispatching the data loaded to the UI
     */
   private function dispatchUiData()
   {
       if(count($this->uiData)>0)
       {
           header("Content-type:application/json");

           $data=json_encode($this->uiData);

           /** send to the browser */
           print_r($data);
       }
   }

   function __destruct()
   {
       /** dispatch data loaded to the UI */
       $this->dispatchUiData();
   }
}