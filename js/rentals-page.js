function loadPartialView() {
    $.ajax({
        url: 'php/RentalsPartialView.php',
        type: 'GET',
        data: {
            tagged: 'getItemList'
        },
        success: function (data) {
            $('#itemList').html(data);
        },
        error: function (error) {
            console.error('Error loading partial view:', error);
        }
    });
}


function saveRentals(RentalID) {

    var student_name = $('#student_name').val();
    var student_number = $('#student_number').val();
    var student_email = $('#student_email').val();
    var student_course = $('#student_course').val();
    var student_year = $('#student_year').val();
    var student_section = $('#student_section').val();

    var dateClaim = $('#dateClaim').val();
    var dateReturn = $('#dateReturn').val();

    var checkedItems = $('#itemList .selected-row');
    checkedItems.each(function() {
        var selectedRowCount = checkedItems.length;
        console.log(selectedRowCount);
    });

    // Iterate over each selected row

    var itemidArray     = [];
    var itemNumberArray = [];
    var itemNameArray   = [];
    var itemStatusArray = [];

    var formData = new FormData();

    checkedItems.each(function() {
        var selectedRow = $(this);

        // Find all cells within the selected row
        var cells = selectedRow.find('td');

        // Extract data from each cell
        var itemID = cells.eq(1).text();
        var SKU = cells.eq(2).text();
        var itemName = cells.eq(3).text();
        var status = cells.eq(4).text();

        // Now you can use the extracted data as needed
        console.log('Item ID:', itemID);
        console.log('SKU:', SKU);
        console.log('Item Name:', itemName);
        console.log('Status:', status);

        itemidArray.push(itemID);
        itemNumberArray.push(SKU);
        itemNameArray.push(itemName);
        itemStatusArray.push(status);

        console.log(itemidArray);
        console.log(itemNumberArray);
        console.log(itemNameArray);
        console.log(itemStatusArray);

  

        if(student_name != "" && student_number != "" && student_email != "" && student_year != "" && student_section != "") {
    
            // // Add individual data
            // formData.append('tagged', "saveStudentRentals");
            // formData.append('course', student_course);
            // formData.append('fullname', student_name);
            // formData.append('studentNum', student_number);
            // formData.append('email', student_email);
            // formData.append('yearlevel', student_year);
            // formData.append('section', student_section);
            // formData.append('dateclaim', dateClaim);
            // formData.append('datereturn', dateReturn);
    
            // // Add arrays to FormData
            // for (var i = 0; i < itemidArray.length; i++) {
            //     formData.append('itemidArray[]', itemidArray[i]);
            //     formData.append('itemNumberArray[]', itemNumberArray[i]);
            //     formData.append('itemNameArray[]', itemNameArray[i]);
            //     formData.append('itemStatusArray[]', itemStatusArray[i]);
            // }
        }

    });

    // DISPLAY RENTALS
    console.log("RentalID:", RentalID);
    $('#rentalID').val(RentalID);
    
    console.log("student_name:", student_name);
    $('#rentFullname').val(student_name);
    
    console.log("student_number:", student_number);
    $('#rentStudentNumber').val(student_number);
    
    console.log("student_email:", student_email);
    $('#rentEmail').val(student_email);

    console.log("student_course:", student_course);
    $('#rentCourse').val('BSCOE');
    
    
    console.log("student_year:", student_year);
    $('#rentYearLevel').val(student_year);
    
    console.log("student_section:", student_section);
    $('#rentSection').val(student_section);
    
    console.log("dateClaim:", dateClaim);
    $('#rentClaim').val(dateClaim);
    
    console.log("dateReturn:", dateReturn);
    $('#rentReturn').val(dateReturn);

    var tableBody = $('#rentItems tbody');
    tableBody.empty(); // Clear existing rows

    for (var i = 0; i < itemidArray.length; i++) {
        // Create a new table row
        var newRow = $('<tr>');

        // Create and append table cells for SKU, Item, and Status
        var skuCell = $('<td>').text(itemNumberArray[i]);
        var itemCell = $('<td>').text(itemNameArray[i]);
        var statusCell = $('<td>').text(itemStatusArray[i]);

        newRow.append(skuCell, itemCell, statusCell);

        // Append the new row to the table body
        tableBody.append(newRow);
    }

    $('#BTNConfirmSave').on('click', function() {
        $('#ConfirmSaveModal').modal('hide');

        
            $.ajax({
                url: 'php/Rentals_BE.php',
                type: 'POST',
                data: {
                    tagged: 'saveStudentRentals',
                    rentalID: RentalID,
                    course: student_course,
                    fullname: student_name,
                    studentNum: student_number,
                    email: student_email,
                    yearlevel: student_year,
                    section: student_section,
                    dateclaim: dateClaim,
                    datereturn: dateReturn,
                    itemidArray: itemidArray,
                    itemNumberArray: itemNumberArray,
                    itemNameArray: itemNameArray,
                    itemStatusArray: itemStatusArray
                },
                success: function (data) {
                    console.log(data);
                    alert(data);
                    location.reload();
                },
                error: function (error) {
                    console.error('Error loading partial view:', error);
                }
            });

       
    });

}



$(document).ready(function(){

    loadPartialView();

    $('#search_item').on('keyup', function(event) {
        
        var searchItem = $('#search_item').val();

        if (event.keyCode === 13) {
            if(searchItem == null || searchItem == " "){
                loadPartialView();
            }
            else {
                $.ajax({
                    url: 'php/RentalsPartialView.php',
                    type: 'GET',
                    data: {
                        tagged: 'getSearchItem',
                        searchItem: searchItem
                    },
                    success: function (data) {
                        $('#itemList').html(data);
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

        }
    
   });


    //FOR ITEM LIST

    $("#itemList").on('change', '.item-cb', function() {
        var selectedItem = $(this);
        var selectedRow = selectedItem.closest('tr');


        if (selectedItem.is(':checked')) {
            selectedRow.addClass("selected-row");

        } else {
            selectedRow.removeClass("selected-row");
        }

    });

   // Save Student Concern
    $('#BTNSave').on("click", function() {

        var student_name = $('#student_name').val();
        var student_number = $('#student_number').val();
        var student_email = $('#student_email').val();
        var student_course = $('#student_course').val();
        var student_year = $('#student_year').val();
        var student_section = $('#student_section').val();

        var dateClaim = $('#dateClaim').val();
        var dateReturn = $('#dateReturn').val();

        var checkedItems = $('#itemList .selected-row');
        

        if(student_name != "" && student_number != "" && student_email != "" && student_course != "" && student_year != "" && student_section != "" && dateClaim != "" && dateReturn != "") {
            
            if(checkedItems.length > 0){

                $.ajax({
                    url: 'php/Rentals_BE.php',
                    type: 'GET',
                    data: {
                        tagged: 'getRentalID'
                    },
                    success: function (data) {
                        console.log('Server Response:', data);
    
                        var rentalID = data.rentalID;
                        console.log('Rental ID:', rentalID);
    
                        RentalID = rentalID.trim();
                        $('#ConfirmSaveModal').modal('show');
                        saveRentals(RentalID);
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });

            }
            else {
                alert("You can't proceed without checking an Item your want to rent.");
            }
            
        }
        else 
        {
            alert("Please Complete your Details");
        }

    });
    
    $('#BTNBack').on("click", function() {
        window.history.back();
    });


});

  



//Generate Rental ID
//Date they sent their rentals



