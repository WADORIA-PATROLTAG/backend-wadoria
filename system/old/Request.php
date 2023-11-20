<?php

/**
 * Created by PhpStorm.
 * User: Christian
 * Date: 2/4/2018
 * Time: 6:04 PM
 */
class Request
{
    public static $websiteDomain;
    public $url;
    public $controller;
    public $params=array();
    private static $customUrlFolder,$customUrlFolderHandler;

    private static $logPath=SYSTEM.DS."logs";
    private static $logs=array();
    private static $log=array();

   function __construct()
   {
       $requestHeaders=apache_request_headers();
       $access=(isset($requestHeaders['Authorization']))? $requestHeaders['Authorization']=="angularjsFd0Req" : false ;
       /** refuse access without authorization */
       if($access==false)
       {
           //header("http://www.error.php");
           //http_response_code(404);
           //echo "refusé";
           //exit();
       }

       /** Filter and Sanitize the input when a POST/GET request is made */
       require_once(SYSTEM.DS."utilities".DS."Validator.php");
       if($_SERVER['REQUEST_METHOD']=="GET")
       {
           $_GET=Validator::filter($_GET);
       }
       else if($_SERVER['REQUEST_METHOD']=="POST")
       {
           if(count($_POST)==0)
           {
               /**
                * si les données sont envoyes en format json
                */
               $dataFromUI=json_decode(file_get_contents("php://input",true));
               if(!is_null($dataFromUI))
               {
                   $_POST=convertToArray($dataFromUI);
               }


           }
            $_POST=Validator::filter($_POST);

            if(isset($_FILES))
            {
                /** include the file uploader */
                require_once(SYSTEM.DS."utilities".DS."FileUploader.php");
            }
       }
       else{}

       if(isset($_SERVER['HTTP_HOST']))
       {
           if(isset($_SERVER['REQUEST_SCHEME']))
           {
               $http=$_SERVER['REQUEST_SCHEME'].'://';
           }
           else
           {
               $http="http://";
           }
           if(isset($_SERVER['HTTPS']))
           {
               if($_SERVER['HTTPS']=='on')
               {
                   $http="https://";
               }
               else
               {
                   $http="http://";
               }
           }
           else
           {
               $http="http://";
           }
           self::$websiteDomain=$http.$_SERVER['HTTP_HOST'];

           /** Start request Login*/
           $this->startLogging();

       }
       /** @var url the original url*/
       $this->url=trim($_SERVER['REQUEST_URI'],'/');
       /** clean the url ,remove the 'b' prefix */
       $this->url=explode('/',$this->url);
       $this->url=remove_by_value($this->url,"backend");
       $this->url=implode('/',$this->url);

       /** if no controller specified use the default Controller
        * called "Index.php Controller" */
       if($this->url=="")
       {
           $this->url="index";
       }

       /** handle pagination */
       if(isset($_GET['page']))
       {
           $this->url=explode('?',$this->url)[0];
           if(!is_numeric($_GET['page']) OR $_GET['page']<1)
           {
               /** throw Url Invalide error */
               $this->url.="###";
           }
       }

      self::$customUrlFolder=ROOT.DS."system".DS."custom_urls";
      self::$customUrlFolderHandler=opendir(self::$customUrlFolder);

       /** parse the url */
       $this->parseUrl($this->url);
   }

   /**
     * method to parse the url so that the system can understand it
     * @param $url => the url to parse
     */
   private function parseUrl($url)
   {
     /** check custom url usage */
     $url=self::checkCustomUrl($url);

     if(strchr($url,"?"))
     {
         $url=explode("?",$url)[0];
     }
     if(strstr($url,'/'))
     {
        $urlParsed=explode('/',$url);

        if(strstr(ROOT,$urlParsed[0]))
        {
            /** not using virtual host */
            $urlParsed=remove_by_key($urlParsed,0);
        }
        $this->controller=$urlParsed[0];
     }
     else
     {
         if(strstr(ROOT,$url))
         {
             /** not using virtual host */
             $this->controller="/";
         }
         else
         {
             /** using virtual host */
             $this->controller=$url;
         }


     }
     /** in case data has been sent through GET */
     if(strchr($this->controller,'?'))
     {
         $this->controller=explode('?',$this->controller)[0];
     }

     if(strstr($url,'/'))
     {
         //exit(print_r($urlParsed));
         if(count($urlParsed)>0)
         {
             for($i=1; $i<count($urlParsed); $i++)
             {
                 $this->params[]=$urlParsed[$i];
             }
         }
     }
     $this->saveUrlRequested($url);
   }

    /**
     * method to create a custom url
     * @param $customUrl => the custom url
     * @param $meaning => the meaning of the url
     */
   public static function createCustomUrl($customUrl,$meaning)
   {
       /** first check if this custom has already been created */
       if(self::isCustomUrlUsed($customUrl))
       {
           /** used */
           return false;
       }

       /** create the custom url file */
       $filename="url_".rand(1,99).time();

       /** build the custom url json object */
       $url['url']=$customUrl;
       $url['meaning']=$meaning;
       $url['created_on']=time();
       $urlJsonObject=json_encode($url);

       file_put_contents(self::$customUrlFolder.DS.$filename,$urlJsonObject);

       return true;
   }

