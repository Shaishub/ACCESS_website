//VARIABLES

var DateTimeToday;

function getListOfEvents() {
    $.ajax({
        url:    'php/EventsListPV.php',
        type:   'GET',
        data:   {
            tagged: 'getEventsList',
            dateToday: DateTimeToday
        },
        success: function(res){
            $('#EventsList').html(res);
            
            $('#EventsList .event-status input[type=button]').each(function() {
                var EventStatus = $(this);

                if(EventStatus.hasClass('ongoing')){
                    EventStatus.val("ONGOING");
                }
                else if (EventStatus.hasClass('upcoming')){
                    EventStatus.val("UPCOMING");
                }
            });
        },
        error: function(err){

        }, 
    });

}

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

function checkUpcomingEvents() {
    $('#EventsList .event-status').each(function () {
        var $statusDiv = $(this);
        // Check if the class contains 'upcoming' or 'ongoing'
        
        if ($statusDiv.hasClass('upcoming')) {
            $statusDiv.find('input').val("UPCOMING");
            console.log("upcoming");
        } 
        else if ($statusDiv.hasClass('ongoing')) {
            $statusDiv.find('input').val("ONGOING");
            console.log("ongoing");
        }
    });
}

function filterEventsList(EventTag){
    $.ajax({
        url:    'php/EventsListPV.php',
        type:   'GET',
        data:   {
            tagged: 'filterEventsList',
            EventTag: EventTag
        },
        success: function(res){
            $('#EventsList').html(res);

            $('#EventsList .event-status input[type=button]').each(function() {
                var EventStatus = $(this);

                if(EventStatus.hasClass('ongoing')){
                    EventStatus.val("ONGOING");
                }
                else if (EventStatus.hasClass('upcoming')){
                    EventStatus.val("UPCOMING");
                }
            });

           

        },
        error: function(err){},
    });
}

function selectedEvent(EventDetials){
    $.ajax({
        url:    'php/EventsListPV.php',
        type:   'GET',
        data:   {
            tagged: 'selectedEvent',
            EventTag: EventTag
        },
        success: function(res){
            $('#EventsList').html(res);

            // $('#EventsList .event-status input[type=button]').each(function() {
            //     var EventStatus = $(this);

            //     if(EventStatus.hasClass('ongoing')){
            //         EventStatus.val("ONGOING");
            //     }
            //     else if (EventStatus.hasClass('upcoming')){
            //         EventStatus.val("UPCOMING");
            //     }
            // });
        },
        error: function(err){},
    });
}

$(document).ready(function() {
    dateToday()
    getListOfEvents();

    $(".events-tag").on('click', function(){

        var EventTag = $(this).val();
        console.log(EventTag);
        filterEventsList(EventTag);
    });

    $("#EventsList").on('click', '.event-title', function () {
        var SelectedEvent = $(this);
        var SelectedEventCont = SelectedEvent.closest(".event-container");
    
        SelectedEventCont.addClass('selected-event');
        
        var EventTitle = SelectedEvent.text();
        var EventBody = SelectedEventCont.find(".event-desc p").text();  // Corrected this line
        var EventAuthor = SelectedEventCont.find(".event-author span").text();  // Corrected this line
        var EventPosted = SelectedEventCont.find(".event-dateposted span").text();  // Corrected this line
        var EventStatus = SelectedEventCont.find(".event-status input").val();  // Corrected this line
        var EventPhoto = SelectedEventCont.find(".event-photo img").attr('src');  // Corrected this line
        var EventDateStart = SelectedEventCont.find(".eventDateStart").val();
        var EventDateEnd = SelectedEventCont.find(".eventDateEnd").val();

        var EventLink1 = SelectedEventCont.find(".event-link1").val();
        var EventLink2 = SelectedEventCont.find(".event-link2").val();
        var EventLink3 = SelectedEventCont.find(".event-link3").val();
        var EventLink4 = SelectedEventCont.find(".event-link4").val();
        var EventLink5 = SelectedEventCont.find(".event-link5").val();

    
        console.log(EventTitle);
        console.log(EventBody);
        console.log(EventAuthor);
        console.log(EventPosted);
        console.log(EventStatus);
        console.log(EventPhoto);
        console.log(EventDateStart);
        console.log(EventDateEnd);

        console.log(EventLink1);
        console.log(EventLink2);
        console.log(EventLink3);
        console.log(EventLink4);
        console.log(EventLink5);

        $('#ViewEventModal').modal('show');

        $('#EventTitle').text(EventTitle);
        $('#EventBody').text(EventBody);
        $('#EventAuthor').text(EventAuthor);
        $('#EventPosted').text(EventPosted);
        $('#EventStatus').text(EventStatus);
        $('#EventStatus').addClass(EventStatus);
        $('#EventPhotoContainer').attr('src', EventPhoto);

        $('#EventLink1').attr('href', EventLink1);
        $('#EventLink1').text(EventLink1);
        $('#EventLink2').attr('href', EventLink2);
        $('#EventLink2').text(EventLink2);
        $('#EventLink3').attr('href', EventLink3);
        $('#EventLink3').text(EventLink3);
        $('#EventLink4').attr('href', EventLink4);
        $('#EventLink4').text(EventLink4);
        $('#EventLink5').attr('href', EventLink5);
        $('#EventLink5').text(EventLink5);

    });
    

    
});