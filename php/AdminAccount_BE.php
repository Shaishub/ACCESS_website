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

function saveUserSignIn($conn) {
    // Extract and sanitize posted data

    $Tagged			    = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $Firstname		    = isset($_POST['Firstname']) ? $_POST['Firstname'] : null;
    $Lastname		    = isset($_POST['Lastname']) ? $_POST['Lastname'] : null;
    $Email			    = isset($_POST['Email']) ? $_POST['Email'] : null;
    $Position           = isset($_POST['Position'])? $_POST['Position'] : null;
    
    $FBLink             = isset($_POST['FBLink'])? $_POST['FBLink'] : null;
    $StudentNumber	    = isset($_POST['StudentNumber']) ? $_POST['StudentNumber'] : null;
    $MobileNumber	    = isset($_POST['MobileNumber']) ? $_POST['MobileNumber'] : null;
    $Gender				= isset($_POST['Gender']) ? $_POST['Gender'] : null;
    $YearSection		= isset($_POST['YearSection']) ? $_POST['YearSection'] : null;
    $AcadYear		    = isset($_POST['AcadYear']) ? $_POST['AcadYear'] : null;
    
    $Username		    = isset($_POST['Username']) ? $_POST['Username'] : null;
    $Password		    = isset($_POST['Password']) ? $_POST['Password'] : null;
    $hashedPassword     = password_hash($Password, PASSWORD_BCRYPT);

    $CreatedBy          = isset($_POST['User']) ? $_POST['User'] : null;

    date_default_timezone_set('Asia/Manila');
    $CreatedOn          = date('Y-m-d H:i:s');
    $UserID             = null;
    $LoginDate          = null;
    $LoginTime          = null;          
    $NewPassword        = null;     
    $ModifiedBy         = null;
    $ModifiedOn         = null;
    $AccountID          = null;


    
    // Your logic to call the stored procedure and insert data
    $sql = "{CALL AdminAccount_SP(
            ?, ?, ?,  
            ?, ?, ?, 
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
        )}";

    $params = array(
        array($Tagged           , SQLSRV_PARAM_IN),
        array($Username		    , SQLSRV_PARAM_IN),
        array($UserID		    , SQLSRV_PARAM_IN),

        array($LoginDate		, SQLSRV_PARAM_IN),
        array($LoginTime		, SQLSRV_PARAM_IN),
        array($NewPassword		, SQLSRV_PARAM_IN),

        array($CreatedBy		, SQLSRV_PARAM_IN),
        array($CreatedOn		, SQLSRV_PARAM_IN),
        array($ModifiedBy		, SQLSRV_PARAM_IN),
        array($ModifiedOn		, SQLSRV_PARAM_IN),
        array($AccountID		, SQLSRV_PARAM_IN),

        array($Firstname		, SQLSRV_PARAM_IN),
        array($Lastname		    , SQLSRV_PARAM_IN),
        array($Email	        , SQLSRV_PARAM_IN),
        array($Position	        , SQLSRV_PARAM_IN),
        array($FBLink           , SQLSRV_PARAM_IN),
        array($StudentNumber    , SQLSRV_PARAM_IN),
        array($YearSection      , SQLSRV_PARAM_IN),
        array($AcadYear         , SQLSRV_PARAM_IN),
        array($MobileNumber     , SQLSRV_PARAM_IN),
        array($Gender           , SQLSRV_PARAM_IN), 
        array($hashedPassword   , SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        echo "Data saved successfully!";
    }

    sqlsrv_free_stmt($stmt);
 
   
}

function checkUserInfo($conn){

    $username   = null;
    $userType   = null;
    $userStatus = null;

    $Tagged	= isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $Username	= isset($_GET['Username']) ? $_GET['Username'] : null;

    $sql = "{CALL AdminAccount_SP(?, ?)}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($Username, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {

        // Check if there are rows
        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $username = $row['Username'];
            $userType = $row['UserType'];
            $userStatus = $row['UserStatus'];

        }

    }

    // Close the statement for each iteration
    sqlsrv_free_stmt($stmt);

    $responseData = [
        'Username' => $username,
        'UserType' => $userType,
        'UserStatus' => $userStatus
    ];

    // Set the Content-Type header to indicate JSON content
    header('Content-Type: application/json');
    echo json_encode($responseData);

    
}

