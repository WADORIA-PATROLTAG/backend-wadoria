<?php
/*
* Lionnel nawej kayembe
* 25/09/2023
* 16h13
*/

class All extends Controller
{
    private $allModule;

    public function __construct($request, $Controller, $session)
    {
        parent::__construct($request, $Controller, $session);

        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Headers:*");
        header("Access-Control-Allow-Headers:Origin, X-Api-Key, X-Requested-With, Content-Type, Accept, Authorization");

        $this->allModule=$this->loadModule($this,"all");
    }

    public function insertion()
    {
        //echo "r"; exit();
        $option=(isset($this->request->params[0]))? $this->request->params[0] : "replace" ;
       // echo $option; exit();
        switch($option)
        {
            case "insertAgents" : $this->allModule->AddAgents(); break;
            case "insertPatrouilles"   : $this->allModule->AddPatrouilles();  break;
            case "insertPointag"    : $this->allModule->AddPointag(); break;
            case "insertPointagId"    : $this->allModule->AddPointagId(); break;
            case "insertSites": $this->allModule->AddSites(); break;
            case "insertSocietes": $this->allModule->AddSocietes(); break;
            case "insertAdmin"  : $this->allModule->AddAdmin(); break;
            case "insertTest"     : $this->allModule->AddTest(); break;
            case "insertConnexion"     : $this->allModule->AddConnexion(); break;
            case "insertDebut"     : $this->allModule->DebutPatrouilles(); break;
        }
    }

    public function delete()
    {
        $option=(isset($this->request->params[0]))? $this->request->params[0] : "replace" ;

        switch($option)
        {
            case "deleteAgents" : $this->allModule->SuppAgents(); break;
            case "deletePatrouilles"   : $this->allModule->SuppPatrouilles();  break;
            case "deletePointag"    : $this->allModule->SuppPointag(); break;
            case "deleteSites": $this->allModule->SuppSites(); break;
            case "deleteSocietes": $this->allModule->SuppSocietes(); break;
            case "deleteAdmin"  : $this->allModule->SuppAdmin(); break;
        }
    }

    public function view()
    {
        $option=(isset($this->request->params[0]))? $this->request->params[0] : "replace" ;

        switch($option)
        {
            case "viewAgents" : $this->allModule->voirAgents(); break;
            case "viewMedias"     : $this->allModule->voirMedias(); break;
            case "viewPatrouilles"   : $this->allModule->voirPatrouilles();  break;
            case "searchPatrouilles"   : $this->allModule->recherchePatrouilles();  break;
            case "viewAllPatrouilles"   : $this->allModule->toutPatrouilles();  break;
            case "viewAllsPatrouilles"   : $this->allModule->tousPatrouilles();  break;
            case "viewPointag"    : $this->allModule->voirPointag(); break;
            case "viewSites": $this->allModule->voirSites(); break;
            case "viewSitesJoue": $this->allModule->voirSitesJoue(); break;
            case "viewSocietes": $this->allModule->voirSocietes(); break;
            case "viewAdmin"  : $this->allModule->voirAdmin(); break;
        }
    }
}