    /**
     * method for getting custom url
     * @param $meaning => the meaning of the url
     */
    public static function getCustomUrl($meaning)
    {
        self::$customUrlFolder=ROOT.DS."system".DS."custom_urls";
        self::$customUrlFolderHandler=opendir(self::$customUrlFolder);

        while($file=readdir(self::$customUrlFolderHandler))
        {
            if($file=="." OR $file=="..")
            {
                continue;
            }
            $custom=json_decode(file_get_contents(self::$customUrlFolder.DS.$file));
            if(strtolower($custom->url)==strtolower($meaning))
            {
                //echo self::$websiteDomain."/".$custom->meaning; exit();
                return self::$websiteDomain."/".$custom->meaning;
                break;
            }
        }
        return self::$websiteDomain."/".$meaning;
    }
    /**
     * method for checking whether a custom url has already been created
     * @param $customUrl =>the custom ur to check
     * @return bool => True | False
     */
   private static function isCustomUrlUsed($customUrl)
   {
       while($file=readdir(self::$customUrlFolderHandler))
       {
           if($file=="." OR $file=="..")
           {
               continue;
           }
           $custom=json_decode(file_get_contents(self::$customUrlFolder.DS.$file));
           if($custom->url==$customUrl)
           {
               return true;
               break;
           }
       }
       return false;
   }

    /**
     * method for checking if the requested url is custom
     * @param $url => the url to be checked
     * @return mixed => return the proper url.
     */
   private static function checkCustomUrl($url)
   {
       while($file=readdir(self::$customUrlFolderHandler))
       {
           if($file=="." OR $file=="..")
           {
               continue;
           }

           $custom=json_decode(file_get_contents(self::$customUrlFolder.DS.$file));
           if($custom->meaning==$url)
           {
               //print_r($custom); echo $url; exit();
               $url=$custom->url;
               break;
           }
       }
       return $url;
   }

    /**
     * method for saving the request url for later use
     * @param $url => the url to save
     */
   private function saveUrlRequested($url)
   {
       /** save the url taped */
       $this->url=explode('/',$url);
       //$this->url=remove_by_key($this->url,0);
       $this->url=implode('/',$this->url);
   }

    /**
     * method to build an url
     * @param $url =>the url to build
     * @return string =>the url built
     */
   public static function urlBuild($url)
   {
      $url=trim($url,'/');
      //$url=BASE_URL.'/'.$url;
      //self::encryptUrl($url);
      return '/'.$url;
   }

    /**
     * method for checking if a POST request is made
     * @return bool
     */
    public static function isPost()
   {
       if($_SERVER['REQUEST_METHOD']=="POST")
       {
           return true;
       }
       else
       {
           return false;
       }
   }

    /**
     * method for checking if a GET request is made
     * @return bool
     */
   public static function isGet()
   {
       if($_SERVER['REQUEST_METHOD']=="GET")
       {
           return true;
       }
       return false;
   }

    /**
     * Method for logging request to the server
     */
   private function startLogging()
   {
       //(isset($_SERVER['REQUEST_SCHEME']))? $_SERVER['REQUEST_SCHEME'] : "not set"
       self::writeLog("url",self::$websiteDomain.$_SERVER['REQUEST_URI']);
       self::writeLog("ip",(isset($_SERVER['REMOTE_ADDR']))? $_SERVER['REMOTE_ADDR'] : "undefined");
       self::writeLog("method",(isset($_SERVER['REQUEST_METHOD']))? $_SERVER['REQUEST_METHOD'] : "undefined");
       self::writeLog("REQUEST_SCHEME",(isset($_SERVER['REQUEST_SCHEME']))? $_SERVER['REQUEST_SCHEME'] : "undefined");
       self::writeLog("user_agent",(isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT'] : "undefined");
       self::writeLog("when",time());
       /**
        *  request classification :
        * 0=> normal, 1=> suspicious ,  2=> danger, 3=> debug
        */
       self::writeLog("class",0);
       if(isset($_SERVER['REDIRECT_STATUS']))
       {
           self::writeLog("response_code",$_SERVER['REDIRECT_STATUS']);
       }

   }

    /**
     * Mehod for writing a log
     * @param $key
     * @param $data
     */
   public static function writeLog($key,$data)
   {
       self::$log[$key]=$data;
   }

    /**
     * Method for storing logs
     */
   private function perfomLogging()
   {
       if(!is_dir(self::$logPath))
       {
           mkdir(self::$logPath);
       }
       else
       {
           /** get existing logs */
           if(is_file(self::$logPath.DS."logs_1.dat"))
           {
               $content=file_get_contents(self::$logPath.DS."logs_1.dat");
               self::$logs=json_decode($content);
           }

       }

       array_push(self::$logs,self::$log);

       $logsJson=json_encode(self::$logs);
       file_put_contents(self::$logPath.DS."logs_1.dat",$logsJson);
   }

    public static function executeRequest($url,$data)
    {
        /** @var  $ch => start curl session */
        $ch=curl_init($url);

        /** set curl session options */
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
        curl_setopt($ch,CURLOPT_HTTPHEADER,
            array("Content-Type:application/json"));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        /** @var  $responseObj => execute the curl session
         * and get stdClass response object returned */
        $response=curl_exec($ch);
        $responseObj=json_decode($response); //decode into json format
        if(is_null($responseObj))
        {
            return $response;
        }

        if(curl_errno($ch)==0)
        {
            curl_close($ch);
            /** check if the transaction request was done successfully */
            if(count($responseObj)<1)
            {
                return null;
            }
            return $responseObj; //return the response object

        }
        else
        {
            /** couldn't execute the curl no Internet */
            return null;
            //require_once(ROOT.DS."modules".DS."tickets".DS."PaymentFailed.php");
            //throw new PaymentFailed();
        }

    }

    /**
     * Method for logging a request detail
     * @param $key
     * @param $data
     */
   function __destruct()
   {
       self::perfomLogging(); /** executing logging */
   }

}
