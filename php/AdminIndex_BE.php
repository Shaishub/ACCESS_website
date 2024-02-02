<?php

function connectToDatabase() {
    $serverName = "SHAINNA-ACER";
    $database = "ACCESS_Library";

    $conn = sqlsrv_connect($serverName, array("Database" => $database));

    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    return $conn;
}


function closeDatabaseConnection($conn) {
    sqlsrv_close($conn);
}

function OngoingRentalsCount($conn){

    $Tagged	= isset($_GET['tagged']) ? $_GET['tagged'] : null;

    $sql = "{CALL AdminAccount_SP(?, null, null, null, null, null, null, ?)}";
    $params = array(
        array($Tagged           , SQLSRV_PARAM_IN),
        array($Username		    , SQLSRV_PARAM_IN),
        // array($Email		    , SQLSRV_PARAM_IN),
        // array($Password		    , SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {

        

    }

    sqlsrv_free_stmt($stmt);

}






//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);

switch ($tagged) {
    case 'OngoingRentalsCount':
        OngoingRentalsCount($conn);
        break;
    case 'checkUserInfo':
        checkUserInfo($conn);
        break;
    case 'loggingUser':
        loggingUser($conn);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>





