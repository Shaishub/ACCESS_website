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


function fetchItemList($conn, $tagged) {
    $sql = "{CALL AdminTickets_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Output HTML for partial view
    ?>

    <?php
    // ... Your PHP code to connect to the database and fetch data ...

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    ?>
        
        <!-- Move table structure outside the loop -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Rental #</th>
                    <th>Renter</th>
                    <th class="text-center">Number of Items</th> 
                    <th class="text-center">Rent Duration</th>
                    <th >Status</th> 
                    <th >Options</th>  
                </tr>
            </thead>
            <tbody>

                <?php
                // Use a different variable for the inner loop
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) :
                ?>
                    <tr>
                        <td class="text-info"><?= $row['RentalID']; ?></td>
                        <td><?= $row['Fullname']; ?></td>
                        <td class="text-center"><?= $row['NumberOfItems']; ?></td>
                        <td class="text-danger text-center">
                            <?= isset($row['DateClaim']) ? $row['DateClaim']->format('Y-m-d') : ''; ?> to 
                            <?= isset($row['DateReturn']) ? $row['DateReturn']->format('Y-m-d') : ''; ?>
                        </td>
                        <td><label class="<?= $row['Status']; ?> badge"><?= $row['Status']; ?></label></td>
                        <td>
                            <div class="row" style="gap: 12px;">
                                <button type="button" class="btn btn-inverse-info btn-rounded btn-icon btnView" data-toggle="modal" data-target="#viewRentalsModal">
                                    <i class="ti-eye"></i>
                                </button>
                                <button type="button" class="btn btn-inverse-success btn-rounded btn-icon">
                                    <i class="ti-check"></i>
                                </button>
                                <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon">
                                    <i class="ti-trash"></i>
                                </button>
                            </div>
                        </td>

                      

                    </tr>
                    
                <?php endwhile; ?>

            </tbody>
        </table>
    <?php

    }   
    // ... Your PHP code to free the statement and close the connection ...

    sqlsrv_free_stmt($stmt);
}

function returnedRentals($conn, $tagged, $RentalID){

    $dateCompaleted = date('Y-m-d');
    $ConcernID = null;

    $sql = "{CALL AdminTickets_SP(?, ?, ?, ?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
        array($ConcernID, SQLSRV_PARAM_IN), 
        array($dateCompaleted, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    //Close the statement for each iteration
    sqlsrv_free_stmt($stmt);

    echo "marked as done";

}

function updateDeletedRentals($conn, $tagged, $RentalID){

    $dateCompaleted = date('Y-m-d');
    $ConcernID = null;

    $sql = "{CALL AdminTickets_SP(?, ?, null, ?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
        array($ConcernID, SQLSRV_PARAM_IN), 
        array($dateCompaleted, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    //Close the statement for each iteration
    sqlsrv_free_stmt($stmt);

    echo "deleted successfully";

}

function getCompletedRentals($conn, $tagged){
    $sql = "{CALL AdminTickets_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Output HTML for partial view
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        ?>
        
        <!-- Move table structure outside the loop -->
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Rental #</th>
                <th>Date Completed</th> 
                <th>View</th>  
                </tr>
            </thead>
            <tbody>

            <?php
            // Use a do-while loop to ensure it executes at least once
            do {
                ?>
                <tr>
                    <td class="text-info"><?= $row['RentalID']; ?></td>
                    <td>
                        <label class="<?= $row['Status']; ?> badge"><?= isset($row['DateCompleted']) ? $row['DateCompleted']->format('Y-m-d') : ''; ?></label>
                    </td>
                    <td>
                        <div class="row" style="margin: auto;">
                            <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#viewRentalsModal">
                                <i class="ti-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php
            } while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)); // Fetch next row

            ?>

            </tbody>
        </table>
    <?php

    }


    sqlsrv_free_stmt($stmt);

}

//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged     = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);
$RentalID   = isset($_GET['RentalID']) ? $_GET['RentalID'] : (isset($_POST['RentalID']) ? $_POST['RentalID'] : null);

switch ($tagged) {
    case 'viewStudentRentals':
        fetchItemList($conn, $tagged);
        break;
    case 'returnedRentals':
        returnedRentals($conn, $tagged, $RentalID);
        break;
    case 'deletedRentals':
        updateDeletedRentals($conn, $tagged, $RentalID);
        break;
    case 'getCompletedRentals':
        getCompletedRentals($conn, $tagged);
        break;


    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>