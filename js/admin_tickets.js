//Variables

var DateTimeToday = null;

function dateToday() {
    const today = new Date();

    // Get the current date components
    const year = today.getFullYear();
    const month = today.getMonth() + 1; // Note: Months are zero-based, so we add 1
    const day = today.getDate();

    // Get the current time components
    const hours = today.getHours();
    const minutes = today.getMinutes();
    const seconds = today.getSeconds();

    // Format the date and time as a string (you can adjust the format as needed)
    DateTimeToday = `${year}-${month < 10 ? '0' + month : month}-${day < 10 ? '0' + day : day} ${hours}:${minutes}:${seconds}`;

    console.log(DateTimeToday);
}

//ONGOING CONCERNS TABLE
function viewStudentConcerns() {
    $.ajax({
        url: 'php/ticketsConcern_PV.php',
        type: 'GET',
        data: {
            tagged: 'viewStudentConcerns',
        },
        success: function (data) {
            $('#concernsTable').html(data);

            var TableRow = $('#concernsTable tbody td label');
            var PendingTableRow = $('#concernsTable tbody td label.Pending');
            var InProgressTableRow = $('#concernsTable tbody td label.InProgress');
            var SolvedTableRow = $('#concernsTable tbody td label.Solved');
            
            TableRow.each(function() {
                var thisTableRow = $(this);

                if(thisTableRow.hasClass('Pending')){
                    PendingTableRow.addClass('badge-danger');
                }
                else if(thisTableRow.hasClass('Solved')){
                    SolvedTableRow.addClass('badge-warning');
                }
            });

            var tableRow =  $('#concernsTable tbody tr').length;
            $('#ongoingConcernsCount').text(tableRow);

        },
        error: function (error) {
            console.error(error);
        }
    });
}

//ONGOING RENTALS TABLE
function viewStudentRentals() {
    $.ajax({
        url: 'php/AdminTickets_BE.php',
        type: 'GET',
        data: {
            tagged: 'viewStudentRentals',
        },
        success: function (data) {
            //console.log(data);
            $('#rentalsTable table tbody').html(data);

            var TableRow = $('#rentalsTable tbody td label');
            var RentedTableRow = $('#rentalsTable tbody td label.Ongoing');
            var ReservedTableRow = $('#rentalsTable tbody td label.Pending');
            var AvailableTableRow = $('#rentalsTable tbody td label.Completed');
            var NotAvailableTableRow = $('#rentalsTable tbody td label.Cancelled');
            
            TableRow.each(function() {
                var thisTableRow = $(this);

                if(thisTableRow.hasClass('Ongoing')){
                    RentedTableRow.addClass('badge-danger');
                }
                if(thisTableRow.hasClass('Pending')){
                    ReservedTableRow.addClass('badge-warning');
                }
                if(thisTableRow.hasClass('Completed')){
                    AvailableTableRow.addClass('badge-info');
                }
                if(thisTableRow.hasClass('Cancelled')){
                    NotAvailableTableRow.addClass('badge-Secondary');
                }
            });

            var tableRow =  $('#rentalsTable tbody tr').length;
            $('#ongoingRentalsCount').text(tableRow);

        },
        error: function (error) {
            console.error(error);
        }
    });
}

// Student Rentals Details View 
function getStudentRentalsDetails(rentalId) {
    
    $.ajax({
        url: 'php/AdminTickets_BE.php',
        type: 'GET',
        data: {
            tagged: 'getStudentRentalsDetails',
            RentalID : rentalId
        },
        success: function (data) {
            //console.log(data);
            $('#RentalDetials').html(data);

            var rows = $('#RentalDetials table tbody tr'); 

            // Add event listener to handle dropdown item selection
            rows.on('click', '.itemStatus', function() {

                var thisButton = $(this);
                var thisRow = thisButton.closest('tr');
                var dropdown = thisButton.next('.dropdown-menu');

                dropdown.toggle();

                var isExpanded = dropdown.is(':visible');
                thisButton.attr('aria-expanded', isExpanded);

                thisRow.on('click', '.dropdown-item', function() {
                    var selectedStatus = $(this).text();
                    thisButton.text(selectedStatus);
                    dropdown.hide();
                    thisButton.attr('aria-expanded', false);
                });
            
                // Optionally, remove the event listener after use
                thisRow.one('click', function() {
                    thisRow.off('click', '.dropdown-item');
                });
            });


        },
        error: function (error) {
            console.error(error);
        }
    });

}


