<?php


class AllMODULE extends Module
{
    private $standardModel;

    public function __construct($controller, $module, Request $request = null)
    {
        parent::__construct($controller, $module, $request);

        $this->standardModel=$this->loadModel("standardModel");

    }

    /*
    *Code de creation d'une actualités
    */
    public function AddAgents(){
        Request::checkPOSTRequiredData($this,"nom");
        Request::checkPOSTRequiredData($this,"postnom");
        Request::checkPOSTRequiredData($this,"prenom");
        Request::checkPOSTRequiredData($this,"code");
        Request::checkPOSTRequiredData($this,"telephone");
        Request::checkPOSTRequiredData($this,"fonction");
        Request::checkPOSTRequiredData($this,"grade");
        Request::checkPOSTRequiredData($this,"username");
        Request::checkPOSTRequiredData($this,"password");
        Request::checkPOSTRequiredData($this,"adresse");
      //  Request::checkPOSTRequiredData($this,"site_id");;

       
        $data['nom']                     =$_POST['nom'];
        $data['postnom']                 =$_POST['postnom'];
        $data['prenom']                  =$_POST['prenom'];
        $data["code"]                    =$_POST["code"];
        $data["telephone"]               =$_POST["telephone"];
        $data["fonction"]                =$_POST["fonction"];
        $data["grade"]                   =$_POST["grade"];
        $data["username"]                =$_POST["username"];
        $data["adresse"]                =$_POST["adresse"];
        $data["password"]                =sha1($_POST["password"]);
        $data['date_enregistrement']        =date("Y-m-d H:i:s");
        $data['site_id']                 =$_POST["site_id"];
        

        $id=$this->standardModel->enregistrer("agents",$data);
      
        if($id>0){
                $this->loadData("reponse",array("status"=>"success","datas"=>$id,"message"=>"Agent enregistré avec succes"));
        }else{
                $this->loadData("reponse",array("status"=>"failed","datas"=>$id,"message"=>"Erreur lors de l'enregistrement de l'agent"));
        }

    }
    public function AddPointag(){
        Request::checkPOSTRequiredData($this,"nom");
        Request::checkPOSTRequiredData($this,"code");
        Request::checkPOSTRequiredData($this,"site_id");
       /* Request::checkPOSTRequiredData($this,"postnom");
        Request::checkPOSTRequiredData($this,"prenom");*/

        $data["nom"]                =$_POST['nom'];
        $data['code']               =$_POST['code'];
        $data['site_id']                 =$_POST["site_id"];
        $data['date_enregistrement']        =date("Y-m-d H:i:s");

        $id=$this->standardModel->enregistrer("pointag",$data);
      
        if($id>0){
                $this->loadData("reponse",array("status"=>"success","datas"=>$id,"message"=>"pointag enregistré avec succes"));
        }else{
                $this->loadData("reponse",array("status"=>"failed","datas"=>$id,"message"=>"Erreur lors du pointag"));
        }
       
     
    }
    public function AddSites(){
        Request::checkPOSTRequiredData($this,"nom");
        Request::checkPOSTRequiredData($this,"code");
        Request::checkPOSTRequiredData($this,"telephone");
        Request::checkPOSTRequiredData($this,"adresse");
        Request::checkPOSTRequiredData($this,"heure_patrouille");
       /* Request::checkPOSTRequiredData($this,"postnom");
        Request::checkPOSTRequiredData($this,"prenom");*/

        $data["nom"]                =$_POST['nom'];
        $data['code']               =$_POST['code'];
        $data['telephone']          =$_POST["telephone"];
        $data['adresse']            =$_POST["adresse"];
        $data['heure_patrouille']            =$_POST["heure_patrouille"];
        $data['date_enregistrement']        =date("Y-m-d H:i:s");

        $id=$this->standardModel->enregistrer("sites",$data);
      
        if($id>0){
                $this->loadData("reponse",array("status"=>"success","datas"=>$id,"message"=>"site enregistré avec succes"));
        }else{
                $this->loadData("reponse",array("status"=>"failed","datas"=>$id,"message"=>"Erreur lors du pointag"));
        }
       
     
    }
    public function AddPointagId(){
        Request::checkPOSTRequiredData($this,"tag_id");
        Request::checkPOSTRequiredData($this,"code");
       /* Request::checkPOSTRequiredData($this,"postnom");
        Request::checkPOSTRequiredData($this,"prenom");*/
        $data["tag_id"] =$_POST["tag_id"];

        $dataExist=$this->standardModel->get("pointag",array("code"=>$_POST['code']));
        if(count($dataExist)>0){
            $this->standardModel->updateData("pointag",$data,array("code"=>$_POST['code']));
            $this->loadData("reponse",array("status"=>"successs", "dataexist"=>$dataExist, "datamodifie"=>$data));
        }else{
            $this->loadData("reponse",array("status"=>"failed","message"=>"ce pointag n'existe pas"));
        } 
       
     
    }
    public function AddConnexion(){
        Request::checkPOSTRequiredData($this,"username");
        Request::checkPOSTRequiredData($this,"password");
       /* Request::checkPOSTRequiredData($this,"postnom");
        Request::checkPOSTRequiredData($this,"prenom");*/
        //$data["tag_id"] =$_POST["tag_id"];

        $dataExist=$this->standardModel->get("agents",array("username"=>$_POST['username'],"password"=>sha1($_POST['password'])));
        if(count($dataExist)>0){
            //$this->standardModel->updateData("pointag",$data,array("code"=>$_POST['code']));
            $this->loadData("reponse",array("status"=>"successs", "dataexist"=>$dataExist));
        }else{
            $this->loadData("reponse",array("status"=>"failed","message"=>"Cet agent existe pas"));
        } 
       
     
    }

