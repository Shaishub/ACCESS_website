 // Function to update the content of the h3 element with the current date
 function updateCurrentDate() {
    var currentDateElement = document.getElementById('currentDate');
    
    // Create a new Date object to get the current date
    var currentDate = new Date();
    
    // Format the date as you like, for example: "Month Day, Year"
    var formattedDate = currentDate.toLocaleDateString('en-PH', { weekday: 'long', year: 'numeric', month: 'numeric', day: 'numeric' });
    
    // Set the formatted date as the content of the h3 element
    currentDateElement.textContent = formattedDate;
}

// Call the function when the page loads to initially display the current date
updateCurrentDate();

// You can also update the date at regular intervals if needed
// setInterval(updateCurrentDate, 1000); // Update every second (1000 milliseconds)
function validateAndShowNewPasswordModal() {

    var oldPasswordInput = document.getElementById('oldPassword').value;

    $.ajax({
      url:'php/AdminAccount_BE.php',
      type: 'POST',
      data: { 
        tagged : 'OfficersDashboardVerify',
        ODPass : oldPasswordInput 
      },
      success: function(res) {
        var u_adminVerify = res.verify;

        if(u_adminVerify == 1){

          $('#errorMessage').text("");
          $('#changePasswordModal').modal('hide');
          $('#newPasswordModal').modal('show');
          
        }
        else {
          $('#errorMessage').text("Invalid Password!");
        }
      },
      error: function(err){
        alert(err);

      }
    });

    // if (oldPasswordInput.trim() !== '') {
    //   $('#changePasswordModal').modal('hide');
    //   $('#newPasswordModal').modal('show');
    // } else {
    //   alert('Please enter the old password.');
    // }
  }

  function changePassword() {

    var newPassword = document.getElementById('newPassword').value;
    var confirmNewPassword = document.getElementById('confirmNewPassword').value;
    var UserID = document.getElementById('UserID').value;
    var Username = document.getElementById('Username').value;


    if (newPassword === confirmNewPassword) {
     
      $.ajax({
        url: 'php/AdminAccount_BE.php',
        type: 'POST',
        data: {
          tagged: 'changePassword',
          password: confirmNewPassword,
          userID: UserID,
          username: Username
        },
        success: function(res){
          //console.log(res);
          $('#newPasswordModal').modal('hide');
          alert(res);
        },
        error: function(err){
          console.log(err);
        },
      });


    } else {
      alert('New password and confirmation do not match. Please try again.');
    }
  }

  // //SEARCHBAR TABLE
  // function filterTable() {
  //   var input, filter, table, tr, td, i, txtValue;
  //   input = document.getElementById("searchInput");
  //   filter = input.value.toUpperCase();
  //   table = document.getElementById("dataTable");
  //   tr = table.getElementsByTagName("tr");

  //   for (i = 0; i < tr.length; i++) {
  //     td = tr[i].getElementsByTagName("td")[1]; // Change index to match the column you want to search
  //     if (td) {
  //       txtValue = td.textContent || td.innerText;
  //       if (txtValue.toUpperCase().indexOf(filter) > -1) {
  //         tr[i].style.display = "";
  //       } else {
  //         tr[i].style.display = "none";
  //       }
  //     }
  //   }
  // }

  //DELETE BUTTON
  // function deleteRow(button) {
  //   var row = button.closest('tr');
  //   row.remove();
  // }

  //EDIT OFFICER INFO TABLE

  function submitForm() {
    var formData = {
      firstName: document.getElementById('firstName').value,
      lastName: document.getElementById('lastName').value,
      email: document.getElementById('email').value,
      position: document.getElementById('position').value,
      fblink: document.getElementById('fblink').value,
      studentNumber: document.getElementById('studentNumber').value,
      mobileNumber: document.getElementById('mobileNumber').value,
      gender: document.getElementById('gender').value,
      yearAndSection: document.getElementById('yearAndSection').value,
      username: document.getElementById('username').value,
    };

    console.log(formData);

    // Add the submitted data to the table
    // appendToTable(formData);

    // Optional: Clear the form fields after submission
    document.getElementById('officerForm').reset();
  }

  function appendToTable(data) {
    var table = document.getElementById('dataTable');
    var row = table.insertRow(-1);

    for (var key in data) {
      if (data.hasOwnProperty(key)) {
        var cell = row.insertCell();
        cell.textContent = data[key];
      }
    }
  }

  // DISPLAY OFFICERS IN THE TABLE
  function OfficersTablePV() {
    $.ajax({
      url: 'php/AdminAccount_BE.php',
      type: 'GET',
      data: {
        tagged: 'OfficersTablePV'
      },
      success: function(res){
        //console.log(res);

        var officersTable = $('#OfficersAccountTable table thead');

        if(officersTable.length > 0){
          
          $('#OfficersAccountTable table tbody').html(res);

        } 
      },
      error: function(err){
        console.log(err);
      },
    });
  }


  // NEW OFFICERS SUBMIT
  function submitNewOfficer() {

    //Check first if the username is existing. 
    var session_user = document.getElementById('session_username').value;
    var firstname = document.getElementById('firstName').value;
    var lastname = document.getElementById('lastName').value;
    var email = document.getElementById('email').value;
    var position = document.getElementById('position').value;
    var fblink = document.getElementById('fblink').value;
    var acadyear = document.getElementById('acadyear').value;
    var studentNumber = document.getElementById('studentNumber').value;
    var mobileNum = document.getElementById('mobileNumber').value;
    var gender = document.getElementById('gender').value;
    var yearsec = document.getElementById('yearAndSection').value;
    var username = document.getElementById('username').value;
    var password = document.getElementById('officerPassword').value;
    var passwordConfrim = document.getElementById('officerConfirmPassword').value;

    if(password === passwordConfrim){

      $.ajax({
        url:'php/AdminAccount_BE.php',
        type: 'GET',
        data: {
          tagged: 'checkUserInfo',
          Username: username
        },
        success: function(res){
          console.log(res);

          var u_username    = res.Username;
          // var u_usertyp     = res.UserType;
          // var u_userstatus  = res.UserStatus;

          if(u_username == null){
            //alert('pwede gamitin');

            var account = new FormData();

            account.append('tagged', 'saveUserSignIn');
            account.append('Firstname', firstname);
            account.append('Lastname', lastname);
            account.append('Email', email);
            account.append('Position', position);
            account.append('FBLink', fblink);
            account.append('AcadYear', acadyear);
            account.append('StudentNumber', studentNumber);
            account.append('MobileNumber', mobileNum);
            account.append('Gender', gender);
            account.append('YearSection', yearsec);
            account.append('Username', username);
            account.append('Password', passwordConfrim);
            account.append('User', session_user);

            $.ajax({
              url:'php/AdminAccount_BE.php',
              type: 'POST',
              data: account,
              processData: false,
              contentType: false, 
              success: function(res){
                alert(res);
                //console.log(res);
                OfficersTablePV();
              },
              error: function(err){
                console.log(err);
              },
            });
          }
          else{
            $('#usernameError').text('This Username if already taken. Please Create a new one.');
          }

        },
        error: function(err){
          console.log(err);
        }

      });

    }

  }

  function viewOfficerInfo(UserID) {

    $.ajax({
      url: 'php/AdminAccount_BE.php',
      type: 'GET',
      data: {
        tagged: 'viewOfficerInfo',
        UserID: UserID
      },
      success: function(res){
        console.log(res);
        $("#officerInfoModal").modal('show');
        $('#officerInfoModal .modal-body').html(res);

      },
      error: function(err){
        console.log(err);
      },
    });

  }

  var prev_username;

  function editOfficerInfo(UserID) {

    $.ajax({
      url: 'php/AdminAccount_BE.php',
      type: 'GET',
      data: {
        tagged: 'editOfficerInfo1',
        UserID: UserID
      },
      success: function(res){
        console.log(res);

        var editofficerModal = $("#editOfficerInfoModal");

        $('#edit_firstName').val(res.Firstname);
        $('#edit_lastName').val(res.Lastname);
        $('#edit_email').val(res.Email);
        $('#edit_position').val(res.Position);
        $('#edit_fblink').val(res.FBlink);
        $('#edit_acadyear').val(res.AcadYear);
        $('#edit_studentNumber').val(res.StudentNumber);
        $('#edit_mobileNumber').val(res.MobileNumber);
        $('#edit_gender').val(res.Gender);
        $('#edit_yearAndSection').val(res.YearSection);
        $('#edit_username').val(res.Username);
        $('#edit_officerPassword').val(res.Password);
        $('#edit_userID').text(UserID);

        prev_username = res.Username;

        $("#editOfficerInfoModal").modal('show');

      },
      error: function(err){
        console.log(err);
      },
    });

  }

 function updateOfficerInfo() {

    var firstName      = $('#edit_firstName').val();
    var lastName       = $('#edit_lastName').val();
    var email          = $('#edit_email').val();
    var position       = $('#edit_position').val();
    var fblink         = $('#edit_fblink').val();
    var acadyear       = $('#edit_acadyear').val();
    var studentNumber  = $('#edit_studentNumber').val();
    var mobileNumber   = $('#edit_mobileNumber').val();
    var gender         = $('#edit_gender').val();
    var yearAndSection = $('#edit_yearAndSection').val();
    var username       = $('#edit_username').val();
    var session_user    = document.getElementById('session_username').value;
    var userID         = $('#edit_userID').text();

    var updateAccount = new FormData();

    if(prev_username === username){

      updateAccount.append('tagged', 'updateOfficerInfo');
      updateAccount.append('UserID', userID);
      updateAccount.append('User', session_user);
      updateAccount.append('Firstname', firstName);
      updateAccount.append('Lastname', lastName);
      updateAccount.append('Email', email);
      updateAccount.append('Position', position);
      updateAccount.append('FBLink', fblink);
      updateAccount.append('AcadYear', acadyear);
      updateAccount.append('StudentNumber', studentNumber);
      updateAccount.append('MobileNumber', mobileNumber);
      updateAccount.append('Gender', gender);
      updateAccount.append('YearSection', yearAndSection);
      updateAccount.append('Username', prev_username);

      $.ajax({
        url: 'php/AdminAccount_BE.php',
        type: 'POST',
        data: updateAccount,
        processData: false,
        contentType: false, 
        success: function(res){
          $('#edit_usernameError').text('');
          alert(res);
        },
        error: function(err){
          console.log(err);
        },

      });

    }
    else {

      $.ajax({
        url:'php/AdminAccount_BE.php',
        type: 'GET',
        data: {
          tagged: 'checkUserInfo',
          Username: username
        },
        success: function(res){
          console.log(res);

          var u_username    = res.Username;

          if(u_username == null){

            updateAccount.append('tagged', 'updateOfficerInfo');
            updateAccount.append('UserID', userID);
            updateAccount.append('User', session_user);
            updateAccount.append('Firstname', firstName);
            updateAccount.append('Lastname', lastName);
            updateAccount.append('Email', email);
            updateAccount.append('Position', position);
            updateAccount.append('FBLink', fblink);
            updateAccount.append('AcadYear', acadyear);
            updateAccount.append('StudentNumber', studentNumber);
            updateAccount.append('MobileNumber', mobileNumber);
            updateAccount.append('Gender', gender);
            updateAccount.append('YearSection', yearAndSection);
            updateAccount.append('Username', username);

            $.ajax({
              url: 'php/AdminAccount_BE.php',
              type: 'POST',
              data: updateAccount,
              processData: false,
              contentType: false, 
              success: function(res){
                $('#edit_usernameError').text('');
                alert(res);

              },
              error: function(err){
                console.log(err);
              },
  
            });

          }
          else{
            $('#edit_usernameError').text('This Username if already taken. Please Create a new one.');
          }
        },
        error: function(err){
          console.log(err);
        },

      });

    }


    
    
    //updateAccount.append('Password', $password);
    //updateAccount.append('ConfirmPassword', confirmpass);

  }

  function changeOfficerPassword() {

    //var username       = $('#edit_username').val();
    var session_user    = document.getElementById('session_username').value;
    var userID         = $('#edit_userID').text();

    var password       = $('#edit_officerPassword').val();
    var confirmpass     = $('#edit_officerConfirmPassword').val();

    if(password === confirmpass){

      var updatePass = new FormData();
      
      updatePass.append('tagged', "updateOfficerPassword");
      updatePass.append('Password', confirmpass);
      updatePass.append('UserID', userID);
      updatePass.append('User', session_user );

      $.ajax({
        url: 'php/AdminAccount_BE.php',
        type: 'POST',
        data: updatePass,
        processData: false,
        contentType: false, 
        success: function(res){
          $('#edit_passwordError').text('');
          alert(res);

        },
        error: function(err){
          console.log(err);
        },

      });

    }
    else{
      $('#edit_passwordError').text('Password do not match.');
    }

  }

  function deleteOfficer(UserID){

    var session_user    = document.getElementById('session_username').value;

    var deleteOfficer = new FormData();

    deleteOfficer.append('tagged', "deleteOfficer");
    deleteOfficer.append('UserID', UserID);
    deleteOfficer.append('User', session_user);

    $.ajax({
      url: 'php/AdminAccount_BE.php',
      type: 'POST',
      data: deleteOfficer,
      processData: false,
      contentType: false, 
      success: function(res){
        console.log(res);
        OfficersTablePV();

      },
      error: function(err){
        console.log(err);
      },

    });
  }


  // COUNT THE LOGINS OF THE DAY
  function viewOfficeLogs(){

    var today = $('#currentDate').text();
    var parts = today.split(', ');

    var dayOfWeek = parts[0];
    var date = parts[1];

    $.ajax({
      url: 'php/AdminAccount_BE.php',
      type: 'GET',
      data: {
        tagged: 'viewOfficeLogs',
        Today: date
      },
      success: function(res){
        //console.log(res);

        $('#officerLogsModal table tbody').html(res);

        var rows = $('#officerLogsModal table tbody').find('tr');

        count = 0;

        rows.each(function() {
          var thisRow = $(this);
          count++;

        });

        $('#loginCount').text(count);

        

      },
      error: function(err){
        console.log(err);
      },

    });

    
  }
  



 // --------------------------------------------------------------------------------------------- 

 $(document).ready(function() {

  OfficersTablePV();
  viewOfficeLogs();
  

  $('#submitNewOfficer').on('click', function() {
    submitNewOfficer();
  });

  $('#newOfficerModal').on('hidden.bs.modal', function () {
    $('#usernameError').text('');
    //$('#edit_officerPassword').hide();
    //$('#edit_officerConfirmPassword').hide();
  });

  //CLICK VIEW BUTTOn
  $('#OfficersAccountTable table').on('click', '.btnView', function() {

    var row = $(this).closest('tr'); 

    var UserID = row.find('td:eq(0)').text().trim();
    viewOfficerInfo(UserID);

  });


  //CLICK EDIT BUTTON
  $('#OfficersAccountTable table').on('click', '.btnEdit', function() {

    var row = $(this).closest('tr'); 

    var UserID = row.find('td:eq(0)').text().trim();
    editOfficerInfo(UserID);

    $('#updatePassword').on('click', function() {
          
      if($('#passwordChangeDiv').hasClass('hidden')){
        $('#passwordChangeDiv').removeClass('hidden');
      }
      else{
        $('#passwordChangeDiv').addClass('hidden');
      }

      $('#updatePasswordSave').on('click', function() {
        changeOfficerPassword();
      });

    });

  });

  $('#updateOfficerInfo').on('click', function() {
    updateOfficerInfo();
  });

  //CLICK EDIT BUTTON
  $('#OfficersAccountTable table').on('click', '.btnDelete', function() {

    var row = $(this).closest('tr'); 

    var UserID = row.find('td:eq(0)').text().trim();
    deleteOfficer(UserID);

  });

  // SEARCH OFFICER

  $('#searchInput').on('keyup', function(event) {

    if (event.keyCode === 13) {

      var searchValue = $(this).val();

      $.ajax({
        url: 'php/AdminAccount_BE.php', 
        method: 'GET',
        data: { 
          tagged: "searchOfficer",
          searchValue: searchValue 
        },
        success: function(res) {
          console.log(res);

          var officersTable = $('#OfficersAccountTable table thead');

          if(officersTable.length > 0){
            
            $('#OfficersAccountTable table tbody').html(res);

          } 

        },
        error: function(err) {
          console.error(err);
        }

      });

    }
  });

  //LOGOUT THE USER
  $('#logout').on('click', function() {

    $.ajax({

      url:'php/AdminAccount_BE.php',
      type:'POST',
      data:{
        tagged:"logoutUser",
      },
      success:function(res){
        var response = JSON.parse(res);

        if (response.success) {
          window.location.href = 'login.php';
        } else {
          console.log(response);
        }

      },
      error:function(err){
        alert(err);
      }

    });

  });
 



 });
