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

function fetchItemList($conn, $tagged, $searchItem) {
    $sql = "{CALL StudentRentals_SP(?,null,null,null,null,null,null,null,null,null,null,null,null,null,null,?,null)}";
    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($searchItem, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }




    // Output HTML for partial view
    ?>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Custom CSS to reset row background color -->
    <style>
        .table tbody tr td {
            border: 1px solid #9d9d9d;
        }
        input[type=checkbox] {
            width: 20px;
            height: 20px;
        }
    </style>
    <table class="table  table-bordered">
        <!-- Your table content from the database -->
        <thead class="thead-dark">
        <tr>
            <th></th>
            <th style="display:none;">ItemID</th>
            <th>Item Code</th>
            <th>Item</th>
            <th>Status</th>
            <!-- Add more columns as needed -->
        </tr>
        </thead>
        <tbody>
        <!-- Loop through your fetched records and generate rows -->
        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
            <tr class="<?= $row['Status']; ?>">
                <td>
                    <input type="checkbox" class="item-cb">
                </td>
                <td style="display:none;"><?= $row['ItemID']; ?></td>
                <td><?= $row['ItemNumber']; ?></td>
                <td><?= $row['ItemName']; ?></td>
                <td><?= $row['Status']; ?></td>
                <!-- Add more columns as needed -->
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
    <script>
        $(document).ready(function(){
            // Find checkboxes in rows with the Reserved class and disable them
            $('.table tbody tr.Available .item-cb').prop('disabled', false);
            $('.table tbody tr.Reserved .item-cb').prop('disabled', true);
            $('.table tbody tr.Rented .item-cb').prop('disabled', true);
        });
    </script>
    <?php

    sqlsrv_free_stmt($stmt);
}

//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
$searchItem = isset($_GET['searchItem']) ? $_GET['searchItem'] : null;

switch ($tagged) {
    case 'getItemList':
        fetchItemList($conn, $tagged, $searchItem);
        break;
    case 'getSearchItem':
        fetchItemList($conn, $tagged, $searchItem);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>