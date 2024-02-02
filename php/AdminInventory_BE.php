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

function getInventoryTable($conn) {
    $Tagged	= isset($_GET['tagged']) ? $_GET['tagged'] : null ;
    $sql = "{CALL AdminInventory_SP(
            ?,
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            <tr>
                <td class="ItemID" style="display:none;"><?php echo $row['ItemID']?></td>
                <td class="ItemNumber text-info"><?php echo $row['ItemNumber']?></td>
                <td class="ItemName text-center" style="max-width:200px; word-wrap:break-word; white-space:normal;"><?php echo $row['ItemName']?></td>
                <td class="UnitQuantity text-center"><?php echo $row['UnitQuantity']?></td>
                <td class="text-center">
                    <div class="dropdown">
                    <button class="itemStatus btn btn-sm dropdown-toggle" type="button" id="StatusDropDown" style="font-weight:bold;" >
                                <?= $row['Status']; ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="StatusDropDown">
                                <!-- <button class="dropdown-item" type="button">Reserved</button> -->
                                <button class="dropdown-item" type="button">Available</button>
                                <button class="dropdown-item" type="button">Reserved</button>
                                <button class="dropdown-item" type="button">Rented</button>
                                <button class="dropdown-item" type="button">Returned</button>
                            </div>
                    </div>
                </td>
                <td>
                    <div class="row" style="gap: 12px;">
                        <button type="button" class="btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#exampleModal">
                            <i class="ti-eye"></i>
                        </button>
                        <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>

        <?php
        }
    }
    sqlsrv_free_stmt($stmt);

   
}

function updateItemStatus($conn){
    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null ;
    $ItemID	= isset($_POST['ItemID']) ? $_POST['ItemID'] : null ;
    $ItemStatus	= isset($_POST['ItemStatus']) ? $_POST['ItemStatus'] : null ;
    $ModifiedBy	= isset($_POST['User']) ? $_POST['User'] : null ;
    
    date_default_timezone_set('Asia/Manila');
    $ModifiedOn = Date('Y-m-d H:i:s');

    $sql = "{CALL AdminInventory_SP(
            ?,?,?,?,?
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($ItemID, SQLSRV_PARAM_IN),
        array($ItemStatus, SQLSRV_PARAM_IN),
        array($ModifiedBy, SQLSRV_PARAM_IN),
        array($ModifiedOn, SQLSRV_PARAM_IN),

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        echo "Updated Successfully!";
    }
}


function saveNewItem($conn){
    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null ;
    $ItemName	= isset($_POST['ItemName']) ? $_POST['ItemName'] : null ;
    $ItemCategory	= isset($_POST['ItemCategory']) ? $_POST['ItemCategory'] : null ;
    $ItemBrand	= isset($_POST['ItemBrand']) ? $_POST['ItemBrand'] : null ;
    $ItemSKU = isset($_POST['ItemSKU']) ? $_POST['ItemSKU'] : null ;
    $ItemNumber = isset($_POST['ItemNumber']) ? $_POST['ItemNumber'] : null ;
    $UnitQuantity = isset($_POST['UnitQuantity']) ? (int)$_POST['UnitQuantity'] : null;


    date_default_timezone_set('Asia/Manila');
    $CreatedBy	= isset($_POST['User']) ? $_POST['User'] : null ;
    $CreatedOn = Date('Y-m-d H:i:s');

    $ItemID = null;
    $ItemStatus = null;
    $ModifiedBy = null;
    $ModifiedOn = null;



    $sql = "{CALL AdminInventory_SP(
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($ItemID, SQLSRV_PARAM_IN),
        array($ItemStatus, SQLSRV_PARAM_IN),
        array($ModifiedBy, SQLSRV_PARAM_IN),
        array($ModifiedOn, SQLSRV_PARAM_IN),

        array($ItemNumber, SQLSRV_PARAM_IN),
        array($ItemName, SQLSRV_PARAM_IN),
        array($ItemSKU, SQLSRV_PARAM_IN),
        array($ItemCategory, SQLSRV_PARAM_IN),
        array($ItemBrand, SQLSRV_PARAM_IN),

        array($UnitQuantity, SQLSRV_PARAM_IN),
        array($CreatedBy, SQLSRV_PARAM_IN),
        array($CreatedOn, SQLSRV_PARAM_IN),

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {
        echo "Successfully Saved";
    }
}

