<?php

//DATABASE CONNECTION
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

//----------------------------------------------- FUNCTIONS --------------------------------------------------------

// FOR SAVING EVENTS
function saveCreatedEvent($conn){

    $tagged         = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $eventTitle     = isset($_POST['eventTitle']) ? $_POST['eventTitle'] : null;
    $eventAuthor    = isset($_POST['eventAuthor']) ? $_POST['eventAuthor'] : null;
    $startDate      = isset($_POST['startDate']) ? $_POST['startDate'] : null;
    $endDate        = isset($_POST['endDate']) ? $_POST['endDate'] : null;

    $eventContent   = isset($_POST['eventContent']) ? $_POST['eventContent'] : null;
    $eventLink1     = isset($_POST['eventLink1']) ? $_POST['eventLink1'] : null;
    $eventLink2     = isset($_POST['eventLink2']) ? $_POST['eventLink2'] : null;
    $eventLink3     = isset($_POST['eventLink3']) ? $_POST['eventLink3'] : null;
    $eventLink4     = isset($_POST['eventLink4']) ? $_POST['eventLink4'] : null;

    $eventLink5     = isset($_POST['eventLink5']) ? $_POST['eventLink5'] : null;
    $eventPosted    = date('Y-m-d');
    $eventPhoto     = isset($_POST['eventPhoto']) ? $_POST['eventPhoto'] : null;

    // Call the stored procedure
    $sql = "{CALL AdminEvents_SP(?,?,?,?,?,?,?,?,?,?,?,?,?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($eventTitle, SQLSRV_PARAM_IN),
        array($eventAuthor, SQLSRV_PARAM_IN),
        array($startDate, SQLSRV_PARAM_IN),
        array($endDate, SQLSRV_PARAM_IN),

        array($eventContent, SQLSRV_PARAM_IN),
        array($eventLink1, SQLSRV_PARAM_IN),
        array($eventLink2, SQLSRV_PARAM_IN),
        array($eventLink3, SQLSRV_PARAM_IN),
        array($eventLink4, SQLSRV_PARAM_IN),

        array($eventLink5, SQLSRV_PARAM_IN),
        array($eventPosted, SQLSRV_PARAM_IN),
        array($eventPhoto, SQLSRV_PARAM_IN),
        //array($eventStatus, SQLSRV_PARAM_IN),


    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "successful save";
    }

}

// FOR UPDATING EVENTS
function UpdateEvents($conn){
    $tagged = isset($_POST['tagged']) ? $_POST['tagged'] : null;

    // Call the stored procedure
    $sql = "{CALL AdminEvents_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        //array($dateToday, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
        //echo "Encountered an error while saving";
    } else {
        echo "events updated";
    }
}

function getUpcomingEvents($conn){
    $tagged         = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $dateToday      = isset($_GET['dateToday']) ? $_GET['dateToday'] : null;

    // Call the stored procedure
    $sql = "{CALL AdminEvents_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        //array($dateToday, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));

    } else {

        ?>
        <tbody>
            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                <tr class="text-center">
                    <td class="eventID" style="display:none;"><?= $row['EventID']; ?></td>
                    <td class="posted" ><?= isset($row['EventDateStart']) ? $row['EventDateStart']->format('F d, Y') : ''; ?></td>
                    <td class="title" ><?= $row['EventTitle']; ?></td>
                    <!-- <td class="text-info font-weight-bold"><?= $row['EventLink1'];?></td> -->
                    <td>
                        <!-- <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#exampleModal2"> -->
                        <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#UpcomingEventDetail">
                            <i class="ti-eye"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <?php

        sqlsrv_free_stmt($stmt);
    }
}

function getOngoingEvents($conn){
    $tagged         = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $dateToday      = isset($_POST['dateToday']) ? $_POST['dateToday'] : null;

    // Call the stored procedure
    $sql = "{CALL AdminEvents_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        //array($dateToday, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));

    } else {

        ?>
        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
            <div class="col-sm-6 flex-row ongoing-event" id="ongoing_events" style="margin-right: 10px; max-width:45%">
                <div class="event-image">
                    <img src="images/events_pics/<?= $row['EventPhoto']; ?>" alt="">
                </div>
                <div class="d-flex ongoing-event-info">
                    
                    <img src="images/LOGO.png" class="rounded-circle" alt="" style="height: 4.5rem; width: 4.5rem; background-color: #fff; padding: 4px; margin: -44px 12px 0 -12px; ">
                    <div class=" d-flex flex-column">
                        
                        <h4 class="viewOngoingEvent font-weight-bold"><?= $row['EventTitle']; ?></h4>
                        <span class="text-muted">Event Head: <?= $row['EventAuthor']; ?></span>
                        <span class="text-secondary mt-2"><?= isset($row['EventDateStart']) ? $row['EventDateStart']->format('m-d-Y') : ''; ?> : <?= isset($row['EventDateEnd']) ? $row['EventDateEnd']->format('m-d-Y') : ''; ?></span>
                        <p class="eventID" style=""><?= $row['EventID']; ?></p>

                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        <?php

        sqlsrv_free_stmt($stmt);
    }
}

