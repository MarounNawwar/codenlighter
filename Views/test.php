<?php
    
    import("Database");    
    $conn = Database::getConnection();

    // $conn->execute_query("INSERT INTO auth_group VALUES (1,'Teststs')");

    // $rslt2 = $conn->insert_to("")
    // var_dump(read_config());
    // if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on" ){
    //     $headers = @get_headers($url);
    //     if($headers && strpos( $headers[0], '200')) {
    //         header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    //     }
    //     else {
    //         $status = "URL Doesn't Exist";
    //         echo $status;
    //     }
    // }
    // import("Authentication");
    // $auth = Authentication::getInstance();
    // $res = $auth->create_user("maroun","nawwar","nawwarmaroun@gmail.com","12345");
    // var_dump($res);

?>