function CategoryFilter($conn){
    $Tagged	= isset($_GET['tagged']) ? $_GET['tagged'] : null ;
    $sql = "{CALL AdminInventory_SP(
            ?,
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
                <button class="dropdown-item"><?php echo $row['Category'] ?></button>

            <?php
        }
        
        sqlsrv_free_stmt($stmt);
    }
}

function BrandFilter($conn){
    $Tagged	= isset($_GET['tagged']) ? $_GET['tagged'] : null ;
    $sql = "{CALL AdminInventory_SP(
            ?,
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
                <button class="dropdown-item"><?php echo $row['Brand'] ?></button>

            <?php
        }
        
        sqlsrv_free_stmt($stmt);
    }
}

function searchCategory($conn){
    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null ;
    $searchBrand	= isset($_POST['searchCategory']) ? $_POST['searchCategory'] : null ;
    $searchCategory	= isset($_POST['searchCategory']) ? $_POST['searchCategory'] : null ;
    $null = null;

    $sql = "{CALL AdminInventory_SP(
        ?, ?, ?, ?, ?
        , ?, ?, ?, ?, ?
        , ?, ?, ?, ?, ?
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),

        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($searchCategory, SQLSRV_PARAM_IN),
        array($searchBrand, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            <tr>
                <td class="ItemID" style="display:none;"><?php echo $row['ItemID']?></td>
                <td class="ItemNumber text-info"><?php echo $row['ItemNumber']?></td>
                <td class="ItemName text-center" style="max-width:200px; word-wrap:break-word; white-space:normal;"><?php echo $row['ItemName']?></td>
                <td class="UnitQuantity text-center"><?php echo $row['UnitQuantity']?></td>
                <td class="text-center">
                    <div class="dropdown">
                    <button class="itemStatus btn btn-sm dropdown-toggle" type="button" id="StatusDropDown" style="font-weight:bold;" >
                                <?= $row['Status']; ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="StatusDropDown">
                                <!-- <button class="dropdown-item" type="button">Reserved</button> -->
                                <button class="dropdown-item" type="button">Available</button>
                                <button class="dropdown-item" type="button">Reserved</button>
                                <button class="dropdown-item" type="button">Rented</button>
                                <button class="dropdown-item" type="button">Returned</button>
                            </div>
                    </div>
                </td>
                <td>
                    <div class="row" style="gap: 12px;">
                        <button type="button" class="btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#exampleModal">
                            <i class="ti-eye"></i>
                        </button>
                        <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>

        <?php
        }

    }
}


function searchBrand($conn){
    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null ;
    $searchBrand	= isset($_POST['searchCategory']) ? $_POST['searchCategory'] : null ;
    $searchCategory	= isset($_POST['searchCategory']) ? $_POST['searchCategory'] : null ;
    $null = null;

    $sql = "{CALL AdminInventory_SP(
            ?, ?, ?, ?, ?
            , ?, ?, ?, ?, ?
            , ?, ?, ?, ?, ?
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),

        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($searchCategory, SQLSRV_PARAM_IN),
        array($searchBrand, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            <tr>
                <td class="ItemID" style="display:none;"><?php echo $row['ItemID']?></td>
                <td class="ItemNumber text-info"><?php echo $row['ItemNumber']?></td>
                <td class="ItemName text-center" style="max-width:200px; word-wrap:break-word; white-space:normal;"><?php echo $row['ItemName']?></td>
                <td class="UnitQuantity text-center"><?php echo $row['UnitQuantity']?></td>
                <td class="text-center">
                    <div class="dropdown">
                    <button class="itemStatus btn btn-sm dropdown-toggle" type="button" id="StatusDropDown" style="font-weight:bold;" >
                                <?= $row['Status']; ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="StatusDropDown">
                                <!-- <button class="dropdown-item" type="button">Reserved</button> -->
                                <button class="dropdown-item" type="button">Available</button>
                                <button class="dropdown-item" type="button">Reserved</button>
                                <button class="dropdown-item" type="button">Rented</button>
                                <button class="dropdown-item" type="button">Returned</button>
                            </div>
                    </div>
                </td>
                <td>
                    <div class="row" style="gap: 12px;">
                        <button type="button" class="btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#exampleModal">
                            <i class="ti-eye"></i>
                        </button>
                        <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>

        <?php
        }

    }
}