function countAllEvents($conn) {

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;

    //Call the stored procedure
    $sql = "{CALL AdminEvents_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {

        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

            echo $row['CountEvents'];
        }
        else {
            echo "0";
        }

        sqlsrv_free_stmt($stmt);

    }

}

function countAllFinishedEvents($conn) {

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;

    //Call the stored procedure
    $sql = "{CALL AdminEvents_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {

        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){

            echo $row['CountEvents'];
        }
        else {
            echo "0";
        }

        sqlsrv_free_stmt($stmt);

    }

}

function getUpcomingEventDetials($conn){
    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $EventID = isset($_GET['eventID']) ? $_GET['eventID'] : null;

    $eventTitle	 = null;
    $eventAuthor = null;
    $startDate   = null;
    $endDate     = null;
    $eventContent = null;
    $eventLink1  = null;
    $eventLink2  = null;
    $eventLink3  = null;
    $eventLink4  = null;
    $eventLink5  = null;
    $eventPosted = null;
    $eventPhoto  = null;
    $eventStatus = null;
    $dateToday	 = null;

    // Call the stored procedure
    $sql = "{CALL AdminEvents_SP(
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?,
        ?, ?, ?,    
        
        )}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
       
        array($eventTitle, SQLSRV_PARAM_IN),
        array($eventAuthor, SQLSRV_PARAM_IN),
        array($startDate, SQLSRV_PARAM_IN),
        array($endDate, SQLSRV_PARAM_IN),
        array($eventContent, SQLSRV_PARAM_IN),
        array($eventLink1, SQLSRV_PARAM_IN),
        array($eventLink2, SQLSRV_PARAM_IN),
        array($eventLink3, SQLSRV_PARAM_IN),
        array($eventLink4, SQLSRV_PARAM_IN),
        array($eventLink5, SQLSRV_PARAM_IN),
        array($eventPosted, SQLSRV_PARAM_IN),
        array($eventPhoto, SQLSRV_PARAM_IN),
        array($eventStatus, SQLSRV_PARAM_IN),
        array($dateToday, SQLSRV_PARAM_IN   ),

        array($EventID, SQLSRV_PARAM_IN),

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));

    } else {

        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            $eventPhoto     = $row['EventPhoto'];
            $eventTitle     = $row['EventTitle'];
            $eventBody      = $row['EventBody'];
            $eventStart     = $row['EventDateStart'];

            $eventEnd       = $row['EventDateEnd'];
            $eventAuthor    = $row['EventAuthor'];
            $eventPosted    = $row['EventPosted'];
            $eventStatus    = $row['EventStatus'];

            $eventLink1     = $row['EventLink1'];
            $eventLink2     = $row['EventLink2'];
            $eventLink3     = $row['EventLink3'];
            $eventLink4     = $row['EventLink4'];
            $eventLink5     = $row['EventLink5'];


            $responseData = [

                'eventPhoto'    => $eventPhoto,
                'eventTitle'    => $eventTitle,
                'eventBody'     => $eventBody,
                'eventStart'    => $eventStart,

                'eventEnd'      => $eventEnd,
                'eventAuthor'   => $eventAuthor,
                'eventPosted'   => $eventPosted,
                'eventStatus'   => $eventStatus,

                'eventLink1'    => $eventLink1,
                'eventLink2'    => $eventLink2,
                'eventLink3'    => $eventLink3,
                'eventLink4'    => $eventLink4,
                'eventLink5'    => $eventLink5



            ];


            header('Content-Type: application/json');
            echo json_encode($responseData);


        } else {

            echo "No data found";
        }

    }
}


