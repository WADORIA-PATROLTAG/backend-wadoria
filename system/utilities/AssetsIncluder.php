<?php

/**
 * Created by PhpStorm.
 * User: Chris Tenday
 * Date: 10/03/2018
 * Time: 11:28
 */
class AssetsIncluder
{
    private $folder=ROOT.DS."pages".DS."assets".DS;
    public function css()
    {
       $handle=opendir($this->folder."css");
       while($file=readdir($handle))
       {
           if($file=="." OR $file=="..")
           {
               continue;
           }
           $css[]=$file;

       }
       return $css;
    }

    public function js()
    {
        $handle=opendir($this->folder."js");
        $js=array("bootstrap.min.js");
        while($file=readdir($handle))
        {
            if($file=="." OR $file=="..")
            {
                continue;
            }
            $js[]=$file;
        }
        $js=remove_by_value($js,array("icheck.min.js","bootstrap.min.js"));
        $js[]="icheck.min.js";
        return $js;
    }

    public function bootstrap()
    {
        $handle=opendir($this->folder."bootstrap");
        while($file=readdir($handle))
        {
            if($file=="." OR $file=="..")
            {
                continue;
            }
            $boostrap[]=$file;
        }
        return $boostrap;
    }

    public function fonts()
    {
        $handle=opendir($this->folder."fonts");
        while($file=readdir($handle))
        {
            if($file=="." OR $file=="..")
            {
                continue;
            }
            $fonts[]=$file;
        }
        return $fonts;
    }

    public function slick()
    {
        $handle=opendir($this->folder."slick");
        while($file=readdir($handle))
        {
            if($file=="." OR $file=="..")
            {
                continue;
            }
            $slick[]=$file;
        }
        return $slick;
    }


}