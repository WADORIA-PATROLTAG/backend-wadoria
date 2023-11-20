<?php

/**
 * Created by PhpStorm.
 * User: Christian
 * Date: 2/8/2018
 * Time: 1:50 PM
 * usage: this class is for manipulating database configuration.
 */
class DBConf
{
   private $languages=array("Mysqli","PDO");
   private $language;
   private $configurations=array();
   protected $conf=array();

   protected function useLanguage($language)
   {
      if(in_array($language,$this->languages))
      {
          $this->language=$language;
      }
      else
      {
          throw new Exception("Sql language:$language is not supported.");
      }
   }

   public function checkLanguage()
   {
       return $this->language;
   }

   public function setConfiguration(array $conf)
   {
       $this->conf=$conf;
   }

   public function checkConf()
   {
       return $this->conf;
   }
}