function SaveRentalStatus() {

    var rows = $('#RentalDetials table tbody tr'); 

    var sessionUsername = $('#session_username').val();
    var rentalId = $('#viewRentalsModal .modal-title').text().trim();

    var itemNumList = [];
    var itemStatusList = [];
    var itemDateRentedList = [];
    var itemDateReturnedList = [];
    var itemDateList = [];

    rows.each(function() {
        var itemNumber = $(this).find('td:eq(0)').text().trim();
        var status = $(this).find('.itemStatus').text().trim();
        var itemDate = $(this).find('.itemDate').val();
        

        itemNumList.push(itemNumber);
        itemStatusList.push(status);
        itemDateList.push(itemDate);

    });

    $.ajax({
        url: 'php/AdminTickets_BE.php',
        type: 'POST',
        data: {
            tagged: 'updateItemStatus',
            RentalID: rentalId,
            itemNumber: itemNumList,
            itemStatus: itemStatusList,
            itemDateRentedList: itemDateRentedList,
            itemDateReturnedList: itemDateReturnedList,
            itemDate: itemDateList,
            User: sessionUsername
        },
        success: function(res){
            alert(res);
            viewStudentRentals();
            getCompletedRentals();

        },
        error: function(err){
            console.log(err);
        },     
    });

    itemNumList.length = 0;
    itemStatusList.length = 0;
    itemDateRentedList.length = 0;
    itemDateReturnedList.length = 0;
    itemDateList.length = 0;

}

// function returnedRentals(rentalId) {
//     $.ajax({
//         url: 'php/ticketsRentals_PV.php',
//         type: 'POST',
//         data: {
//             tagged: 'returnedRentals',
//             RentalID : rentalId
//         },
//         success: function (data) {
//             console.log(data);
//             //$('#completedConcernsTable').html(data);

//         },
//         error: function (error) {
//             console.error(error);
//         }
//     });
// }


//Deleted Rentals
function deletedRentals(rentalId) {
    $.ajax({
        url: 'php/ticketsRentals_PV.php',
        type: 'POST',
        data: {
            tagged: 'deletedRentals',
            RentalID : rentalId
        },
        success: function (data) {
            console.log(data);
            //$('#completedConcernsTable').html(data);

        },
        error: function (error) {
            console.error(error);
        }
    });
}

//COMPLETED RENTALS
function getCompletedRentals() {
    $.ajax({
        url: 'php/AdminTickets_BE.php',
        type: 'GET',
        data: {
            tagged: 'getCompletedRentals',
            // ConcernID : concernID
        },
        success: function (data) {
            //console.log(data);
            $('#completedRentalsTable').html(data);

            var TableRow = $('#completedRentalsTable tbody td label');
            var CompletedTableRow = $('#completedRentalsTable tbody td label.Completed');
            var DeletedTableRow = $('#completedRentalsTable tbody td label.Deleted');
                
            TableRow.each(function() {
                var thisTableRow = $(this);

                if(thisTableRow.hasClass('Completed')){
                    CompletedTableRow.addClass('badge-success');
                }
                else if(thisTableRow.hasClass('Deleted')){
                    DeletedTableRow.addClass('badge-danger');
                }
            });

            var tableRow =  $('#completedRentalsTable tbody tr').length;
            $('#completedRentalsCount').text(tableRow);


        },
        error: function (error) {
            console.error(error);
        }
    });
}






function getStudentConcernsDetails(concernID) {
    $.ajax({
        url: 'php/getStudentConcernsDetails.php',
        type: 'GET',
        data: {
            tagged: 'getStudentConcernsDetails',
            ConcernID : concernID
        },
        success: function (data) {
            //console.log(data);
            $('#ConcernsDetials').html(data);

        },
        error: function (error) {
            console.error(error);
        }
    });
}

function solvedConcerns(concernID) {
    $.ajax({
        url: 'php/ticketsConcern_PV.php',
        type: 'POST',
        data: {
            tagged: 'solvedConcerns',
            ConcernID : concernID
        },
        success: function (data) {
            console.log(data);
            $('#completedConcernsTable').html(data);

        },
        error: function (error) {
            //getSolvedConcerns();
            console.error(error);
        }
    });
}

function deletedConcerns(concernID) {
    $.ajax({
        url: 'php/ticketsConcern_PV.php',
        type: 'POST',
        data: {
            tagged: 'deletedConcerns',
            ConcernID : concernID
        },
        success: function (data) {
            console.log(data);
            //$('#completedConcernsTable').html(data);

        },
        error: function (error) {
            console.error(error);
        }
    });
}

//COMPLETED CONCERNS
function getSolvedConcerns() {
    $.ajax({
        url: 'php/ticketsConcern_PV.php',
        type: 'GET',
        data: {
            tagged: 'getSolvedConcerns',
            // ConcernID : concernID
        },
        success: function (data) {
            //console.log(data);
            $('#completedConcernsTable').html(data);

            var TableRow = $('#completedConcernsTable tbody td label');
            var CompletedTableRow = $('#completedConcernsTable tbody td label.Solved');
            var DeletedTableRow = $('#completedConcernsTable tbody td label.Deleted');
                
            TableRow.each(function() {
                var thisTableRow = $(this);

                if(thisTableRow.hasClass('Solved')){
                    CompletedTableRow.addClass('badge-success');
                }
                else if(thisTableRow.hasClass('Deleted')){
                    DeletedTableRow.addClass('badge-danger');
                }
            });

            var tableRow =  $('#completedConcernsTable tbody tr').length;
            $('#completedConcernsCount').text(tableRow);

        },
        error: function (error) {
            console.error(error);
        }
    });
}






