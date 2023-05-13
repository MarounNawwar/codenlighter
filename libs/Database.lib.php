<?php

/*
*   Database Library
*/

import("Log");

class DataBase{

    private $pdo;
    private static $instance;

    //private constructor
    
    private function __construct(){
        $this->pdo = $this->connect();
    }

    public function __clone() {
        throw new Exception("Database Connection cannot be cloned");
    }

    // Using the function to retrieve the driver's name in the PDO
    private function get_driver_name(){
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    //Using the getInstance to obtain a Database Object
    public static function getConnection(): DataBase{
        
        $cls = static::class;
        
        if (!isset(self::$instance[$cls])) {
            log_write("Creating Instance of Database...");
            self::$instance[$cls] = new static();
        }
        
        return self::$instance[$cls];
    
    }

    public function closeConnection(){
        if(isset($this->pdo)) unset($this->pdo);
        if(isset($instance)) unset($instance);
        log_write("Connection of Database closed");
    }

    //Private function to connect to the database
    private function connect(){

        $db_type = get_config_param("Database");
        
        if($db_type != null && $db_type){
            log_write("Database Configuration found! Database: $db_type");
        }else{
            log_write("No Database Configuration found in conf file! Connection Aborted!");
        }

        if(strtolower($db_type) == strtolower("MySQL")){

            log_write("Retrieving hostname, DBName, DBUsername, DBPassword configurations from config file...");
            $host = get_config_param("hostname");
            $dbName = get_config_param("DBName");
            $userName = get_config_param("DBUsername");
            $password = get_config_param("DBPassword");
    
            try{
                
                log_write("Connecting to Database...");
                $pdo = new PDO("mysql:host=".$host.";dbname=".$dbName,$userName,$password);
    
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
    
                log_write("Fetching Database Connection. Connection Type: Database");
    
                return $pdo;
            
            }catch(Exception $e){
     
                $errorMsgDetail = "ERROR: ".$e->getMessage();
                log_error("Connection to Database failed.");
                log_error($errorMsgDetail,2);
                return null;
            }
 
        }elseif(strtolower($db_type) == strtolower("PostgreSQL")){

            log_write("Retrieving Hostname, DBName, DBPort, DBUsername, DBPassword configurations from config file...");
            $host = get_config_param("Hostname");
            $dbName = get_config_param("DBName");
            $port = get_config_param("DBPort","5432");
            $userName = get_config_param("DBUsername");
            $password = get_config_param("DBPassword");
    
            try{
                
                log_write("Connecting to Database...");
                $pdo = new PDO("pgsql:host=".$host.";port=$port;dbname=$dbName;",$userName,$password);
    
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
    
                log_write("Fetching Database Connection. Connection Type: Database");
    
                return $pdo;
            
            }catch(Exception $e){
     
                $errorMsgDetail = "ERROR: ".$e->getMessage();
                log_error("Connection to Database failed.");
                log_error($errorMsgDetail,2);
                return null;
            }

        }
        
        //TODO: Loop through each possible database 
        return;

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
        $query_log_msg = (count($params) != 0) ? "Executing query: `$query`. Params: (".join(", ",$params).")":"Executing query: `$query`."; 
        log_write($query_log_msg,2);
        switch(strtoupper($query_type)){
            case "select":
            case "Select":
            case "SELECT": 
                try{
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    $rslt = $statement->fetchAll();
                    log_write("Query fetch result: ".count($rslt),3);
                    return $rslt;
                }catch(Exception $e){
                    $errorMsgDetail = "ERROR: ".$e->getMessage();
                    log_error("SELECT Query Failed....");
                    log_error($errorMsgDetail,2);
                    return "DB_EXECUTION_ERROR";
                }
            case "insert":
            case "Insert":
            case "INSERT":
                try{
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    $rslt = $this->pdo->lastInsertId();
                    log_write("New inserted row id: $rslt");
                    return $rslt;
                }catch(Exception $e){
                    $errorMsgDetail = "ERROR: ".$e->getMessage();
                    log_error("INSERT Query Failed....");
                    log_error($errorMsgDetail,2);
                    return "DB_EXECUTION_ERROR";
                }
            case "delete":
            case "Delete":
            case "DELETE":
                try{               
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    $rslt = $statement->rowCount();
                    log_write("Number of rows affected from delete query: $rslt");
                    return $rslt;                
                }catch(Exception $e){
                    $errorMsgDetail = "ERROR: ".$e->getMessage();
                    log_error("DELETE Query Failed....");
                    log_error($errorMsgDetail,2);
                    return "DB_EXECUTION_ERROR";
                }
            case "update":
            case "Update":
            case "UPDATE":
                try{
                    $statement = $this->pdo->prepare($query);
                    $statement->execute($params);
                    $rslt = $statement->rowCount() > 0;
                    if($rslt && $rslt != "0E0"){
                        log_write("$rslt records were Updated!");
                    }else if($rslt == "0E0"){
                        log_write("No Matches were found! 0 records were updated!");
                    }else{
                        log_write("Update Failed... An Error Occured");
                    }
                    return $rslt;
                }catch(Exception $e){           
                    $errorMsgDetail = "ERROR: ".$e->getMessage();
                    log_error("UPDATE Query Failed....");
                    log_error($errorMsgDetail,2);
                    return "DB_EXECUTION_ERROR";
                }
            default:
                log_error("Query Failed.... couldn't find matching type of query for `$query`");
                return null;
        }        
    }

    // Function to get rows from database
    // @param 1: Table Name in database
    // @param 2: Parameters wanted (Not mandatory)
    // @Returns: 2-dimensional array containing fetched rows
    public function get_rows_from_db($table,$parameters = ["*"]){

        $cnt = 0;
        $query = "SELECT ";
        $parameters = (count($parameters) == 0) ? ["*"] : $parameters;
        $paramsCtr = count($parameters);

        log_write("Getting rows from table $table.");

        if(gettype($parameters) !== "array" || gettype($table) !== "string"){ 
            log_error("Bad Parameters sent to get_tables_rows_from_db function");
            log_error("ERROR: BAD PARAMETERS. Params Sent: $parameters",2);
            return "BAD_PARAMETERS";
        }
        
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

    public function validate_query_content($query,$values = []){ 
 
        $inside_brackets = $arguments_ctr = $parameters_ctr = $brackets_ctr = $inside_argument = $ticks_ctr = 0;
 
        //TODO: Count the number of argument and compare to the number of values (arguments > 1) 
        //TODO: Count the number of () and ` and check if they're correct 
        for($i = 0; $i < strlen($query); $i++){ 
             
            if($query[$i] == "(" && ($inside_brackets || ($ticks_ctr)%2 != 0)){

                // print "Inside Brackets: ".$inside_brackets."<br/>";
                // print "Ticks Counter: ".$ticks_ctr."<br/>";
                // print "RETURN 1: On index ".$i." At character ".$query[$i]."<br/>";
                return false;

            }elseif($query[$i] == "(" && !$inside_brackets){

                $inside_brackets = 1; 
                $brackets_ctr++;
 
            }elseif($query[$i] == ")" && (!$inside_brackets || !(($ticks_ctr)%2 == 0))){

                // print "Inside Brackets: ".$inside_brackets."<br/>";
                // print "Ticks Counter: ".$ticks_ctr."<br/>";
                // print "RETURN 2: On index ".$i." At character ".$query[$i]."<br/>";
                return false;

            }elseif($query[$i] == ")" && $inside_brackets && (($ticks_ctr)%2 == 0)){

                $inside_brackets = 0;
                $brackets_ctr++; 
 
            }elseif($query[$i] == "`" || $query[$i] == "\"" || $query[$i] == "'"){  
 
                $ticks_ctr++;  
                if((($ticks_ctr%2) != 0)&&($inside_brackets)){
                    $inside_argument = 1;
                } 

            }elseif(($query[$i] == "?" && $inside_brackets && (($ticks_ctr)%2 == 0)) || ($inside_brackets && ($ticks_ctr)%2 != 0  && $inside_argument)){
                if(!$inside_argument) {
                   $arguments_ctr++;
                }
                if($query[$i] == "?" && $inside_brackets && (($ticks_ctr)%2 == 0)){
                    $parameters_ctr++;
                }
            }elseif(($query[$i] == "," || $query[$i] == ")") && $inside_brackets && $inside_argument && (($ticks_ctr)%2 == 0)){
                
                $arguments_ctr++; 
                $inside_argument = 0;

            }elseif($inside_brackets && !$inside_argument && (($ticks_ctr)%2 == 0) && !is_numeric($query[$i])){
 
                // print "Inside Brackets: ".$inside_brackets."<br/>";
                // print "Inside Argument: ".$inside_argument."<br/>";
                // print "Ticks Counter: ".$ticks_ctr."<br/>";
                // print "RETURN 3: On index ".$i." At character ".$query[$i]."<br/>";
                return false;
             
            }

        }    
        
        return (sizeof($values) == $parameters_ctr) && ($brackets_ctr%2 == 0)&& ($ticks_ctr%2 == 0);
 
    } 

    // Function to insert to database
    // @param 1: Query that is targetted 
    // @param 2: Values expected
    public function insert_to($query,$params = []){
        // $db_type = "MySQL";
    }

    // Function to execute a query
    // @Param 1: Query to be executed
    // @Param 2: Params to be fetched
    public function execute_query($query,$params = []){
        
        $db_type = get_config_param("Database");

        //TODO: Add Hashing Security for confidential Params
        //TODO: Integrate the matching logging into the function
        if( gettype($query) !== "string" || gettype($params) !== "array"){ return "BAD_PARAMETERS"; }
        //This Regular expression allows to make sure that the query has a valid structure;        
        // FIXME: We still have to add to the regex the SQL Keywords in the WHERE Statement in the insert_pattern_2 variable 
        // FIXME: Make a better regex because special characters should be able to be inserted
        $insert_pattern_1 = "/INSERT INTO[ ]+[`]*[a-zA-z0-9_]*[`]*[ ]*\([`a-zA-Z0-9_, ]+\)[ ]+VALUES[ ]*\([a-zA-Z0-9_,?'\"` ]+\)[;]?/i";
        $insert_pattern_2 = "/INSERT INTO[ ]+[`]*[a-zA-z0-9_]*[`]*[ ]*\([`a-zA-Z0-9_, ]+\)[ ]*SELECT[ ]+[a-zA-Z0-9_,?'\"\s+` ]+[ ]+FROM[ ]+[`]*[a-zA-z0-9_]*[ ]*[WHERE[ ]*[a-zA-Z0-9_,?'\"\s+` ]+[ ]*[=]*[ ]*[\"|\']*[a-zA-Z0-9_,?'\"\s+`]*[ ]*]?[;]?/";
        $insert_pattern_3 = "/INSERT INTO[ ]+[`]*[a-zA-z0-9_]*[`]*[ ]*VALUES[ ]*\([a-zA-Z0-9_,?'\"` ]+\)[;]?/i";
        $insert_pattern_4 = "/INSERT INTO[ ]+[`]*[a-zA-z0-9_]*[`]*[ ]*SELECT[ ]+[a-zA-Z0-9_,?'\"\s+` ]+[ ]+FROM[ ]+[`]*[a-zA-z0-9_]*[ ]*[WHERE[ ]*[a-zA-Z0-9_,?'\"\s+` ]+[ ]*[=]*[ ]*[\"|\']*[a-zA-Z0-9_,?'\"\s+`]*[ ]*]?[;]?/";

        if(preg_match($insert_pattern_1,$query) || preg_match($insert_pattern_2,$query) 
        || preg_match($insert_pattern_3,$query) || preg_match($insert_pattern_4,$query)){
            foreach($params as $param){
                // FIXME: Filter the parameters passed using regex, make a better regex because special characters should be able to be inserted
                if (!preg_match($param,"/([A-Z0-9])*/i")){
                    return "BAD VALUES";
                }
            }          
            if(strtolower($db_type) == strtolower("MySQL")){
                return $this->query($query,$params);
            }elseif(strtolower($db_type) == strtolower("PostgreSQL")){
                return $this->query($query,$params);        
            }
        }
    }

    // Function to execute multiple queries
    // @Param 1: The Array of Queries to be executed
    // @Param 2: 2 dimensional array of the Params to be fetched
    public function execute_multiple_queries($queries = [],$params = []){
        if(gettype($queries) || gettype($params)){
            log_error("ERROR: BAD_PARAMS");
            return "BAD_PARAM";
        }elseif(count($queries) < 1){
            log_write("Function received empty query array...No modifications done on the database");
            return "BAD_PARAM";
        }elseif(count($queries) != count($params) && count($params)){
            log_error("Connection to Database failed.");
            return "CONN_ERROR";
        }
        try{
            $this->pdo->beginTransaction();
            while(count($queries)){
                $query = array_shift($queries);
                $parameters = (count($params) != 0)?array_shift($params):[];
                $this->execute_query($query,$parameters);
            }
            log_write("Queries Inserted:".json_encode($queries));
            log_write("Parameters Inserted:".json_encode($params));
        }catch(Exception $e){
            log_error("Error Occured while executing queries... Rolling Back All Executed Queries!");
            $this->pdo->rollBack();
            return "DB_EXCEPTION";
        }
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