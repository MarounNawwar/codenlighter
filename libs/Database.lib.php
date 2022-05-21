<?php

/*
*   Database Library
*/

class DataBase{

    //TODO: Implement later on from the conf file
    private static $host = "127.0.0.1";
    private static $dbName = "codenlighter";
    private static $userName = "root";
    private static $password = "";

    private static $instance;
    private $pdo;

    //private constructor
    
    private function __construct(){
        $this->pdo = $this->connect();
    }

    //Using the getInstance to obtain a Database Object
    public static function getConnection(): DataBase{
        
        $cls = static::class;
        
        if (!isset(self::$instance[$cls])) {
            self::$instance[$cls] = new static();
        }
        
        return self::$instance[$cls];
    
    }

    public function closeConnection(){
        if(isset($this->pdo)) unset($this->pdo);
        if(isset($instance)) unset($instance);
    }

    //Private function to connect to the database
    private function connect(){

        try{

            $pdo = new PDO("mysql:host=".self::$host.";dbname=".self::$dbName,self::$userName,self::$password);

            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            
            return $pdo;
        
        }catch(Exception $e){
 
            throw new Exception($e->getMessage());
        
        }

    }

    //Function to execute a query in the database
    //Returns an array of elements if the query is of type "SELECT".
    //Returns the Id of the new inserted row if the query is of type "INSERT".
    //Returns an integer that represents the number of rows affected by a query of type "DELETE".
    //Returns a boolean result that show the success/fail of a query if it is of type "UPDATE".
    private function query($query,$params = array()){
        
        //All Possible Query Types
        $query_types = array("SELECT","INSERT","DELETE","UPDATE");
        
        //Query Type of the Function
        $query_type = explode(' ',$query)[0];

        //Returns Error if query type invalid
        if(!in_array($query_type,$query_types)){ return "INVALID_QUERY"; }

        switch(strtoupper($query_type)){
            case "select":
            case "Select":
            case "SELECT": 
                
                try{
                
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    return $statement->fetchAll();
                
                }catch(Exception $e){

                    //TODO: Implement Log Error Here
                    return "DB_EXECUTION_ERROR";
                
                }

            case "insert":
            case "Insert":
            case "INSERT":
                
                try{
                    
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    return $this->pdo->lastInsertId();

                }catch(Exception $e){
                    //TODO: Implement Log Error Here
                    return "DB_EXECUTION_ERROR";
                }

            case "delete":
            case "Delete":
            case "DELETE":

                try{
                
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    return $statement->rowCount();  
                
                }catch(Exception $e){
                    //TODO: Implement Log Error Here
                    return "DB_EXECUTION_ERROR";
                }
         
            case "update":
            case "Update":
            case "UPDATE":

                try{
                
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    return $statement->rowCount() > 0;
            
                }catch(Exception $e){           
                    //TODO: Implement Log Error Here
                    return "DB_EXECUTION_ERROR";
                }
            default: 
                //TODO: Implement Log Error Here    
                return null;
        }        
    }

    // Function to get rows from database
    // @param 1: Table Name in database
    // @param 2: Parameters wanted (Not mandatory)
    // @Returns: 2-dimensional array containing fetched rows
    public function get_table_rows_from_db($table,$parameters = ["*"]){

        $cnt = 0;
        $query = "SELECT ";
        $parameters = (count($parameters) == 0) ? ["*"] : $parameters;
        $paramsCtr = count($parameters);

        if(gettype($parameters) !== "array" || gettype($table) !== "string"){ return "BAD_PARAMETERS";}
        
        //Table name cleansing and remove of special characters
        $table = preg_replace('/[\?\@\?\/\.\;\=" "]+/', '', $table);
        
        //Parameters cleansing and remove of special characters
        foreach($parameters as &$param){
            $param = preg_replace('/[\?\@\?\/\.\;\=" "]+/', '', $param);
            $query .= $param;
            $query .= (++$cnt !== $paramsCtr)?", ":" FROM ";
        }
        $query .= $table;
        return $this->query($query);

    }

    // Function to insert to database
    // @param 1: Query that is targetted 
    // @param 2: Values expected
    public function insert_on($query,$values = []){

        if( gettype($query) !== "string" || gettype($values) !== "array"){ return "BAD_PARAMETERS"; }

        //INSERT INTO `test_table`(`ID`, `name`) VALUES ([value-1],[value-2])
        //INSERT INTO [a-zA-z_]\([a-zA-Z_,]*\) VALUES \([a-zA-Z_,?]*\)
        
        

        // $statement = preg_replace('/\w+/','',$query);

        

    }

    //Function that returns true if there at least 1 valid row for a given query of type "SELECT"
    public function hasValidResults($query){

        if(explode(' ',$query[0]=='SELECT') && count($this->query($query,[]))!=0)
            return true;
        return false;

    }

    //Function that returns true if there is at least 1 valid row for a given query of type "SELECT"
    public function FindValidResults($query,$params){

        if(explode(' ',$query[0]=='SELECT') && count($this->query($query,$params))!=0)
            return true;
        return false;

    }

    public function countResult($query){    
        
        $result = $this->pdo->query($query);
        $ctr = $result->fetchColumn();             
        
        return $ctr;       

    }

}