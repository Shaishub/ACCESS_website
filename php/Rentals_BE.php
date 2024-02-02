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

function saveStudentRentals($conn, $postData) {
    // Extract and sanitize posted data
    $Tagged             = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $RentalID		    = isset($_POST['rentalID']) ? $_POST['rentalID'] : null;
    $Fullname		    = isset($_POST['fullname']) ? $_POST['fullname'] : null;
    $StudentNumber      = isset($_POST['studentNum']) ? $_POST['studentNum'] : null;
    $StudentEmail	    = isset($_POST['email']) ? $_POST['email'] : null;
    $StudentCourse	    = isset($_POST['course']) ? $_POST['course'] : null;
    $YearLevel	        = isset($_POST['yearlevel']) ? $_POST['yearlevel'] : null;
    $Section		    = isset($_POST['section']) ? $_POST['section'] : null;
    $DateClaim	        = isset($_POST['dateclaim']) ? $_POST['dateclaim'] : null;
    $DateReturn	        = isset($_POST['datereturn']) ? $_POST['datereturn'] : null;
    $DatePosted         = date('Y-m-d');

    // Handle arrays
    $itemidArray = isset($_POST['itemidArray']) ? $_POST['itemidArray'] : [];
    $itemNumberArray = isset($_POST['itemNumberArray']) ? $_POST['itemNumberArray'] : [];
    $itemNameArray = isset($_POST['itemNameArray']) ? $_POST['itemNameArray'] : [];
    $itemStatusArray = isset($_POST['itemStatusArray']) ? $_POST['itemStatusArray'] : [];

    // Iterate through the arrays and insert into the stored procedure
    for ($i = 0; $i < count($itemidArray); $i++) {
 
        $ItemID = $itemidArray[$i];
        $ItemNumber = $itemNumberArray[$i];
        $ItemName = $itemNameArray[$i];
        $ItemStatus = $itemStatusArray[$i];

        // Your logic to call the stored procedure and insert data
        $sql = "{CALL StudentRentals_SP(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}";
        $params = array(
            array($Tagged           , SQLSRV_PARAM_IN),        //1
            array($RentalID		    , SQLSRV_PARAM_IN),    //8
            array($Fullname		    , SQLSRV_PARAM_IN),        //1
            array($StudentNumber	, SQLSRV_PARAM_IN),      //2
            array($StudentEmail	    , SQLSRV_PARAM_IN),
            array($StudentCourse    , SQLSRV_PARAM_IN),        //3    
            array($YearLevel		, SQLSRV_PARAM_IN),         //4
            array($Section		    , SQLSRV_PARAM_IN),     //5
            array($DateClaim		, SQLSRV_PARAM_IN),       //6
            array($DateReturn		, SQLSRV_PARAM_IN),     //7
            array($ItemID			, SQLSRV_PARAM_IN),        //9
            array($ItemNumber		, SQLSRV_PARAM_IN),    //10
            array($ItemName		    , SQLSRV_PARAM_IN),      //11
            array($DatePosted		, SQLSRV_PARAM_IN),    //12
        );
 
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Close the statement for each iteration
        sqlsrv_free_stmt($stmt);
    }
 
    // Output a success message
    echo "Data saved successfully!";
}

// // Debug Point 5
// echo "Before closing the database connection.\r\n";

function getRentalID($conn, $tagged) {
    $sql = "{CALL StudentRentals_SP (?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Check if there are rows
    if (sqlsrv_has_rows($stmt)) {
 
        // Fetch the result
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $lastRentalID = $row['LastRentalID'];
        $lastRentalID = trim($lastRentalID);

        // Check if LastRentalID is null or empty
        if (empty($lastRentalID)) {
            $formattedRentalID = 'RENT00000';
        } else {
            // Extract numeric part and increment
            $numericPart = (int) preg_replace('/[^0-9]/', '', $lastRentalID);
            $nextNumericPart = $numericPart + 1;

            // Format the rental ID as "RENT00001"
            $formattedRentalID = null;
            $formattedRentalID = 'RENT' . str_pad($nextNumericPart, 5, '0', STR_PAD_LEFT);
        }



    } else {
        // Handle the case where there are no rows
        echo "No rows returned from the stored procedure.";
    }

    // Don't forget to free the statement
    sqlsrv_free_stmt($stmt);

    // Create an associative array to represent the JSON response
    $responseData = [
        'rentalID' => $formattedRentalID,
    ];

    // Set the Content-Type header to indicate JSON content
    header('Content-Type: application/json');
    echo json_encode($responseData);
}

// function updateRentalsStatus($conn, $tagged){

//     $Tagged = isset($_POST['tagged']) ? $_POST['tagged'] : null;
//     $RentalID = isset($_POST['RentalID']) ? $_POST['RentalID'] : null;
//     $DateRented = date('Y-m-d H:i:s');

//     $sql = "{CALL AdminTickets_SP (?, ?, null, null, null, ?)}";
//     $params = array(
//         array($Tagged, SQLSRV_PARAM_IN),
//         array($RentalID, SQLSRV_PARAM_IN),
//         array($DateRented, SQLSRV_PARAM_IN),
//     );

//     $stmt = sqlsrv_query($conn, $sql, $params);

//     if ($stmt === false) {
//         die(print_r(sqlsrv_errors(), true));
       
//     } else {
//         echo "successful save";
//     } 

//     sqlsrv_free_stmt($stmt);

// }


//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);

switch ($tagged) {
    case 'saveStudentRentals':
        saveStudentRentals($conn, $_POST);
        break;
    case 'getRentalID':
        getRentalID($conn, $tagged);
        break;

    case 'updateRentalsStatus':
        updateRentalsStatus($conn, $tagged);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

// // Debug Point 5
// echo "Before closing the database connection.\r\n";

?>