function loggingUser($conn){
    // $log_username = null
    // $log_email = null;
    // $log_password = null;

    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $Username	= isset($_POST['username']) ? $_POST['username'] : null;
    $Email	= isset($_POST['email']) ? $_POST['email'] : null;
    $Password	= isset($_POST['password']) ? $_POST['password'] : null;

    date_default_timezone_set('Asia/Manila');
    $LoginDate = date('Y/m/d');
    $LoginTime = date('H:i:s');
    

    $sql = "{CALL AdminAccount_SP(?,?, null, ?,?)}";
    $params = array(
        array($Tagged           , SQLSRV_PARAM_IN),
        array($Username		    , SQLSRV_PARAM_IN),
        array($LoginDate		    , SQLSRV_PARAM_IN),
        array($LoginTime		    , SQLSRV_PARAM_IN),
        // array($Email		    , SQLSRV_PARAM_IN),
        // array($Password		    , SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {
        // Check if there are rows
        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $log_userid     = $row['UserID'];
            $log_username   = $row['Username'];
            $log_password   = $row['Password'];
            $log_email      = $row['Email'];

            $log_firstname      = $row['Firstname'];
            $log_lastname       = $row['Lastname'];
            $log_StudentNumber  = $row['StudentNumber'];
            $log_MobileNumber   = $row['MobileNumber'];

            $log_Gender         = $row['Gender'];
            $log_UserType       = $row['UserType'];
            $log_UserStatus     = $row['UserStatus'];
            $log_ProfilePic     = $row['ProfilePic'];

            // Debug outputs
            // echo "Entered Password: $Password\n";
            // echo "Stored Hashed Password: $log_password\n";

            $verify = password_verify($Password, $log_password);

            if ($verify) {
                // Password is correct
                // echo "Password is correct!";
                // header("Location: index.php");

                $_SESSION['user_id']    = $log_userid;
                $_SESSION['username']   = $log_username;
                $_SESSION['Email']      = $log_email;

                $_SESSION['Firstname']     = $log_firstname    ;
                $_SESSION['Lastname']      = $log_lastname     ; 
                $_SESSION['StudentNumber'] = $log_StudentNumber;
                $_SESSION['MobileNumber']  = $log_MobileNumber ;

                $_SESSION['Gender']        = $log_Gender    ;
                $_SESSION['UserType']      = $log_UserType  ;
                $_SESSION['UserStatus']    = $log_UserStatus;
                $_SESSION['ProfilePic']    = $log_ProfilePic;
                

                $responseData = [
                    'verify' => $verify,
                    'username' => $log_username,
                    'userID' => $log_userid,
                ];
            
                // Set the Content-Type header to indicate JSON content
                header('Content-Type: application/json');
                echo json_encode($responseData);


            } else {
                // Password is incorrect
                // echo "Invalid password!";
                echo $verify;
            }
        }

        

    }
    
    

    //sqlsrv_free_stmt($stmt);

}


function OfficersDashboardVerify($conn){

    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $ODPass	= isset($_POST['ODPass']) ? $_POST['ODPass'] : null;

    $sql = "{CALL AdminAccount_SP(?, null, null, null, null, null, null, null, 
        null, null, null, null, null, null, null, null, null)}";
    $params = array(
        array($Tagged           , SQLSRV_PARAM_IN),
        //array($ODPass		    , SQLSRV_PARAM_IN)

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {
        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            $log_userid     = $row['UserID'];
            $log_ODpass     = $row['Password'];

            $verify = password_verify($ODPass, $log_ODpass);

            $responseData = [
                'verify' => $verify,
            ];
        
            // Set the Content-Type header to indicate JSON content
            header('Content-Type: application/json');
            echo json_encode($responseData);
        }
    }


}

