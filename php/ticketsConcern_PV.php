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

// // Debug Point 2
// echo "Before fetching item list.\r\n";

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


    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        ?>
        
        <!-- Move table structure outside the loop -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Ticket #</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>

                <?php
                // Use a different variable for the inner loop
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) :
                ?>
                    <tr>

                        <td id="ticketNumber"><?= $row['TicketNumber']; ?></td>
                        <td><?= $row['Fullname']; ?></td>
                        <td class="text-warning"><?= isset($row['DatePosted']) ? $row['DatePosted']->format('Y-m-d') : ''; ?></td>
                        <td><label class="<?= $row['Status']; ?> badge"><?= $row['Status']; ?></label></td>
                        
                        
                        <!--Buttons-->
                        <td>
                            <div class="row" style="gap: 12px; ">
                                <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#viewConcernsModal">
                                    <i class="ti-eye"></i>
                                </button>
                                <button type="button" class="btnDone btn btn-inverse-success btn-rounded btn-icon">
                                    <i class="ti-check"></i>
                                </button>
                                <button type="button" class="btnDelete btn btn-inverse-danger btn-rounded btn-icon">
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


    sqlsrv_free_stmt($stmt);
}

function updateCompletedConcern($conn, $tagged, $ConcernID){

    $dateCompaleted = date('Y-m-d');
    $RentalID = null;

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

    echo "successful";

}

function updateDeletedConcern($conn, $tagged, $ConcernID){

    $dateCompaleted = date('Y-m-d');
    $RentalID = null;

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

    echo "successful";

}

function getSolvedConcerns($conn, $tagged){
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
                <th>Concern #</th>
                <th>Date Completed</th> 
                <th>View</th>  
                </tr>
            </thead>
            <tbody>

                <?php
                // Use a different variable for the inner loop
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) :
                ?>
                    <tr>
                        <td class="text-info"><?= $row['TicketNumber']; ?></td>
                        <td><label class="<?= $row['Status']; ?> badge"><?= isset($row['DateCompleted']) ? $row['DateCompleted']->format('Y-m-d') : ''; ?></label></td>
                        <td>
                            <div class="row" style="margin: auto;">
                                <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#completedConcernsModal">
                                    <i class="ti-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                <?php endwhile; ?>

            </tbody>
        </table>
    <?php

    }


    sqlsrv_free_stmt($stmt);

}

//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);
$ConcernID = isset($_GET['ConcernID']) ? $_GET['ConcernID'] : (isset($_POST['ConcernID']) ? $_POST['ConcernID'] : null);

switch ($tagged) {
    case 'viewStudentConcerns':
        fetchItemList($conn, $tagged);
        break;
    case 'solvedConcerns':
        updateCompletedConcern($conn, $tagged, $ConcernID);
        break;
    case 'deletedConcerns':
        updateDeletedConcern($conn, $tagged, $ConcernID);
        break;
    case 'getSolvedConcerns':
        getSolvedConcerns($conn, $tagged);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>