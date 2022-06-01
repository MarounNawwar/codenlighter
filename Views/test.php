<?php
    
    import("Database");

    $conn = Database::getConnection();

    // print_r($conn->get_table_rows_from_db("test_table",[]));

    $rslt = $conn->validate_query_content("INSERT INTO `test`('val1','val2') VALUES('','')");

    if($rslt === false){
        echo "Bad Query Structure";
    }else{
        echo "Correct Query Structure";
    }

?>