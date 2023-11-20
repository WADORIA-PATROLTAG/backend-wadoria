<?php
 final class Session
 {
 	function __construct()
 	{
        if(!isset($_SESSION))
        {
          session_start();
        }
 	}

    /**
    * METHOD POUR LANCER UNE SESSION
    * @param 1 $name => LE NOM DU SESSION
    * @param 2 $session => LA VALEUR DE LA SESSION
    */
 	public function set($name,$session)
 	{
 		$_SESSION[$name]=$session;
 	}
 	/*=====================
 	 FIN - METHOD => set()
 	======================*/

  /**
  * METHOD POUR OBTENIR LA VALEUR D'UNE SESSION
  * @param $name => LE NOM DE LA SESSION
  * @return LA VALEUR DE LA SESSION
  */
  public function get($name)
  {
    if($this->isValid($name))
    {
       return $_SESSION[$name];
    }
    else
    {
      //SESSION INVALIDE , GERER L'ERREUR***
      throw new Exception("The session:$name does not exist.");
    }
  }
  /*====================
   FIN - METHOD => get()
  ====================*/
    
    /**
    * METHOD POUR VERIFIER UNE SESSION
    * @param $name => LE NOM DU SESSION
    * @return TRUE OU FALSE
    */
 	public function isValid($name)
 	{
 		if(isset($_SESSION[$name]))
 		{
 			return true;
 		}
 		return false;
 	}
 	/*=======================
 	 FIN -METHOD => isValid()
 	=======================*/

    /**
    * METHOD POUR SUPPRIMER UNE SESSION
    * @param $name => LE NOM DE LA SESSION
    * @return TRUE OU FALSE
    */
 	public function delete($name)
 	{
 		if($this->isValid($name))
 		{
           unset($_SESSION[$name]);
           return true;
 		}
 		else
 		{
           return true;
 		}
 	}
 	/*=========================
 	 FIN - METHOD => delete()
 	==========================*/
 }
?>