function searchItem($conn){
    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null ;
    $searchItem	= isset($_POST['searchItem']) ? $_POST['searchItem'] : null ;
    $null = null;

    $sql = "{CALL AdminInventory_SP(
            ?, ?, ?, ?, ?
            , ?, ?, ?, ?, ?
            , ?, ?, ?, ?, ?
            ,?
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),

        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),
        array($null, SQLSRV_PARAM_IN),

        array($searchItem, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            <tr>
                <td class="ItemID" style="display:none;"><?php echo $row['ItemID']?></td>
                <td class="ItemNumber text-info"><?php echo $row['ItemNumber']?></td>
                <td class="ItemName text-center" style="max-width:200px; word-wrap:break-word; white-space:normal;"><?php echo $row['ItemName']?></td>
                <td class="UnitQuantity text-center"><?php echo $row['UnitQuantity']?></td>
                <td class="text-center">
                    <div class="dropdown">
                    <button class="itemStatus btn btn-sm dropdown-toggle" type="button" id="StatusDropDown" style="font-weight:bold;" >
                                <?= $row['Status']; ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="StatusDropDown">
                                <!-- <button class="dropdown-item" type="button">Reserved</button> -->
                                <button class="dropdown-item" type="button">Available</button>
                                <button class="dropdown-item" type="button">Reserved</button>
                                <button class="dropdown-item" type="button">Rented</button>
                                <button class="dropdown-item" type="button">Returned</button>
                            </div>
                    </div>
                </td>
                <td>
                    <div class="row" style="gap: 12px;">
                        <button type="button" class="btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal" data-target="#exampleModal">
                            <i class="ti-eye"></i>
                        </button>
                        <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>

        <?php
        }

    }
}


function countItemAvailable($conn){
    $Tagged	= isset($_GET['tagged']) ? $_GET['tagged'] : null ;

    $sql = "{CALL AdminInventory_SP(
            ?
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            $item_available = $row['countItemAvailable'];

            $responseData = [
                'item_available' => $item_available

            ];
        
            // Set the Content-Type header to indicate JSON content
            header('Content-Type: application/json');
            echo json_encode($responseData);
        }

    }
}


function countItemRented($conn){
    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null ;

    $sql = "{CALL AdminInventory_SP(
            ?
        )}";

    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{

        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            $item_available = $row['countItemAvailable'];

            $responseData = [
                'item_available' => $item_available
            ];
        
            // Set the Content-Type header to indicate JSON content
            header('Content-Type: application/json');
            echo json_encode($responseData);
        }

    }
}



//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);

switch ($tagged) {
    case 'getInventoryTable':
        getInventoryTable($conn);
        break;
    case 'updateItemStatus':
        updateItemStatus($conn);
        break;
    case 'saveNewItem':
        saveNewItem($conn);
        break;
    case 'CategoryFilter':
        CategoryFilter($conn);
        break;
    case 'BrandFilter':
        BrandFilter($conn);
        break;
    case 'searchCategory':
        searchCategory($conn);
        break;
    case 'searchBrand':
        searchBrand($conn);
        break;
    case 'searchItem':
        searchItem($conn);
        break;

    case 'countItemAvailable':
        countItemAvailable($conn);
        break;
    case 'countItemRented':
        countItemRented($conn);
        break;
    case 'countItemReserved':
        countItemReserved($conn);
        break;
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>





