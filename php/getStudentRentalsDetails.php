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


function fetchItemList1($conn, $tagged, $RentalID) {
    $sql = "{CALL AdminTickets_SP(?,?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Output HTML for partial view
    ?>
        <?php
        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            <div class="card-body">
                <!-- <h4 class="card-title">Input size</h4> -->
                <div class="form-group">
                    <label>Fullname</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['FullName']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Student Number</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['StudentNumber']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Student Email</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['StudentEmail']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Student Course</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['StudentCourse']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Year & Section</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['YearLevel']; ?> - <?= $row['Section']; ?>" readonly>
                </div>

            </div>
            <?php
        }
        ?>

    <?php
        
    sqlsrv_free_stmt($stmt);
}

function fetchItemList2($conn, $tagged, $RentalID) {
    $sql = "{CALL AdminTickets_SP(?,?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Output HTML for partial view
    ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ItemNumber</th>
                <th>ItemName</th>
                <th class="text-center">Range of Use</th> 
                <th class="text-center">Status</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                ?>
                <tr>
                    <td class="text-info"><?= $row['ItemNumber']; ?></td>
                    <td><?= $row['ItemName']; ?></td>
                    <td class="text-danger text-center">
                        <?= isset($row['DateClaim']) ? $row['DateClaim']->format('Y-m-d') : ''; ?> to 
                        <?= isset($row['DateReturn']) ? $row['DateReturn']->format('Y-m-d') : ''; ?>
                    </td>
                    <td class="text-center"><label class="<?= $row['Status']; ?> badge"><?= $row['Status']; ?></label></td>
                    <td style="display:none;"><?= $row['RentalID']; ?></td>
                    </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
        
    <?php
        
    sqlsrv_free_stmt($stmt);
}


    

//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
$RentalID = isset($_GET['RentalID']) ? $_GET['RentalID'] : null;

switch ($tagged) {
    case 'getStudentRentalsDetails':
        fetchItemList1($conn, $tagged, $RentalID);
        fetchItemList2($conn, $tagged, $RentalID);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>