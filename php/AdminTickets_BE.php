<?php

session_start();

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

// Lended Rentals (Ongoing Rentals)
function updateRentalsStatus($conn, $tagged){

    $Tagged = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $RentalID = isset($_POST['RentalID']) ? $_POST['RentalID'] : null;
    $DateRented = date('Y-m-d H:i:s');

    $sql = "{CALL AdminTickets_SP(?, ?, null, null, ?)}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
        array($DateRented, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
       
    } else {
        echo "successfuly lended the item/s";
    } 

    sqlsrv_free_stmt($stmt);

}

function viewStudentRentals($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;

    $sql = "{CALL AdminTickets_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    else { 
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
                        <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon " data-toggle="modal" data-target="#viewRentalsModal">
                            <i class="ti-eye"></i>
                        </button>
                        <!-- <button type="button" class="btnDone btn btn-inverse-success btn-rounded btn-icon">
                            <i class="ti-check"></i>
                        </button> -->
                        <button type="button" class="btnDelete btn btn-inverse-danger btn-rounded btn-icon">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </td>

              

            </tr>
            
        <?php endwhile;
    }
       
        
}

function getStudentRentalsDetails1($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $RentalID = isset($_GET['RentalID']) ? $_GET['RentalID'] : null;


    $sql = "{CALL AdminTickets_SP(?,?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

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

    }

}

function getStudentRentalsDetails2($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $RentalID = isset($_GET['RentalID']) ? $_GET['RentalID'] : null;


    $sql = "{CALL AdminTickets_SP(?,?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

        

        ?>
        <table class="table table-hover">
        <thead>
            <tr>
                <th>ItemNumber</th>
                <th>ItemName</th>
                <th class="text-center">Range of Use</th> 
                <th class="text-center">Status</th>
                <th class="text-center">Date</th>
                <!-- <th class="text-center">Date Rented</th>
                <th class="text-center">Date Returned</th> -->
                
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                $itemStatus = $row['Status'];

                if($itemStatus == "Reserved"){
                    $itemDate = isset($row['DatePosted']) ? $row['DatePosted']->format('Y-m-d') : '';
                }
                else if ($itemStatus == "Rented"){
                    $itemDate = isset($row['DateRented']) ? $row['DateRented']->format('Y-m-d') : '';
                }
                else if ($itemStatus == "Returned"){
                    $itemDate = isset($row['DateCompleted']) ? $row['DateCompleted']->format('Y-m-d') : '';
                }



                ?>
                <tr>
                    <td class="text-info"><?= $row['ItemNumber']; ?></td>
                    <td><?= $row['ItemName']; ?></td>
                    <td class="text-danger text-center">
                        <?= isset($row['DateClaim']) ? $row['DateClaim']->format('Y-m-d') : ''; ?> to 
                        <?= isset($row['DateReturn']) ? $row['DateReturn']->format('Y-m-d') : ''; ?>
                    </td>

                    <td class="text-center">
                        <div class="dropdown">
                            <button class="itemStatus btn btn-outline-success btn-sm dropdown-toggle" type="button" id="StatusDropDown" >
                                <?= $row['Status']; ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="StatusDropDown">
                                <!-- <button class="dropdown-item" type="button">Reserved</button> -->
                                <button class="dropdown-item" type="button">Rented</button>
                                <button class="dropdown-item" type="button">Returned</button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <input id="itemDateRented" class="itemDate form-control" type="date" value="<?php echo $itemDate ?>" style="padding:5px;" />
                    </td>

                    <!-- <td class="text-center"><label class="<?= $row['Status']; ?> badge"><?= $row['Status']; ?></label></td> -->
                    <td style="display:none;"><?= $row['RentalID']; ?></td>
                    </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
        
    <?php

    }
}


function getCompletedRentals($conn){
    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;

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
                            <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#completedRentalsModal">
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

}

