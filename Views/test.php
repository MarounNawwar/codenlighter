<?php
    
    import("Database");

    $conn = Database::getConnection();

    print_r($conn->get_table_rows_from_db("test_table",[]));


?>