function changePassword($conn){

    $Tagged	= isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $UserID = isset($_POST['userID']) ? (int)$_POST['userID'] : null;
    $Username = isset($_POST['username']) ? $_POST['username'] : null;
    $chng_pass	= isset($_POST['password']) ? $_POST['password'] : null;
    $NewPassword = password_hash($chng_pass, PASSWORD_BCRYPT);

    date_default_timezone_set('Asia/Manila');
    $ModifiedOn          = date('Y-m-d H:i:s');
    $ModifiedBy          = isset($_POST['User']) ? $_POST['User'] : null;

    $Username = null;
    $UserID = null;
    $LoginDate = null;
    $LoginTime = null;
    $CreatedBy = null;
    $CreatedOn = null;

    $sql = "{CALL AdminAccount_SP(
            ?,?,?,
            ?, ?, ?,
            ?, ?, ?, ? 
    )}";

    $params = array(
        array($Tagged           , SQLSRV_PARAM_IN),
        array($Username		    , SQLSRV_PARAM_IN),
        array($UserID		    , SQLSRV_PARAM_IN),

        array($LoginDate		, SQLSRV_PARAM_IN),
        array($LoginTime		, SQLSRV_PARAM_IN),
        array($NewPassword		, SQLSRV_PARAM_IN),

        array($CreatedBy		, SQLSRV_PARAM_IN),
        array($CreatedOn		, SQLSRV_PARAM_IN),
        array($ModifiedBy		, SQLSRV_PARAM_IN),
        array($ModifiedOn		, SQLSRV_PARAM_IN),

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else{
        echo "Successfully changed password";
    }
}




function OfficersTablePV($conn)
{
    $Tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;

    $sql = "{CALL AdminAccount_SP(?)}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            <tr>
                <td style="display:none;"><?php echo $row['UserID']; ?></td>
                <td><?php echo $row['Firstname']; ?></td>
                <td><?php echo $row['Position']; ?></td>
                <td class="text-success"><?php echo $row['AcadYear']; ?></td>
                <td>
                    <div class="row justify-content-between align-items-center px-4">
                        <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal">
                            <i class="ti-eye"></i>
                        </button>
                        <button type="button" class="btnEdit btn btn-inverse-success btn-rounded btn-icon" data-toggle="modal">
                            <!-- data-target="#editOfficerInfoModal" -->
                            <i class="ti-pencil"></i>
                        </button>
                        <button type="button" class="btnDelete btn btn-inverse-danger btn-rounded btn-icon">

                                <!-- onclick="deleteRow(this)" -->
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

function viewOfficerInfo($conn){

    $Tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $UserID = isset($_GET['UserID']) ? $_GET['UserID'] : null;

    $sql = "{CALL AdminAccount_SP(?, null, ?)}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($UserID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {

        if($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        ?>

            <form>
                <div class="d-flex row">
                <div class="col-sm-6">
                    <div class="form-group">
                    <label for="firstName">First name: </label>
                    <input type="text" class="form-control" id="firstName" value="<?php echo $row['Firstname']; ?>" placeholder="Enter officer name"readonly>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" class="form-control" id="lastName" value="<?php echo $row['Lastname']; ?>" placeholder="Enter officer name" readonly>
                    </div>
                </div>
                </div>
                <div class="form-group">
                <label for="email">Officer's E-mail:</label>
                <input type="email" class="form-control" id="email" value="<?php echo $row['Email']; ?>" placeholder="Enter e-mail" readonly>
                </div>
                <div class="form-group">
                <label for="position">Officer Position:</label>
                <input type="position" class="form-control" id="position" value="<?php echo $row['Position']; ?>" placeholder="Enter position" readonly>
                </div>
                <div class="form-group">
                <label for="fblink">Facebook Link:</label>
                <input type="fblink" class="form-control" id="fblink" value="<?php echo $row['FBlink']; ?>" placeholder="Paste facebook link" readonly>
                </div>
                <div class="d-flex row">
                <div class="col-sm-6 form-group">
                    <label for="studentNumber">Student Number:</label>
                    <input type="text" class="form-control" id="studentNumber" value="<?php echo $row['StudentNumber']; ?>" placeholder="Enter student number" readonly>
                </div>
            
                <div class="col-sm-6 form-group">
                    <label for="mobileNumber">Mobile Number:</label>
                    <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">+(63)</span>
                    </div>
                    <input type="text" pattern="[0-9]*" inputmode="numeric" class="form-control" id="mobileNumber" value="<?php echo $row['MobileNumber']; ?>" placeholder="Enter mobile number" readonly>
                    </div>
                </div>
                </div>
                <div class="d-flex row">
                <div class="col-sm-4">
                    <div class="form-group">
                    <label for="gender">Gender:</label>
                    <input class="form-control" id="gender" value="<?php echo $row['Gender']; ?>" readonly><?php echo $row['Gender']; ?> >
                    </div>
                </div>
                <div class="col-sm-8" style="display:flex; justify-content:space-evenly;">
                    <div class="form-group">
                    <label for="officeDuty">Year and Section:</label>
                    <input class="form-control" id="yearAndSection" value="<?php echo $row['YearSection']; ?>" readonly >
                    </div>

                    <div class="form-group">
                    <label for="officeDuty">Academic Year:</label>
                    <input type="text" class="form-control" id="acadyear" value="<?php echo $row['AcadYear']; ?>" placeholder="Enter Academic Year" readonly>
                    </div>
                </div>
                </div>

                <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" value="<?php echo $row['Username']; ?>" placeholder="Enter username" readonly>
                <p id="usernameError" style="color:red;"></p>
                </div>
                <div class="form-group">
                    <label for="officerPassword">Password</label>
                    <input type="password" class="form-control" id="officerPassword" value="<?php echo $row['Password']; ?>" placeholder="Password">
                </div>
                


            </form>
        
        <?php

        }


    }

}

