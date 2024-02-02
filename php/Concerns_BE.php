<?php

// // Debug Point 1
// echo "Before connecting to the database.\r\n";

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


// // Debug Point 3
// echo "Before saving student rentals.\r\n";

function saveStudentConcerns($conn, $postData) {
    // Extract and sanitize posted data
    $Tagged             = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $fullname           = isset($_POST['fullname']) ? $_POST['fullname'] : null;
    $email              = isset($_POST['email']) ? $_POST['email'] : null;
    $yearlevel          = isset($_POST['yearlevel']) ? $_POST['yearlevel'] : null;
    $section            = isset($_POST['section']) ? $_POST['section'] : null;
    $concernCategory    = isset($_POST['concernCategory']) ? $_POST['concernCategory'] : null;
    $concernTitle       = isset($_POST['concernTitle']) ? $_POST['concernTitle'] : null;
    $concernBody        = isset($_POST['concernBody']) ? $_POST['concernBody'] : null;
    $datePosted         = date('Y-m-d H:i:s');

   
    // Call the stored procedure
    $sql = "{CALL StudentConcerns_SP(?, ?, ?, ?, ?, ?, ?, ?, ?)}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($fullname, SQLSRV_PARAM_IN),
        array($email, SQLSRV_PARAM_IN),
        array($yearlevel, SQLSRV_PARAM_IN),
        array($section, SQLSRV_PARAM_IN),
        array($concernCategory, SQLSRV_PARAM_IN),
        array($concernTitle, SQLSRV_PARAM_IN),
        array($concernBody, SQLSRV_PARAM_IN),
        array($datePosted, SQLSRV_PARAM_IN)
        
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
        //echo "Encountered an error while saving";
    } else {
        echo "Your concern is sent successfully!";
    }
}





//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);

switch ($tagged) {
    case "saveStudentConcerns":
        saveStudentConcerns($conn, $_POST);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

// // Debug Point 5
// echo "Before closing the database connection.\r\n";

?>