function updateItemStatus($conn){

    $tagged = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    //$RentalID = isset($_POST['RentalID']) ? $_POST['RentalID'] : null;
    $ItemNmberList = isset($_POST['itemNumber']) ? $_POST['itemNumber'] : null;
    $ItemStatusList = isset($_POST['itemStatus']) ? $_POST['itemStatus'] : null;
    $ItemDateRentedList = isset($_POST['itemDateRentedList']) ? $_POST['itemDateRentedList']: null;
    $ItemDateReturnedList = isset($_POST['itemDateReturnedList']) ? $_POST['itemDateReturnedList']: null;
    $ItemDateList = isset($_POST['itemDate']) ? $_POST['itemDate']: null;

    $ModifiedBy = isset($_POST['User']) ? $_POST['User']: null;
    date_default_timezone_set('Asia/Manila');
    $ModifiedOn = Date('Y-m-d H:i:s');


    if($tagged == "updateItemStatus" ){

        for ($i = 0; $i < count($ItemNmberList); $i++) {

            $tagged = "updateItemStatus";
            //$RentalID = isset($_POST['RentalID']) ? $_POST['RentalID'] : null;
            $ItemNumber = $ItemNmberList[$i];
            $ItemStatus = $ItemStatusList[$i];
            $DateCompleted = $ItemDateList[$i];
            $DateRented = $ItemDateList[$i];
    
            $ModifiedBy = isset($_POST['User']) ? $_POST['User']: null;
            date_default_timezone_set('Asia/Manila');
            $ModifiedOn = Date('Y-m-d H:i:s');
    
            $sql = "{CALL AdminTickets_SP(?, null, null, ?, ?, ?, ?, ?, ? )}";
            $params = array(
                array($tagged, SQLSRV_PARAM_IN),
    
                array($DateCompleted, SQLSRV_PARAM_IN),
                array($DateRented, SQLSRV_PARAM_IN),
    
                array($ItemNumber, SQLSRV_PARAM_IN),
                array($ItemStatus, SQLSRV_PARAM_IN),
                array($ModifiedBy, SQLSRV_PARAM_IN),
                array($ModifiedOn, SQLSRV_PARAM_IN)
            );
    
            $stmt = sqlsrv_query($conn, $sql, $params);
        }

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        else{

            $ItemNmberList = null;
            $ItemStatusList = null;
            $ItemDateList = null;

            $tagged = "updateUnfinishedRentals";

            if ($tagged == "updateUnfinishedRentals") {

                $RentalID = isset($_POST['RentalID']) ? $_POST['RentalID'] : null;
        
                $sql = "{CALL AdminTickets_SP(?, ? )}";
                $params = array(
                    array($tagged, SQLSRV_PARAM_IN),
                    array($RentalID, SQLSRV_PARAM_IN),
                );
        
                $stmt = sqlsrv_query($conn, $sql, $params);
        
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
            }

            echo "Saved successfully";
        }

       
        
    }
    
}

function getCompletedRentalsDetails1($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $RentalID = isset($_GET['RentalID']) ? $_GET['RentalID'] : null;


    $sql = "{CALL AdminTickets_SP(?,?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

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

    }

    sqlsrv_free_stmt($stmt);

}

function getCompletedRentalsDetails2($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $RentalID = isset($_GET['RentalID']) ? $_GET['RentalID'] : null;


    $sql = "{CALL AdminTickets_SP(?,?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($RentalID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

        ?>
        <table class="table table-hover">
        <thead>
            <tr>
                <th>ItemNumber</th>
                <th>ItemName</th>
                <th class="text-center">Range of Use</th> 
                <th class="text-center">Status</th>
                <th class="text-center">Date</th>
                <!-- <th class="text-center">Date Rented</th>
                <th class="text-center">Date Returned</th> -->
                
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                $itemStatus = $row['Status'];

                if($itemStatus == "Reserved"){
                    $itemDate = isset($row['DatePosted']) ? $row['DatePosted']->format('Y-m-d') : '';
                }
                else if ($itemStatus == "Rented"){
                    $itemDate = isset($row['DateRented']) ? $row['DateRented']->format('Y-m-d') : '';
                }
                else if ($itemStatus == "Returned"){
                    $itemDate = isset($row['DateCompleted']) ? $row['DateCompleted']->format('Y-m-d') : '';
                }



                ?>
                <tr>
                    <td class="text-info"><?= $row['ItemNumber']; ?></td>
                    <td><?= $row['ItemName']; ?></td>
                    <td class="text-danger text-center">
                        <?= isset($row['DateClaim']) ? $row['DateClaim']->format('Y-m-d') : ''; ?> to 
                        <?= isset($row['DateReturn']) ? $row['DateReturn']->format('Y-m-d') : ''; ?>
                    </td>

                    <td class="text-center">
                        <?= $row['Status']; ?>
                    </td>
                    <td>
                        <input id="itemDateRented" class="itemDate form-control" type="date" value="<?php echo $itemDate ?>" style="padding:5px;" readonly />
                    </td>
                    <td style="display:none;"><?= $row['RentalID']; ?></td>
                    </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
        
    <?php

    }

    sqlsrv_free_stmt($stmt);
}


function getStudentConcernsDetails($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $ConcernID = isset($_GET['ConcernID']) ? $_GET['ConcernID'] : null;

    $sql = "{CALL AdminTickets_SP(?, null, ?)}";
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


//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);

switch ($tagged) {
    case 'updateRentalsStatus':
        updateRentalsStatus($conn, $tagged);
        break;
    case 'viewStudentRentals':
        viewStudentRentals($conn);
        break;
    case 'getStudentRentalsDetails':
        getStudentRentalsDetails1($conn);
        getStudentRentalsDetails2($conn);
        break;
    case 'updateItemStatus':
        updateItemStatus($conn);
        // updateUnfinishedRentals($conn);
        break;
    case 'getCompletedRentals':
        getCompletedRentals($conn);
        break;
    case 'getCompletedRentalsDetails':
        getCompletedRentalsDetails1($conn);
        getCompletedRentalsDetails2($conn);
        break;


    case 'getStudentConcernsDetails':
        getStudentConcernsDetails($conn);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>





