
            <?php
            Class ExempleModel extends Model
            {
                function __construct($language=null)
                {
                    parent::__construct($language);
                    
                    
                    /**EN: array containing details about the database to connect */
                    /**FR: array containant les details de la base de donnée  à connecter */
                    $databaseConfiguration=array('host'=>'localhost','username'=>'root','password'=>'','database'=>'exemple_database');
                    
                    /** sending of the details for configuration */
                    /** Envoie des details de la configuration  */
                    $this->setConfiguration($databaseConfiguration); 
                    
                    /**EN: Connection to the Database */
                    /**FR: Connexion à la base de donnée */
                    $this->connect();
                }
            }
            ?>