function editOfficerInfo1($conn){

    $Tagged = "viewOfficerInfo";
    $UserID = isset($_GET['UserID']) ? $_GET['UserID'] : null;

    $sql = "{CALL AdminAccount_SP(?, null, ?)}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($UserID, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {

        if (sqlsrv_has_rows($stmt)) {

            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            $officer_userID         = $row['UserID'];
            $officer_Firstname      = $row['Firstname'];
            $officer_Lastname       = $row['Lastname'];
            $officer_Username       = $row['Username'];
            $officer_Email          = $row['Email'];
            $officer_Password       = $row['Password'];
            $officer_StudentNumber  = $row['StudentNumber'];
            $officer_YearSection    = $row['YearSection'];
            $officer_AcadYear       = $row['AcadYear'];
            $officer_Position       = $row['Position'];
            $officer_Gender         = $row['Gender'];
            $officer_MobileNumber   = $row['MobileNumber'];
            $officer_FBlink         = $row['FBlink'];

            $responseData = [
                'UserID' => $officer_userID,
                'Firstname' => $officer_Firstname,
                'Lastname' => $officer_Lastname,
                'Username' => $officer_Username,
                'Email' => $officer_Email,
                'Password' => $officer_Password,
                'StudentNumber' => $officer_StudentNumber,
                'YearSection' => $officer_YearSection,
                'AcadYear' => $officer_AcadYear,
                'Position' => $officer_Position,
                'Gender' => $officer_Gender,
                'MobileNumber' => $officer_MobileNumber,
                'FBlink' => $officer_FBlink,

            ];
        
            // Set the Content-Type header to indicate JSON content
            header('Content-Type: application/json');
            echo json_encode($responseData);
        }


    }

}

