<?php

/**
 * Created by PhpStorm.
 * User: Chris Tenday
 * Date: 20/04/2020
 * Time: 19:50
 */
class Builder
{

    private $needsToBeBuilt=true;


    public function build()
    {
        $controllersFolder=ROOT.DS."controllers";
        if(!is_dir($controllersFolder))
        {
            /** start building the architecture */

            $this->controllers();

            $this->modules();

            $this->models();

            $this->homeHtaccess();

            $this->homeHtaccess();


        }
    }

    private function controllers()
    {
        if(!is_dir(ROOT.DS."controllers"))
        {
            mkdir(ROOT.DS."controllers");

            $str="
            <?php 
            Class IndexController extends Controller
            {
                 
            }
            ?>";

            file_put_contents(ROOT.DS."controllers".DS."IndexController.php",$str);
        }
        else
        {
            $this->needsToBeBuilt=true;
        }
    }

    private function modules()
    {
        if(!is_dir(ROOT.DS."modules"))
        {
            mkdir(ROOT.DS."modules");

            mkdir(ROOT.DS."modules".DS."exemple");

            $str="
            <?php
                Class ExempleMODULE extends Module
                {
                
                }
            ?>";

            file_put_contents(ROOT.DS."modules".DS."exemple".DS."ExempleMODULE.php",$str);
        }
        else
        {
            $this->needsToBeBuilt=true;
        }
    }

    private function models()
    {
        if(!is_dir(ROOT.DS."models"))
        {
            mkdir(ROOT.DS."models");

            $language="\$language";
            $str="
            <?php
            Class ExempleModel extends Model
            {
                function __construct(\$language=null)
                {
                    parent::__construct(\$language);
                    
                    
                    /**EN: array containing details about the database to connect */
                    /**FR: array containant les details de la base de donnée  à connecter */
                    \$databaseConfiguration=array('host'=>'localhost','username'=>'root','password'=>'','database'=>'exemple_database');
                    
                    /** sending of the details for configuration */
                    /** Envoie des details de la configuration  */
                    \$this->setConfiguration(\$databaseConfiguration); 
                    
                    /**EN: Connection to the Database */
                    /**FR: Connexion à la base de donnée */
                    \$this->connect();
                }
            }
            ?>";
            file_put_contents(ROOT.DS."models".DS."ExempleModel.php",$str);
        }
        else
        {
            $this->needsToBeBuilt=true;
        }
    }

    private function homeHtaccess()
    {
        /** create the home .htaccess file */

        if(!file_exists(ROOT.DS.".htaccess"))
        {
            file_put_contents(ROOT.DS.".htaccess","
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule (.*) system/index.php [L]");
        }
        else
        {
            $this->needsToBeBuilt=true;
        }

    }

    public function isStructureBuilt()
    {
        return $this->needsToBeBuilt;
    }
}