    public function AddPatrouilles(){
        Request::checkPOSTRequiredData($this,"agent_id");
        Request::checkPOSTRequiredData($this,"code_patrouille");
        Request::checkPOSTRequiredData($this,"pointag_id");
        Request::checkPOSTRequiredData($this,"site_id");
      //  Request::checkPOSTRequiredData($this,"site_id");
       /* Request::checkPOSTRequiredData($this,"postnom");
        Request::checkPOSTRequiredData($this,"prenom");*/

        $data["agent_id"]                   =$_POST['agent_id'];
        $data['code_patrouille']            =$_POST['code_patrouille'];
        $data['pointag_id']                 =$_POST["pointag_id"];
        $data['site_id']                 =$_POST["site_id"];
        $data['date_enregistrement']        =date("Y-m-d");
        $data['heure_point']        =date("Y-m-d H:i:s");

       
            $dataExist=$this->standardModel->get("pointag",array("tag_id"=>$_POST['pointag_id'],"site_id"=>$_POST['site_id']));
            if(count($dataExist)>0){
                $id=$this->standardModel->enregistrer("patrouilles",$data);
                if($id>0){
                //$this->standardModel->updateData("pointag",$data,array("code"=>$_POST['code']));
                $this->loadData("reponse",array("status"=>"successs", "dataexist"=>$dataExist,"message"=>"ptrouille enregistré avec succes"));
            }else{
                $this->loadData("reponse",array("status"=>"failed","datas"=>$id,"message"=>"Erreur lors du pointag"));
             }
            }else{
                $this->loadData("reponse",array("status"=>"failed","message"=>"Ce pointag est pas lié à ce site"));
            } 
              
        
       
    }
    public function DebutPatrouilles(){
        
                $this->loadData("reponse",array("status"=>"success","code_patrouille"=>time()));
    }

    public function voirSites(){
        $viewData=$this->standardModel->get("sites",array("status_site"=>'actif'));
        $this->loadData("reponse",array("status"=>"success","sites"=>$viewData));
    }
    public function voirPointag(){
        $viewData=$this->standardModel->get("pointag",array("statut"=>'actif'));
        $this->loadData("reponse",array("status"=>"success","pointag"=>$viewData));
    }
    public function voirAgents(){
        $viewData=$this->standardModel->get("agents",array("statut_agent"=>'actif'));
        $this->loadData("reponse",array("status"=>"success","agents"=>$viewData));
    }
    public function voirPatrouilles(){
        
        
        $viewData=$this->standardModel->getPatrouilles($_POST['site_id'],$_POST['date_enre']);
        $this->loadData("reponse",array("status"=>"success","patrouilles"=>$viewData,"type"=>"allpatrouilles"));
    }
    public function recherchePatrouilles(){
        $viewData=$this->standardModel->recPatrouilles($_POST['code_patrouille']);
        $this->loadData("reponse",array("status"=>"success","patrouille"=>$viewData,"type"=>"recallpatrouilles"));
    }
    public function toutPatrouilles(){
        $viewData=$this->standardModel->allPatrouilles();
        $this->loadData("reponse",array("status"=>"success","patrouille"=>$viewData,"type"=>"rectoutpatrouilles"));
    }
    public function tousPatrouilles(){
        $viewData=$this->standardModel->allPatrouilles();
        $this->loadData("reponse",array("status"=>"success","patrouille"=>$viewData,"type"=>"rectouspatrouilles"));
    }
   




}