$(document).ready(function(){
    
    //$('#concernsTable').css('background', 'black');
    dateToday();
    viewStudentConcerns();
    viewStudentRentals();
    getSolvedConcerns();
    getCompletedRentals();



    // CONCERNS
    $('#concernsTable').on('click', '.btnView', function() {
        var row = $(this).closest('tr'); 
        var concernID = row.find('td:eq(0)').text().trim(); 

        $('#viewConcernsModal .modal-title').text(concernID);

        $.ajax({
            url: 'php/AdminTickets_BE.php',
            type: 'GET',
            data: {
                tagged: 'getStudentConcernsDetails',
                ConcernID : concernID
            },
            success: function (data) {
                //console.log(data);
                $('#ConcernsDetials').html(data);
    
            },
            error: function (error) {
                console.error(error);
            }
        });

    });

    $('#concernsTable').on('click', '.btnDone', function() {
        var row = $(this).closest('tr'); 
        var concernID = row.find('td:eq(0)').text().trim();

        solvedConcerns(concernID);
        //$('#completedConcernsTable').css('background', 'black');

    });

    $('#concernsTable').on('click', '.btnDelete', function() {
        var row = $(this).closest('tr'); 
        var concernID = row.find('td:eq(0)').text().trim();

        deletedConcerns(concernID)  ;
        //$('#completedConcernsTable').css('background', 'black');

    });

    $('#completedConcernsTable').on('click', 'button', function() {
        var row = $(this).closest('tr'); 
        var concernID = row.find('td:eq(0)').text().trim();

        $('#completedConcernsModal .modal-title').text(concernID);
    
        $.ajax({
            url: 'php/getStudentConcernsDetails.php',
            type: 'GET',
            data: {
                tagged: 'getStudentConcernsDetails',
                ConcernID : concernID
            },
            success: function (data) {
                //console.log(data);
                $('#completedConcernsDetials').html(data);
            },
            error: function (error) {
                console.error(error);
            }
        });

    });









    //RENTALS
    $('#rentalsTable table').on('click', '.btnView', function() {
        var row = $(this).closest('tr'); 
        var rentalId = row.find('td:eq(0)').text().trim(); 

        $('#viewRentalsModal .modal-title').text(rentalId);
    

        getStudentRentalsDetails(rentalId);

    });

    $('#SaveRentalStatus').on('click', function() {
        SaveRentalStatus();
    });

    $('#rentalsTable').on('click', '.btnDone', function() {
        var row = $(this).closest('tr'); 
        var rentalId = row.find('td:eq(0)').text().trim(); 

        $('#viewRentalsModal .modal-title').text(rentalId);
    

        returnedRentals(rentalId);

    });

    $('#rentalsTable').on('click', '.btnDelete', function() {
        var row = $(this).closest('tr'); 
        var rentalId = row.find('td:eq(0)').text().trim(); 

        $('#viewRentalsModal .modal-title').text(rentalId);
    

        returnedRentals(rentalId);

    });
    
   
    $('#completedRentalsTable').on('click', '.btnView', function() {
        var row = $(this).closest('tr'); 
        var rentalId = row.find('td:eq(0)').text().trim();

        $('#completedRentalsTable .modal-title').text(rentalId);
    
        $.ajax({
            url: 'php/AdminTickets_BE.php',
            type: 'GET',
            data: {
                tagged: 'getCompletedRentalsDetails',
                RentalID : rentalId
            },
            success: function (data) {
                console.log(data);
                //$('#viewRentalsModal').html(data);
                $('#completedRentalsModal .modal-body').html(data);
            },
            error: function (error) {
                console.error(error);
            }
        });

    });
    


    //OFFICERS PASSWORD
    
    $('#submitPass').on('click', function() {

      var officersDashboard = $('#oldPassword').val();

      $.ajax({
        url:'php/AdminAccount_BE.php',
        type: 'POST',
        data: { 
          tagged : 'OfficersDashboardVerify',
          ODPass : officersDashboard 
        },
        success: function(res) {
          var u_adminVerify = res.verify;

          if(u_adminVerify == 1){
            window.location.href = "officer-dashboard.php";
          }
          else {
            $('#errorMessage').text("Invalid Password!");
          }
        },
        error: function(err){
          alert(err);

        }
      });

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

