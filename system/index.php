<?php

   define("SYSTEM",dirname(__FILE__));
   define("ROOT",dirname(SYSTEM));
   define("DS",DIRECTORY_SEPARATOR);
   define("MODULES",ROOT.DS."Modules");
   define("BASE_URL",dirname(dirname($_SERVER['SCRIPT_NAME'])));

   //require_once(ROOT.DS."Request.php");
   require_once(SYSTEM.DS."Controller.php");
   require_once (SYSTEM.DS."Module.php");
   require_once (SYSTEM.DS."DBConf.php");
   require_once (SYSTEM.DS."Model.php");
   require_once (SYSTEM.DS."libs".DS."chris_libs.php");
   require_once(SYSTEM.DS."Autoloader.php");
   require_once (SYSTEM.DS."ERRORINIT.php");
   require_once (SYSTEM.DS."Errors.php");

   /** include utilities */
   require_once(SYSTEM.DS."utilities".DS."Validator.php");
   require_once(SYSTEM.DS."utilities".DS."FileUploader.php");
   require_once(SYSTEM.DS."utilities".DS."AssetsIncluder.php");

   spl_autoload_register("Autoloader::system");
   /*function __autoload($class)
   {
       require_once(SYSTEM.DS.$class.".php");
   }*/

   $session=new Session();

   $errors=array(); /** to save system errors */
   function loadError($errorName,$msg)
   {
      global $errors;
      $errors[$errorName]=$msg;

      global $session;
      if($session->isValid("errorsObj"))
      {
          $savedErrors=unserialize($session->get("errorsObj"));
          $savedErrors->$errorName=$msg;
          $session->set("errorsObj",serialize($savedErrors));
      }
      else
      {
          $errorsObj=new stdClass();
          $errorsObj->$errorName=$msg;
          /** save the error object */
          $session->set("errorsObj",serialize($errorsObj));
      }
   }
    date_default_timezone_set("africa/kinshasa");
    $today=date("d-m-Y");
    $now=date("H:i");

    $builder=new Builder();
    $builder->build();
    $start=new Dispatcher();

?>