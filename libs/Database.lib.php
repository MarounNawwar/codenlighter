<?php

/*
*   Database Library
*/

import("Log");

class DataBase{

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

        $host = get_config_param("hostname");
        log_write("Retrieving hostname configuration from config file");
        $dbName = get_config_param("DBName");
        log_write("Retrieving DBName configuration from config file");
        $userName = get_config_param("DBUsername");
        log_write("Retrieving DBUsername configuration from config file");
        $password = get_config_param("DBPassword");
        log_write("Retrieving DBPassword configuration from config file");

        try{

            log_write("Connecting to Database...");
            $pdo = new PDO("mysql:host=".$host.";dbname=".$dbName,$userName,$password);

            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            
            return $pdo;
        
        }catch(Exception $e){
 
            $errorMsgDetail = "ERROR: ".$e->getMessage();
            log_error("Connection to Database failed.");
            log_error($errorMsgDetail,2);
            return null;
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
                    if($rslt){
                        log_write("");
                    }else{

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
    public function get_table_rows_from_db($table,$parameters = ["*"]){

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
 
        // print "Values number = ".sizeof($values)."<br/>";
        // print "Parameters Counter = ".$parameters_ctr."<br/>";
        // print "Brackets Counter = ".$brackets_ctr."<br/>";
        // print "Ticks Counter = ".$ticks_ctr."<br/>";
        
        return (sizeof($values) == $parameters_ctr) &&($brackets_ctr%2 == 0)&& ($ticks_ctr%2 == 0);
 
    } 

    // Function to insert to database
    // @param 1: Query that is targetted 
    // @param 2: Values expected
    public function insert_on($query,$values = []){

        //This Regular expression allows to make sure that the query has a valid structure;
        $insert_pattern_1 = "/INSERT INTO[ ]*[`]*[a-zA-z0-9_]*[`]*[ ]*\([`a-zA-Z0-9_, ]+\)[ ]*VALUES[ ]*\([a-zA-Z0-9_,?'\"` ]+\)[;]?/i";

        //TODO: We still have to create a regex that is compatible with insert query that contains select built in query
        $insert_pattern_2 = "/[NOT IMPLEMENTED YET]/";


        if( gettype($query) !== "string" || gettype($values) !== "array"){ return "BAD_PARAMETERS"; }

        if(preg_match($insert_pattern_1,$query) || preg_match($insert_pattern_2,$query)){
            
            //TODO: Filter the parameters passed using regex
            //TODO: Create an if branch for the mysql only and then execute



        }

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