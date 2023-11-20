<?php

/**
 * Created by PhpStorm.
 * User: Chris Tenday
 * Date: 26/09/2018
 * Time: 14:01
 */
class Autoloader
{
    /**
     * method for loading a system class
     * @param $className
     */
    public static function system($className)
    {
        require_once(SYSTEM.DS.$className.".php");
    }

    /**
     * method for loading a module class
     * @param $className
     */
    public static function modules($className)
    {
        /** traverse all modules to include this class */
        $moduleFolder=ROOT.DS."modules";
        $handle=opendir($moduleFolder);
        while($module=readdir($handle))
        {
            if($module=="." OR $module=="..")
            {
                continue;
            }

            if(is_dir($moduleFolder.DS.$module))
            {
                $folder=$moduleFolder.DS.$module;
                $handle2=opendir($folder);
                while($class=readdir($handle2))
                {
                    if($class=="." OR $class=="..")
                    {
                        continue;
                    }

                    if($class==$className.".php")
                    {
                        require_once($folder.DS.$className.".php");
                    }
                }
            }

        }
    }

    /**
     * method for loading a class found in models folder
     * @param $className
     */
    public static function models($className)
    {
        require_once(ROOT.DS."models".DS.$className.".php");
    }
}