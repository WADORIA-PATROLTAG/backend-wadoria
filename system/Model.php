<?php

/**
 * Created by PhpStorm.
 * User: Chris Tenday
 * Date: 25/09/2018
 * Time: 11:12
 */
class Model extends DBConf
{
    private $conn; /** connection object */
    private $table; /** table name */
    private $where=null;
    private $limit=null;
    private $orderBy=null;

    private $tableJoin=null;
    private $joinSelect=null;
    private $joinTables=array();
    private $joinOn=null;
    private $whereJoin=null;

    protected function __construct($language=null)
    {
        /** set sql language to use */
        if(is_null($language))
        {
            $this->useLanguage("Mysqli");
        }
        else
        {
            $this->useLanguage($language);
        }
    }

    /** method to do database connection */
    protected function connect()
    {
        if($this->checkLanguage()=="Mysqli")
        {
            /** connect the database in Mysqli */
            if(count($this->conf))
            {
                $this->conn=mysqli_connect($this->conf['host'],$this->conf['username'],
                    $this->conf['password'],$this->conf['database']);
                /** encodage character */

                mysqli_query($this->conn,"SET NAMES 'utf8'");
                /*mysqli_query($this->conn,"SET character_set_client = utf8;");
                mysqli_query($this->conn,"SET character_set_results = utf8;");
                mysqli_query($this->conn,"SET character_set_connection = utf8");*/

            }
            else
            {
                throw new Exception("No database configuration specified");
            }
        }
        else
        {
            /** connect the database in PDO */
        }
    }
    /** connect() method - end */

    /**
     * method for setting the table
     * @param $tableName
     */
    protected function setTable($tableName)
    {
        $this->table=$tableName;
    }