function updateOfficerInfo($conn){

    $Tagged         = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $Username       = isset($_POST['Username']) ? $_POST['Username'] : null;
    $UserID         = isset($_POST['UserID']) ? $_POST['UserID'] : null;

    $FirstName      = isset($_POST['Firstname']) ? $_POST['Firstname'] : null;
    $LastName       = isset($_POST['Lastname']) ? $_POST['Lastname'] : null;
    $Email          = isset($_POST['Email']) ? $_POST['Email'] : null;
    $Position       = isset($_POST['Position']) ? $_POST['Position'] : null;
    $FBLink         = isset($_POST['FBLink']) ? $_POST['FBLink'] : null;
    $AcadYear       = isset($_POST['AcadYear']) ? $_POST['AcadYear'] : null;
    $StudentNumber  = isset($_POST['StudentNumber']) ? $_POST['StudentNumber'] : null;
    $MobileNumber   = isset($_POST['MobileNumber']) ? $_POST['MobileNumber'] : null;
    $Gender         = isset($_POST['Gender']) ? $_POST['Gender'] : null;
    $YearSection    = isset($_POST['YearSection']) ? $_POST['YearSection'] : null;
   
    $Password        = isset($_POST['Password']) ? $_POST['Password'] : null;
    $ConfirmPassword = isset($_POST['ConfirmPassword']) ? $_POST['ConfirmPassword'] : null;
    $hashedPassword  = password_hash($ConfirmPassword, PASSWORD_BCRYPT);

    date_default_timezone_set('Asia/Manila');
    $ModifiedOn          = date('Y-m-d H:i:s');
    $ModifiedBy          = isset($_POST['User']) ? $_POST['User'] : null;

    $LoginDate          = null;
    $LoginTime          = null;
    $NewPassword        = null;
    $CreatedBy          = null;
    $CreatedOn          = null;
    $AccountID          = null;
    $Password           = null;
    $ConfirmPassword    = null;
    $hashedPassword     = null;

    $sql = "{CALL AdminAccount_SP(
        ?,?,?,
        ?,?,?,
        ?,?,?,?,?,
        ?,?,?,?,?,
        ?,?,?,?,?,?
    )}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($Username, SQLSRV_PARAM_IN),
        array($UserID, SQLSRV_PARAM_IN),

        array($LoginDate		, SQLSRV_PARAM_IN),
        array($LoginTime		, SQLSRV_PARAM_IN),
        array($NewPassword		, SQLSRV_PARAM_IN),
  
        array($CreatedBy, SQLSRV_PARAM_IN),
        array($CreatedOn, SQLSRV_PARAM_IN),
        array($ModifiedBy, SQLSRV_PARAM_IN),
        array($ModifiedOn, SQLSRV_PARAM_IN),
        array($AccountID, SQLSRV_PARAM_IN),

        array($FirstName, SQLSRV_PARAM_IN),
        array($LastName, SQLSRV_PARAM_IN),
        array($Email, SQLSRV_PARAM_IN),
        array($Position, SQLSRV_PARAM_IN),
        array($FBLink, SQLSRV_PARAM_IN),

        array($StudentNumber, SQLSRV_PARAM_IN),
        array($YearSection, SQLSRV_PARAM_IN),
        array($AcadYear, SQLSRV_PARAM_IN),
        array($MobileNumber, SQLSRV_PARAM_IN),
        array($Gender, SQLSRV_PARAM_IN),
        

        array($hashedPassword, SQLSRV_PARAM_IN),

    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Successfully Updated";
    }
}

function updateOfficerPassword($conn){
    $tagged = isset($_POST['tagged']) ? $_POST['tagged']: null;
    $UserID = isset($_POST['UserID']) ? $_POST['UserID']: null;
    $NewPassword = isset($_POST['Password']) ? $_POST['Password'] : null;
    $hashedPassword  = password_hash($NewPassword, PASSWORD_BCRYPT);

    date_default_timezone_set('Asia/Manila');
    $ModifiedOn          = date('Y-m-d H:i:s');
    $ModifiedBy          = isset($_POST['User']) ? $_POST['User'] : null;


    $sql = "{CALL AdminAccount_SP(
        ?, null, ?, 
        null, null, ?, 
        null, null, ?, ?
        
    )}";

    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($UserID, SQLSRV_PARAM_IN),
        array($hashedPassword, SQLSRV_PARAM_IN),
        array($ModifiedBy, SQLSRV_PARAM_IN),
        array($ModifiedOn, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Successfully changed the password!";
    }
}

function deleteOfficer($conn){
    $tagged = isset($_POST['tagged']) ? $_POST['tagged']: null;
    $UserID = isset($_POST['UserID']) ? $_POST['UserID']: null;

    date_default_timezone_set('Asia/Manila');
    $ModifiedOn          = date('Y-m-d H:i:s');
    $ModifiedBy          = isset($_POST['User']) ? $_POST['User'] : null;

    $sql = "{CALL AdminAccount_SP(
        ?, null, ?, 
        null, null, null, 
        null, null, ?, ?
        
    )}";

    $params = array(
        array($tagged, SQLSRV_PARAM_IN),
        array($UserID, SQLSRV_PARAM_IN),
        array($ModifiedBy, SQLSRV_PARAM_IN),
        array($ModifiedOn, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "UserID" + $UserID + "Deleted";
    }

}

