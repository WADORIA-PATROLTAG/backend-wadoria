<?php
 final class Validator
 {
   public static  $informations=array();
 	/**
 	* METHOD POUR FILTRER UN STRING
 	* @param $string => LE STRING A FILTRER
 	* @return $string => LE STRING FILTRE 
 	*/
    public static function filter($data)
    {
      if(is_array($data))
      {
         foreach($data as $key=>$val)
         {
           if(is_string($val))
           {
             $data[$key]=filter_var($data[$key],  FILTER_SANITIZE_STRING);
             $data[$key]=htmlspecialchars($data[$key]);
             $data[$key]=stripslashes($data[$key]);
             $data[$key]=trim($data[$key]);
           }
         }
      }
      else
      {
         $data=filter_var($data,  FILTER_SANITIZE_STRING);
         $data=htmlspecialchars($data);
         $data=stripslashes($data);
         $data=trim($data);
      }
	   return $data ; //DONNEE FILTREE
    }  
    /*===================
     FIN - METHOD =>filter()
    ===================*/

    /**
    * METHOD POUR VALIDER UNE DONNEE ENTREE
    * @param 1 $typeOfData => le type de donnee a valider
    * @param 2 $data => le donnee a valider en format 'string'
    * @return true ou false
    */
    public static function validate($typeOfData,$data)
    {
       if($typeOfData=="email")
       {
          //valider l'email***
       	  return true;
       }
       else if($typeOfData=="phone")
       {
          //valide le numero de phone ***
       	  return true;
       }
       else if($typeOfData=="prix")
       {
          //VALIDEZ LE PRIX ENTRE**
          return true;
       }
       else
       {
       	 //type de donnee passee invalide , jeter une erreur
       	 throw new Exception("Ce type de donnee << $typeOfData >> ne peut etre valider.");
       }
    }
    /*============================
     fin - method => validate()
    ============================*/
 }
?>