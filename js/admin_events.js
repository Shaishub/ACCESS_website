


$(document).ready(function() {

    $('#SubmitBTN').on('click', function() {

        var eventTitle      = $('#eventTitle').val();
        var eventAuthor     = $('#eventAuthor').val();
        var startDate       = $('#startDate').val();
        var endDate         = $('#endDate').val();
        var eventContent    = $('#eventContent').val();
        var eventLink1      = $('#eventLink1').val();
        var eventLink2      = $('#eventLink2').val();
        var eventLink3      = $('#eventLink3').val();
        var eventLink4      = $('#eventLink4').val();
        var eventLink5      = $('#eventLink5').val();

        var eventData = new FormData();

        if(eventTitle != null && eventAuthor != null && eventContent != null && startDate != null && endDate != null){
            
            // eventData = {
            //     tagged      : 'saveCreatedEvent',
            //     eventTitle  : eventTitle,
            //     eventAuthor : eventAuthor,
            //     startDate   : startDate,
            //     endDate     : endDate,
            //     eventContent: eventContent,
            //     eventLink1  : eventLink1,
            //     eventLink2  : eventLink2,
            //     eventLink3  : eventLink3,
            //     eventLink4  : eventLink4,
            //     eventLink5  : eventLink5
            // }
    
            $.ajax({
                url: 'php/AdminEvent_BE.php',
                type: 'POST',
                data: {
                    tagged      : 'saveCreatedEvent',
                    eventTitle  : eventTitle,
                    eventAuthor : eventAuthor,
                    startDate   : startDate,
                    endDate     : endDate,
                    eventContent: eventContent,
                    eventLink1  : eventLink1,
                    eventLink2  : eventLink2,
                    eventLink3  : eventLink3,
                    eventLink4  : eventLink4,
                    eventLink5  : eventLink5
                },
                successs: function(data){
                    console.log(data);
                },
                error: function(err){
    
                },
            });
        }




        


    });











   



});