<?php
/*Libray class to handle user uploaded images*/
/*
  --DOCUMENTATION--
  HOW TO USE THIS LIBRARY:
*     1. @ public method uploads() takes 3 arguments: array containing image properties,image key
*(value of name="" used in html form), one of 3 predefined folder to save the image.
*        @ return true if image successfully uploaded ,false if not.
*
*     2. @public method getImgSrc() takes no arguments.
*         @ return the src path of the uploaded image
*     3.
*
*
*
*/
class FileUploader
{
    private $newName; //save new generated name
    private $folder; //save image folder to uploads image in
    private $accessFolderFrom; //save the leve from where to access the folder eg: '../' ,'../../'
    private $ext; // save image extensions
    private $extensionSupported=array(".jpg",".gif",".png"); //image file supported
    private $image=array(); //image array
    private $uploadedImgSrc=null; //save source path of uploaded img
    const FOLDER_ITEMS="uploads/"; //directory to save image for items
    const FOLDER_JSON_DATA="JSONDATA"; //save item json DATA
    private $totalFiles=1; /** nbre de fichier */
    private $folderFullPath=null;

    //method to uploads a file to a folder
    public function upload($files,$folder,$fileKey=null)
    {
        //check folder permission
        if(!$this->checkFolder(basename($folder)."/"))
        {
            throw new Exception("Operation failed!!");
        }

        $this->folder=trim($folder,"/"); //folder to be used
        $rootUploadFolder=explode('/',$this->folder)[0];
        //$this->folder=Request::$websiteDomain.'/'.trim($this->folder,'/');

        /** get principal system folder */
        $d=dirname($_SERVER['SCRIPT_FILENAME']);
        $mainFolder='/'.explode('/',$d)[3].'/uploads/';
        //$mainFolder=Request::$websiteDomain.'/uploads/';
        if($this->multiplefiles($files))
        {
            //$this->totalFiles=count($files);
            $this->totalFiles=count($files);
        }
        else
        {
            $this->totalFiles=1;
        }

        //uploads files sent
        if($this->totalFiles<2)
        {
            /** upload d'un seul fichier */
            if(is_null($fileKey))
            {
                $allKeys=array_keys($files);
                $fileKey=$allKeys[0]; //get the file key
            }

            /** save file extension */
            $this->ext=".".pathinfo($files[$fileKey]['name'],PATHINFO_EXTENSION);

            /** rename properly file name */
            $files[$fileKey]['name']=$this->renameImg($files[$fileKey]['name']);

            $src=$this->folder.DS.$files[$fileKey]['name']; /** build src */
            $srcRef=basename($this->folder)."/".$files[$fileKey]['name']; //src ref to be returned

            if(move_uploaded_file($files[$fileKey]['tmp_name'],$src))
            {
                /** retourne le path du fichier uplodé */
                return Request::$websiteDomain.'/'.$srcRef;
            }
            else
            {
                return false;
            }

        }
        else
        {
            $srcs=array(); //srcs to be returned
            foreach($files as $key=>$fileToUpload)
            {
                /** save file extension */
                $this->ext=".".pathinfo($fileToUpload['name'],PATHINFO_EXTENSION);

                /** rename properly file name */
                $fileToUpload['name']=$this->renameImg($fileToUpload['name']);

                $src=$this->folder."/".$fileToUpload['name']; /** build src */
                $srcRef=basename($this->folder)."/".$fileToUpload['name']; /** src ref to be returned */
                if(move_uploaded_file($fileToUpload['tmp_name'],$src))
                {
                    if(is_null($fileKey))
                    {
                        $srcs[]=Request::$websiteDomain.'/'.$srcRef;
                        //$srcs[]=$mainFolder.$srcRef; //build src to add in array and return
                    }
                    else
                    {
                        return Request::$websiteDomain.'/'.$srcRef;
                    }
                }
            }//for loop end
            return $srcs; //return sources of uploaded images
        }
    }//fileUploader method end

    /** method to check if mutiple files sent */
    private function multipleFiles(array $files)
    {
        foreach($files as $key=>$val)
        {
            if(is_array($val))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public function getImageSrc() //get image uploaded source path
    {
        return $this->uploadedImgSrc; //return the img  uploaded full src path
    }//end of get image uploaded source path
    private function checkImg($imgName) //check file whether img or not
    {
        for($i=0; $i<count($this->extensionSupported); $i++)
        {
            if($this->ext==$this->extensionSupported[$i])
            {
                return true; //if extension supported used
            }
        }
        return false; //if extsension supported not used
    }//end of checking file whether img or not
    private function checkFolder($folder)//check folder existence
    {
        /*if($folder==self::FOLDER_ITEMS || $folder==self::FOLDER_SEEKER_IMAGE
         || $folder==self::FOLDER_PROVIDER_IMAGE ||$folder==self::FOLDER_JSON_DATA)
        {
           return true; //if folder correct exist
        }
        else
        {
           return false; //if folder exist not
        }
        */
        return true;
    }//end of checking folder existence
    private function renameImg($actualName) //rename image randomly
    {
        do
        {
            $this->newName=str_replace($actualName,rand().$this->ext,$actualName);
        }while($this->isNameUsed($this->newName));
        return $this->newName; //return new name
    }
    private function isNameUsed($name) //check if name generated already used or not
    {

        if(is_null($this->folderFullPath))
        {
            /** build folder fullpath */
            $this->folderFullPath=ROOT.'/'.$this->folder.'/';
        }
        $this->folder=$this->folderFullPath;

        $handle=opendir($this->folder);
        while($file=readdir($handle))
        {
            if($file==$name) //name used already
            {
                return true;
            }
        }
        return false;
    }

}
?>
