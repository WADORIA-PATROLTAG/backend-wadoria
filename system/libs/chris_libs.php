<?php
 /** method to remvove array element(s) by value(s) */
 function remove_by_value(array $array,$value)
 {
   $newArray=array();

   for($i=0; $i<count($array); $i++)
   {
       if(is_array($value))
       {
           if(in_array($array[$i],$value))
           {
               continue;
           }
       }
       else
       {
           if($array[$i]==$value)
           {
               continue;
           }
       }
       $newArray[]=$array[$i];
   }

   return $newArray;
 }

 /** method to remove array element(s) by key(s) */
 function remove_by_key(array $array,$key)
 {
    $newArray=array();
        foreach($array as $k=>$v)
        {
            if(is_array($key))
            {
                if(in_array($k,$key))
                {
                    continue;
                }
                $newArray[$k]=$v;
            }
            else
            {
                if(is_numeric($key))
                {
                    if($k==$key)
                    {
                        continue;
                    }
                    $newArray[]=$v;
                }
                else
                {
                    if($k==$key)
                    {
                        continue;
                    }
                    $newArray[$k]=$v;
                }
            }
        }
        return $newArray;
 }

 /** method to change a key of an array */
 function change_array_key(array $array,$keyToChange,$changeTo)
 {
     if(is_numeric($keyToChange) OR is_numeric($changeTo))
     {
         return null;
     }

     foreach($array as $key=>$data)
     {
         if($key==$keyToChange)
         {
             $arrayChanged[$changeTo]=$data;
             continue;
         }
         $arrayChanged[$key]=$data;
     }

     return $arrayChanged;
 }

 /** method to convert an object to an array */
 function convertToArray($object)
 {
     $array=array();
     if(!is_object($object))
     {
         throw new Exception("convertToArray() method takes only an Object as arg");
     }

     $properties=get_object_vars($object);

     foreach($properties as $key=>$val)
     {
         $array[$key]=$object->$key;
     }

     return $array;
 }

?>