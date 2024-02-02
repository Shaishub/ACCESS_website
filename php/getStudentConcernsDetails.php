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


function fetchItemList1($conn, $tagged, $ConcernID) {
    $sql = "{CALL AdminTickets_SP(?, null, ?, null)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($ConcernID, SQLSRV_PARAM_IN),
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
            <div class="card-body">
                <!-- <h4 class="card-title">Input size</h4> -->
                <div class="form-group">
                    <label>Fullname</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['Fullname']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Student Email</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['Email']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Student Course</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['Course']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Year & Section</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['YearLevel']; ?> - <?= $row['Section']; ?>" readonly>
                </div>

            </div>

            <div class="card-body" style="border-top:1px solid black;">
                <!-- <h4 class="card-title">Input size</h4> -->
                <div class="form-group">
                    <label>Concern Category</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['ConcernCategory']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Concern Title</label>
                    <input type="text" class="form-control form-control-sm" value="<?= $row['ConcernTitle']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="exampleTextarea1">Concern</label>
                    <textarea class="form-control" id="exampleTextarea1" rows="4" style="resize: vertical;height: 200px;text-align:justify;" readonly>
                      <?= $row['ConcernBody']; ?>
                    </textarea>
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
$ConcernID = isset($_GET['ConcernID']) ? $_GET['ConcernID'] : null;

switch ($tagged) {
    case 'getStudentConcernsDetails':
        fetchItemList1($conn, $tagged, $ConcernID);
        //fetchItemList2($conn, $tagged, $RentalID);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>