    /**
     * method for executing a sql query
     * @param $query
     * @param null $return
     * @return array|bool|int|string
     * @throws Exception
     */
    protected function execute($query)
    {
        /** determine what to expect in return after sql execution */
        $returnOption=array("SELECT"=>'data',"INSERT"=>'lastRowId',"UPDATE"=>'bool',
            "DELETE"=>'bool');
        $return=null;
        foreach($returnOption as $option=>$val)
        {
            if(strstr(strtolower($query),strtolower($option)))
            {
                $return=$val;
                break;
            }
        }
        if(is_null($return))
        {
            throw new Exception("Your query:\"$query\"  is not or couldn't be supported!!");
        }

        if($this->checkLanguage()=="Mysqli")
        {
            /**  execute the query in Mysqli language */
            $result=mysqli_query($this->conn,$query);
            if(!$result)
            {
                //throw new Exception("An Error occured while executing the query.");
                 echo mysqli_error($this->conn); exit();
            }
            if(!is_null($return))
            {
                /** manage what does this method return */
                switch($return)
                {
                    case "data"      : $data=array();
                        while($rows=mysqli_fetch_assoc($result))
                        {
                            foreach($rows as $key=>$val)
                            {
                                $rows[$key]=$this->convertAccentData($rows[$key]);
                            }
                            $data[]=$rows;
                        }
                        return $data;
                        break;
                    case "lastRowId" : $lastRowId=mysqli_insert_id($this->conn);
                        return $lastRowId;
                        break;
                    case "bool"      : if($result){return true;}else{return false;}
                        break;
                    default          : throw new Exception("Model:execute() method
                                      cannot return $return");
                }
            }
        }
        else
        {
            /** execute the query in PDO. */
        }
    }

    /** method to insert data */
    protected function insert($data)
    {
        if(!is_array($data))
        {
            throw new Exception("Model:insert() method receives only an array 
           as parameter.");
        }
        $query=" INSERT INTO $this->table";
        $columns=" ";
        $values=" ";
        $call=1;
        foreach($data as $key=>$val)
        {
            $columns.=$key;
            if(is_string($val))
            {
                $values.="'$val'";
            }
            else
            {
                $values.=$val;
            }

            if($call<count($data))
            {
                $columns.=',';
                $values.=',';
            }
            $call++;
        }
        $query.="(".$columns.")";
        $query.=" VALUES(".$values.")";
        $query.=";";
        return $this->execute($query);
        //debug_print_backtrace();
    }
    /** insert() method - end */

    /** method to select data */
    public function select($cols=array())
    {
        if(is_null($cols) || count($cols)<1)
        {
            $query="SELECT * FROM ".$this->table;
        }
        else
        {
            $cols=implode(",",$cols);
            $query="SELECT ".$cols." FROM ".$this->table;
        }
        if(!is_null($this->where))
        {
            $query.=" ".$this->where;
        }
        if(!is_null($this->orderBy))
        {
            $query.=" ".$this->orderBy;
        }
        if(!is_null($this->limit))
        {
            $query.=" ".$this->limit;
        }
        $query.=";";
        return $this->execute($query);
    }
    /** select() method - end */

    /**
     * Method pour selection les données d'une table à joindre
     * @param $table =>la table
     * @param array $cols => les colonnes à selectionner
     * @return Model
     */
    public function selectJoin($table,array $cols)
    {
        $sql="";
        for($i=0; $i<count($cols); $i++)
        {
            $sql=$sql.$table.".".$cols[$i]." as ".$cols[$i];

            if($i<count($cols)-1)
            {
                $sql=$sql.",";
            }
        }
        $s="";
        if(!is_null($this->joinSelect))
        {
            $s=",";
        }
        $this->joinSelect=$this->joinSelect.$s.$sql;

        $this->joinTables[]=$table; /** ajout de cette dans l'ensemble des tables à joindre */
        return $this;
    }

    /**
     * Method pour joindre 2 tables
     * @param $table_1
     * @param $table_2
     * @param $onCol => colonne à joindre
     * @return Model
     * @throws Exception
     */
    public function join($table_1,$table_2,$onCol)
    {
        if(!in_array($table_1,$this->joinTables))
        {
            throw new Exception("Join Table 1 invalid.");
        }
        else if(!in_array($table_2,$this->joinTables))
        {
            throw new Exception("Join Table 2 invalid.");
        }
        else{}

        $sql="INNER JOIN ".$table_2." ON ".$table_2.".".$onCol."=".$table_1.".".$onCol;

        $this->joinOn=$this->joinOn." ".$sql;

        return $this;
    }

    /**
     * Method pour définir une clause where dans une requete join
     * @param $table
     * @param $col1
     * @param $logicOperator1
     * @param $val1
     * @param string $operator
     * @param string $col2
     * @param string $logicOperator2
     * @param string $val2
     * @return $this
     */
    public function joinWhere($table,$col1,$logicOperator1,$val1,$operator="",$col2="",$logicOperator2="",$val2="")
    {
        if(!is_null($this->whereJoin))
        {
            $this->whereJoin.=" AND";
        }
        if(gettype($val1)=="string")
        {
            if(strtoupper($logicOperator1)=="LIKE")
            {
                $val1="%".$val1."%";
            }
            $this->whereJoin=$this->whereJoin." ".$table.".".$col1." ".$logicOperator1." '$val1' ";
        }
        else
        {
            $this->whereJoin=$this->whereJoin." ".$table.".".$col1." ".$logicOperator1." ".$val1." ";
        }
        if($operator!="" && $col2!="" && $logicOperator2!="" && $val2!="")
        {
            if(gettype($val2)=="string")
            {
                if(strtoupper($logicOperator2)=="LIKE")
                {
                    $val2="%".$val2."%";
                }
                $this->whereJoin.=$operator." ".$table.".".$col2." ".$logicOperator2." '$val2' ";
            }
            else
            {
                $this->whereJoin.=$operator." ".$table.".".$col2." ".$logicOperator2." ".$val2." ";
            }
        }

        $this->whereJoin=$this->whereJoin;

        return $this;
    }

    /**
     * Method pour éxécuter une requete join
     * @return array|bool|int|string
     * @throws Exception
     */
    public function executeJoin()
    {
        if(!$this->joinTables || is_null($this->joinSelect) || is_null($this->joinOn))
        {
            throw new Exception("Requete join invalid.");
        }
        $where="";
        if(!is_null($this->whereJoin))
        {
            $where=" WHERE ".$this->whereJoin;
        }

        $sql="SELECT ".$this->joinSelect." FROM ".$this->joinTables[0].$this->joinOn.$where;
        $data=$this->execute($sql);

        /**
         * Reinitialiser.
         */
        $this->joinSelect=null;
        $this->joinOn=null;
        $this->joinTables=null;
        $this->whereJoin=null;

        return $data;
    }

    /** method to set where clause */
    public function where($col1,$logicOperator1,$val1,$operator="",$col2="",
                          $logicOperator2="",$val2="")
    {
        if(gettype($val1)=="string")
        {
            if(strtoupper($logicOperator1)=="LIKE")
            {
                $val1="%".$val1."%";
            }
            $this->where="WHERE ".$col1." ".$logicOperator1." '$val1' ";
        }
        else
        {
            $this->where="WHERE ".$col1." ".$logicOperator1." ".$val1." ";
        }
        if($operator!="" && $col2!="" && $logicOperator2!="" && $val2!="")
        {
            if(gettype($val2)=="string")
            {
                if(strtoupper($logicOperator2)=="LIKE")
                {
                    $val2="%".$val2."%";
                }
                $this->where.=$operator." ".$col2." ".$logicOperator2." '$val2' ";
            }
            else
            {
                $this->where.=$operator." ".$col2." ".$logicOperator2." ".$val2." ";
            }
        }
    }
    /** method where() -end */

    public function setOrderBy($col,$order)
    {
        $this->orderBy="ORDER BY ".$col." ".$order;
    }

    public function setLimit($limit)
    {
        $this->limit="LIMIT ".$limit;
    }

    /** method to update data */
    public function update($columnsValue)
    {
        //build query
        if($this->table==null) //table not select
        {
            return false;
        }
        //parse columns sent
        $toBeUpdated=null;
        $total=count($columnsValue); //total elements
        $called=1; //number of looping
        foreach($columnsValue as $column=>$value)
        {
            //echo $column=$column."="." '$value' ";
            if($total>0)
            {
                //build query based on value type
                if(gettype($value)=="string") //check if value is string
                {
                    $toBeUpdated.=$column."="." '$value' ";
                }
                else //if value other than string
                {
                    $toBeUpdated.="$column=$value ";
                }
                if($called<$total)
                {
                    $toBeUpdated.=","; //add a commas
                }
            }
            else
            {
                $toBeUpdated.=$column."=".$value." ";
            }
            $called=$called+1; //number of looping
        }
        if(!is_null($this->where))
        {
            $query="UPDATE $this->table SET $toBeUpdated $this->where";
        }
        else
        {
            $query="UPDATE $this->table SET $toBeUpdated ";
        }
        return $this->execute($query);
    }
    /** method update() - end */

    /**
    Developpeur: Lionnel nawej kayembe
    Heure: 1h44
    Date: 29/07/2021
    Conversion des caracteres speciaux
    */
    private function convertAccentData($data)
    {

        $caractereAremplacer    =array("&eacute;", "&#233;", "&#xE9;", "�;", "Ã©;",
            "&egrave;", "&#232;", "&#xE8;", "Ã¨;", "&ecirc;",
            "&#234;;", "&#xEA;", "Ãª;", "&euml;", "&#235;",
            "&#xEB;", "Ã«Ã«;", "&agrave;", "&#224;", "&#xE0;",
            "Ã;", "&aacute;", "&#225;", "&#xE1;", "&acirc;",
            "&#226;", "&#xE2;", "Ã¢;", "&auml;", "&#228;",
            "&#xE4;", "Ã¤;", "&iuml;", "&#239;", "&#xEF;",
            "Ã¯;", "&icirc;", "&#238;", "&#xEE;", "Ã®;",
            "&ugrave;", "&#249;", "&#xF9;", "Ã¹;", "&uacute;",
            "&#250;", "&#xFA;", "Ãº;", "&ucirc;", "&#251;",
            "&#xFB;", "Ã»;", "&ocirc;", "&#244;", "&#xF4;", "Ã´;", "&amp;", "#39;" );


        $caractereReplace       =array("é", "é", "é", "-", "é",
            "è", "è", "è", "è", "ê",
            "ê", "ê", "ê", "ë", "ë",
            "ë", "ë", "à", "à",
            "à", "à", "á", "á", "á",
            "â", "â", "â", "â", "ä",
            "ä", "ä", "ä", "ï", "ï",
            "ï", "ï", "î", "î", "î",
            "î", "ù", "ù", "ù", "ù",
            "ú", "ú", "ú", "ú", "û",
            "û", "û", "û", "ô", "ô",
            "ô", "ô", "'", "" );

        return $newphrase       =str_replace($caractereAremplacer, $caractereReplace, $data);
    }
}
