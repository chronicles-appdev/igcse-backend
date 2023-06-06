<?php
    /**
     * Database Connection
     */

  class DbConnect {
         private $server = 'localhost';
        
         
             private $dbname = 'lawebpor_api';
             private $user = 'lawebpor_chronicl';
             private $pass = 'LearnAfrica@2023';
            //  private $user = 'root';
            //  private $pass = '';
        
         public function connect(){
             try{
                 $conn = new PDO('mysql:host='. $this->server .';dbname=' .$this->dbname, $this->user, $this->pass);
                 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                 return $conn;

             } catch (\Exception $e){
                 echo "Database Error: " . $e->getMessage();
             }
         }

     }



?>