function searchOfficer($conn){

    $Tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $Search = isset($_GET['searchValue']) ? $_GET['searchValue'] : null;

    $sql = "{CALL AdminAccount_SP(
        ?, null, null,
        null, null, null, 
        null, null, null, null, null, 
        null, null, null, null, null, 
        null, null, null, null, null, 
        null, null, null, ?
        
    )}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($Search, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            <tr>
                <td style="display:none;"><?php echo $row['UserID']; ?></td>
                <td><?php echo $row['Firstname']; ?></td>
                <td><?php echo $row['Position']; ?></td>
                <td class="text-success"><?php echo $row['AcadYear']; ?></td>
                <td>
                    <div class="row justify-content-between align-items-center px-4">
                        <button type="button" class="btnView btn btn-inverse-info btn-rounded btn-icon" data-toggle="modal">
                            <i class="ti-eye"></i>
                        </button>
                        <button type="button" class="btnEdit btn btn-inverse-success btn-rounded btn-icon" data-toggle="modal">
                            <!-- data-target="#editOfficerInfoModal" -->
                            <i class="ti-pencil"></i>
                        </button>
                        <button type="button" class="btnDelete btn btn-inverse-danger btn-rounded btn-icon">

                                <!-- onclick="deleteRow(this)" -->
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

function viewOfficeLogs($conn){

    $Tagged = isset($_GET['tagged']) ? $_GET['tagged'] : null;
    $LoginDate = isset($_GET['Today']) ? $_GET['Today'] : null;

    $sql = "{CALL AdminAccount_SP(
        ?, null, null, ? 
    )}";
    $params = array(
        array($Tagged, SQLSRV_PARAM_IN),
        array($LoginDate, SQLSRV_PARAM_IN),
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            ?>
            
            <tr>
                <td><?php echo $row['ProductID']; ?></td>
                <td><?php echo $row['ProductName']; ?></td>
                <td>
                    <?php echo isset($row['TranDate']) ? $row['TranDate']->format('m-d-Y, g:ia') : ''; ?>
                </td
                <td><?php echo $row['Vol_60ml']; ?></td>
                <td><?php echo $row['Vol_80ml']; ?></td>
                <td><?php echo $row['Vol_125ml']; ?></td>
                <td><?php echo $row['Vol_185ml']; ?></td>
                <td><?php echo $row['Vol_205ml']; ?></td>
            </tr>

        <?php
        }
    }
    sqlsrv_free_stmt($stmt);

}

function logoutUser($conn){
    $Tagged = isset($_POST['tagged']) ? $_POST['tagged'] : null;
    $LogoutDate = date('Y-m-d H:i:s');

    if($Tagged == "logoutUser"){
        session_destroy();
        unset($_SESSION['user_id']);
        echo json_encode(['success' => true]);
    }
    else {
        echo json_encode(['error' => 'Invalid request']);
    }

}


//-------------------------------------------------- Main script--------------------------------------------------------
$conn = connectToDatabase();

$tagged = isset($_GET['tagged']) ? $_GET['tagged'] : (isset($_POST['tagged']) ? $_POST['tagged'] : null);

switch ($tagged) {
    case 'saveUserSignIn':
        saveUserSignIn($conn);
        break;
    case 'checkUserInfo':
        checkUserInfo($conn);
        break;
    case 'loggingUser':
        loggingUser($conn);
        break;
    case 'OfficersDashboardVerify':
        OfficersDashboardVerify($conn);
        break;
    case 'changePassword':
        changePassword($conn);
        break;
    case 'OfficersTablePV':
        OfficersTablePV($conn);
        break;
    case 'viewOfficerInfo':
        viewOfficerInfo($conn);
        break;
    case 'editOfficerInfo1':
        editOfficerInfo1($conn);
        break;
    case 'updateOfficerInfo':
        updateOfficerInfo($conn);
        break;
    case 'updateOfficerPassword':
        updateOfficerPassword($conn);
        break;
    case 'deleteOfficer':
        deleteOfficer($conn);
        break;
    case 'searchOfficer':
        searchOfficer($conn);
        break;
    case 'loginCountToday':
        loginCountToday($conn);
        break;
    case 'viewOfficeLogs':
        viewOfficeLogs($conn);
        break;
    case 'logoutUser':
        logoutUser($conn);
        break;
        
    // Add more cases as needed
}

closeDatabaseConnection($conn);

?>





