<?php

/**
 * Class StandardModel
 * Model standard pour éxécuter des requetes standard d'une table à une autre
 */

class StandardModel extends Model
{
    function __construct($language=null)
    {
        parent::__construct($language);

        /**EN: array containing details about the database to connect */
        /**FR: array containant les details de la base de donnée  à connecter */
        $databaseConfiguration=array('host'=>'localhost','username'=>'root','password'=>'','database'=>'patroltag');

        if(strstr(Request::$websiteDomain,".net"))
        {
            /** en ligne */
            $databaseConfiguration=array("host"=>"localhost","username"=>"fiscosar_developpeur","password"=>"",
                "database"=>"");
        }

        /** sending of the details for configuration */
        /** Envoie des details de la configuration  */
        $this->setConfiguration($databaseConfiguration);

        /**EN: Connection to the Database */
        /**FR: Connexion à la base de donnée */
        $this->connect();
    }

    /**
     * Method pour récuperer les données d'une table
     * @param $table => la table de donnée
     * @param array $whereData => array contenant les données pour where clause
     * @param array $cols > les colonnes à selectionner
     */
    public function get($table,array $whereData=array(),$cols=array())
    {
        $this->setTable($table);

        $keys=array_keys($whereData);
        if(isset($keys[0]))
        {
            $this->where($keys[0],"=",$whereData[$keys[0]]);

            if(isset($keys[1]))
            {
                $this->where($keys[0],"=",$whereData[$keys[0]],"AND",$keys[1],"=",$whereData[$keys[1]]);
            }
        }

        return $this->select($cols);
    }

    /**
     * Method pour enregistrer les données dans une table
     * @param $table => la table de donnée
     * @param array $data => les données
     * @return array|bool|int|string => la cle primaire ID de la donnée inserée
     * @throws Exception
     */
    public function enregistrer($table,array $data)
    {
        $this->setTable($table);
        return $this->insert($data);
    }
  
    /**
     * Method pour mettre à jour une données dans une table
     * @param $table => la table
     * @param array $data => les données à mettre à  jour
     * @param array $whereData => array contenant les details where clause
     * @return array|bool|int|string
     */
    public function updateData($table,array $data,array $whereData=array())
    {
        $this->setTable($table);

        $keys=array_keys($whereData);
        if(isset($keys[0]))
        {
            $this->where($keys[0],"=",$whereData[$keys[0]]);

            if(isset($keys[1]))
            {
                $this->where($keys[0],"=",$whereData[$keys[0]],"AND",$keys[1],"=",$whereData[$keys[1]]);
            }
        }

        return $this->update($data);
    }
    /**
     * Methode pour recuperer le total d'une table
     */

    public function getPatrouilles($site_id,$date_enre){
        $sql="SELECT patrouilles.agent_id as agent_id, agents.agent_id as agent_id_a,agents.nom as nom_agent,agents.postnom as postnom_agent,agents.prenom as prenom_agent,
                     patrouilles.code_patrouille as code_patrouille, patrouilles.pointag_id as pointag, pointag.nom as nompointag, patrouilles.site_id as site_id,
                      sites.nom as nomsite, patrouilles.heure_point as heure_point, patrouilles.date_enregistrement as date_enregistrement
                     FROM patrouilles INNER JOIN agents ON 
                     patrouilles.agent_id=agents.agent_id INNER JOIN pointag ON patrouilles.pointag_id=pointag.tag_id INNER JOIN sites ON patrouilles.site_id=sites.site_id
                     WHERE  patrouilles.date_enregistrement like '".$date_enre."' AND patrouilles.site_id=".$site_id."    group by code_patrouille ";
        $data=$this->execute($sql);
        return $data;
    }
    public function recPatrouilles($code_patr){
        $sql="SELECT patrouilles.agent_id as agent_id, agents.agent_id as agent_id_a,agents.nom as nom_agent,agents.postnom as postnom_agent,agents.prenom as prenom_agent,
                     patrouilles.code_patrouille as code_patrouille, patrouilles.pointag_id as pointag, pointag.nom as nompointag, patrouilles.site_id as site_id,
                      sites.nom as nomsite, patrouilles.heure_point as heure_point, patrouilles.date_enregistrement as date_enregistrement
                     FROM patrouilles INNER JOIN agents ON 
                     patrouilles.agent_id=agents.agent_id INNER JOIN pointag ON patrouilles.pointag_id=pointag.tag_id INNER JOIN sites ON patrouilles.site_id=sites.site_id
                     WHERE patrouilles.code_patrouille=".$code_patr."";
        $data=$this->execute($sql);
        return $data;
    }
    public function allPatrouilles(){
        $sql="SELECT patrouilles.agent_id as agent_id, agents.agent_id as agent_id_a,agents.nom as nom_agent,agents.postnom as postnom_agent,agents.prenom as prenom_agent,
                     patrouilles.code_patrouille as code_patrouille, patrouilles.pointag_id as pointag, pointag.nom as nompointag, patrouilles.site_id as site_id,
                      sites.nom as nomsite, patrouilles.heure_point as heure_point, patrouilles.date_enregistrement as date_enregistrement
                     FROM patrouilles INNER JOIN agents ON 
                     patrouilles.agent_id=agents.agent_id INNER JOIN pointag ON patrouilles.pointag_id=pointag.tag_id INNER JOIN sites ON patrouilles.site_id=sites.site_id
                       order by patrouilles.patrouille_id asc ";
        $data=$this->execute($sql);
        return $data;
    }

}
