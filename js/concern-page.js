$(document).ready(function(){
    // Cache the jQuery objects for better performance
    var anonymous_user = $('#anon_cb');
    var student_name = $('#student_name');
    var student_email = $('#student_email');
    var student_year = $('#student_year');
    var student_section = $('#student_section');

    var concern_category = $('#categorySelect');
    var concern_title = $('#concern_title');
    var concern_body = $('#concern_body');

    // Function to enable or disable form fields based on checkbox state
    anonymous_user.on('change', function() {

        if (anonymous_user.is(':checked')) {
            student_name.prop('disabled', true);
            // student_email.prop('disabled', true);
            
            student_year.prop('disabled', true);
            student_section.prop('disabled', true);

            student_name.val('');
            student_email.val('');
            student_year.val($('#student_year option:first').val());
            student_section.val($('#student_section option:first').val());


        } else {
            student_name.prop('disabled', false);
            student_email.prop('disabled', false);
            student_year.prop('disabled', false);
            student_section.prop('disabled', false);
        }

    }); 

    // Save Student Concern
    $('#BTNSave').on("click", function() {

        var anonymous_user = $('#anon_cb').val();
        var student_name = $('#student_name').val();
        var student_email = $('#student_email').val();
        var student_year = $('#student_year').val();
        var student_section = $('#student_section').val();

        var concern_category = $('#categorySelect').val();
        var concern_title = $('#concern_title').val();
        var concern_body = $('#concern_body').val();

        if (concern_category === "") {
            alert("Do not forget to select a category for concerns.");
        } 
        else if (concern_title === "") {
            alert("Please create a title for your concerns so that we can address you as quickly.");
        } 
        else if (concern_body === "") {
            alert("Please send a valid concern.");
        }
        else if (student_email === "") {
            alert("Please enter a valid email.");
        }
        
        else {
            var concernData;

            var anonymous_user = $('#anon_cb');
            var student_name = $('#student_name').val();
            var student_email = $('#student_email').val();
            var student_year = $('#student_year').val();
            var student_section = $('#student_section').val();

            var concern_category = $('#categorySelect').val();
            var concern_title = $('#concern_title').val();
            var concern_body = $('#concern_body').val();

            if (anonymous_user.is(':checked')) {
                
                concernData = {
                    tagged: "saveStudentConcerns",
                    fullname: "Anonymous",
                    email: student_email,
                    yearlevel: "Anonymous",
                    section: "Anonymous",
                    concernCategory: concern_category,
                    concernTitle: concern_title,
                    concernBody: concern_body,
                }
            }
            else {
        
                concernData = {
                    tagged: "saveStudentConcerns",
                    fullname: student_name,
                    email: student_email,
                    yearlevel: student_year,
                    section: student_section,
                    concernCategory: concern_category,
                    concernTitle: concern_title,
                    concernBody: concern_body,
                }
            }
        
            $.ajax({
                url: 'php/Concerns_BE.php',
                method: 'POST',
                data: concernData,
                success: function(data){
                    console.log(data);
                    alert(data);
        
                },
                error:function(err){
                    console.log(err);
                    alert("Failed sending your concern :(");
                },
            });
        }   


        
        
    });
    
    $('#BTNBack').on("click", function() {
        window.history.back();
    });
    

});


// var ConcernCategory = $('#BTNcategorySelect');
// var SelectCategory = $('#categorySelect');

// ConcernCategory.on('click', function() {
//     SelectCategory.css('background-color', "red");
//     SelectCategory.val(2).trigger('change');
// });


