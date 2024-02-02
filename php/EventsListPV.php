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

// // Debug Point 2
// echo "Before fetching item list.\r\n";

function fetchItemList($conn, $tagged, $eventsTag) {
    $sql = "{CALL Events_SP(?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ?)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($eventsTag, SQLSRV_PARAM_IN),
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
        
    <div class="event-container" id="eventsContainer">
        <div class="event-photo" id="eventPhoto">
            <img src="images/events_pics/<?= $row['EventPhoto']; ?>">
            <!-- <img src="images/events_pics/sample1.jpg"> -->
        </div>
        <div class="event-contents" id="eventContents">
            <div class="event-title" id="eventTitle"><?= $row['EventTitle']; ?></div>
            <div class="event-desc" id="eventDescription">
                <p><?= $row['EventBody']; ?></p>
            </div>

            <div class="event-sub-cont">
                <div class="event-sub">
                    <div class="event-author" id="eventAuthor">
                        <span><?= $row['EventAuthor']; ?></span>
                    </div>
                    <div class="event-dateposted" id="eventDatePosted">
                        <span><?= isset($row['EventPosted']) ? $row['EventPosted']->format('Y-m-d') : ''; ?></span>
                    </div>
                </div>
                <div class="event-status" id="eventStatus">
                    <input class="status <?= $row['EventStatus']; ?>" type="button" readonly value="<?= $row['EventStatus']; ?>">
                </div>
                
            </div>
        </div>
        <div class="hidden">
            <input class="event-link1" value="<?= $row['EventLink1']; ?>">
            <input class="event-link2" value="<?= $row['EventLink2']; ?>">
            <input class="event-link3" value="<?= $row['EventLink3']; ?>">
            <input class="event-link4" value="<?= $row['EventLink4']; ?>">
            <input class="event-link5" value="<?= $row['EventLink5']; ?>">

            <input class="eventDateStart" readonly value="<?= isset($row['EventDateStart']) ? $row['EventDateStart']->format('Y-m-d') : ''; ?>">
            <input class="eventDateEnd" readonly value="<?= isset($row['EventDateEnd']) ? $row['EventDateEnd']->format('Y-m-d') : ''; ?>">
        </div>
    </div>
    
<?php

}
// ... Your PHP code to free the statement and close the connection ...

    sqlsrv_free_stmt($stmt);
}

//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
$eventsTag = isset($_GET['EventTag']) ? $_GET['EventTag'] : null;

switch ($tagged) {
    case 'getEventsList':
        fetchItemList($conn, $tagged, $eventsTag);
        break;
    case 'filterEventsList':
        fetchItemList($conn, $tagged, $eventsTag);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>