function getOfficersList($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;

    // Call the stored procedure
    $sql = "{CALL AdminEvents_SP(?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        //array($dateToday, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));

    } else {


        ?>
        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
            <div class="d-flex row officer-info">

                <div id="officers_profPic">
                    <img src="images/users/<?= $row['ProfilePic']; ?>" alt="" class="rounded-circle" >
                </div>

                <div class="flex-column mx-2">
                    <span class="font-weight-bold" id="officers_Name"><?= $row['Fullname']; ?></span>
                    <p class="text-muted" id="officers_position"><?= $row['Position']; ?></p>
                </div>

                <div id="officers_fblink">
                    
                    <a href="<?= $row['FBLink']; ?>" target="_blank">
                        <i class="ti-facebook text-muted"></i>
                    </a>
                </div>

            </div>
        <?php endwhile; ?>
        <?php

        sqlsrv_free_stmt($stmt);



    }

}

function getOngoingEventDetails($conn){

    $tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $EventID = isset($_GET['eventID']) ? $_GET['eventID'] : null;

    $eventTitle	 = null;
    $eventAuthor = null;
    $startDate   = null;
    $endDate     = null;
    $eventContent = null;
    $eventLink1  = null;
    $eventLink2  = null;
    $eventLink3  = null;
    $eventLink4  = null;
    $eventLink5  = null;
    $eventPosted = null;
    $eventPhoto  = null;
    $eventStatus = null;
    $dateToday	 = null;

    // Call the stored procedure
    $sql = "{CALL AdminEvents_SP(
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?,
        ?, ?, ?,    
        
        )}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
       
        array($eventTitle, SQLSRV_PARAM_IN),
        array($eventAuthor, SQLSRV_PARAM_IN),
        array($startDate, SQLSRV_PARAM_IN),
        array($endDate, SQLSRV_PARAM_IN),
        array($eventContent, SQLSRV_PARAM_IN),
        array($eventLink1, SQLSRV_PARAM_IN),
        array($eventLink2, SQLSRV_PARAM_IN),
        array($eventLink3, SQLSRV_PARAM_IN),
        array($eventLink4, SQLSRV_PARAM_IN),
        array($eventLink5, SQLSRV_PARAM_IN),
        array($eventPosted, SQLSRV_PARAM_IN),
        array($eventPhoto, SQLSRV_PARAM_IN),
        array($eventStatus, SQLSRV_PARAM_IN),
        array($dateToday, SQLSRV_PARAM_IN   ),

        array($EventID, SQLSRV_PARAM_IN),

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));

    } else {

        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            $eventPhoto     = $row['EventPhoto'];
            $eventTitle     = $row['EventTitle'];
            $eventBody      = $row['EventBody'];
            $eventStart     = $row['EventDateStart'];

            $eventEnd       = $row['EventDateEnd'];
            $eventAuthor    = $row['EventAuthor'];
            $eventPosted    = $row['EventPosted'];
            $eventStatus    = $row['EventStatus'];

            $eventLink1     = $row['EventLink1'];
            $eventLink2     = $row['EventLink2'];
            $eventLink3     = $row['EventLink3'];
            $eventLink4     = $row['EventLink4'];
            $eventLink5     = $row['EventLink5'];


            $responseData = [

                'eventPhoto'    => $eventPhoto,
                'eventTitle'    => $eventTitle,
                'eventBody'     => $eventBody,
                'eventStart'    => $eventStart,

                'eventEnd'      => $eventEnd,
                'eventAuthor'   => $eventAuthor,
                'eventPosted'   => $eventPosted,
                'eventStatus'   => $eventStatus,

                'eventLink1'    => $eventLink1,
                'eventLink2'    => $eventLink2,
                'eventLink3'    => $eventLink3,
                'eventLink4'    => $eventLink4,
                'eventLink5'    => $eventLink5



            ];


            header('Content-Type: application/json');
            echo json_encode($responseData);


        } else {

            echo "No data found";
        }

    }

}



//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);

switch ($tagged) {
    case 'saveCreatedEvent':
        saveCreatedEvent($conn);
        break;
    case 'UpdateEvents':
        UpdateEvents($conn);
        break;
    case 'getUpcomingEvents':
        getUpcomingEvents($conn);
        break;
    case 'getOngoingEvents':
        getOngoingEvents($conn);
        break;
    case 'countAllEvents':
        countAllEvents($conn);
        break;

    case 'countAllFinishedEvents':
        countAllFinishedEvents($conn);
        break;
    case 'getUpcomingEventDetials':
        getUpcomingEventDetials($conn);
        break;
    case 'getOfficersList':
        getOfficersList($conn);
        break;
    case 'getOngoingEventDetails':
        getOngoingEventDetails($conn);
        break;

    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>