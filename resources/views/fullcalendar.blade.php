
<!DOCTYPE html>
<html>
<head>
    <title>Event Calendar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
  
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
    .calendar-full-block .fc-center h2 {
        font-size:18px;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
    .calendar-full-block .title-block {
        text-align: center;
        font-size: 32px;
        font-weight: 600;
        margin: 24px 0 24px 0;
    }
     .calendar-full-block .fc-button-group {
        display: flex;
        gap: 6px;
        }
    .calendar-full-block .fc-button-group button {
        background: rgb(25, 25, 25);
        border:0;
        color: #fff;
        border-radius: 26px;
    }
    .calendar-full-block .fc-prev-button,
    .calendar-full-block .fc-next-button {
        height: 34px;
        width: 34px;
        border-radius: 50px;
        padding:0;
    }
    .calendar-full-block .fc button .fc-icon{
        top: -1px;
        margin: 0;
    }
     .calendar-full-block  .fc-icon-left-single-arrow:after,
     .calendar-full-block  .fc-icon-right-single-arrow:after {
        top:0
    }
    .calendar-full-block .fc th,
    .calendar-full-block .fc td {
        border-style: dashed;
    }

    .calendar-full-block .fc-day:nth-child(even) {
        background-color: #FDFDFD;
    }
    .calendar-full-block .fc-day:nth-child(odd) {
        background-color: #FDFDFD;
    }
    .calendar-full-block .fc-event, .fc-event-dot {
        background-color: #d4e8f9;
        border: 0;
        border-radius: 6px;
        padding: 4px;
    }
    .calendar-full-block .fc-toolbar .fc-left {
       display: flex;
        align-items: center; 
    }
    .calendar-full-block .fc-today-button{
        border-radius: 26px;
        background: #20B2AA;
        color: #fff;
    }
     .calendar-full-block .fc-day-header {
        padding: 8px 4px;
    }
     .calendar-full-block  .fc-widget-content .fc-scroller {
        max-height: calc(100vh - 200px) !important;
    }
    </style>
</head>
<body>
  
<div class="container">
    <div class="calendar-full-block">
        <h2 class="title-block">Calendar</h2>
        <div id='calendar'></div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addEventForm">
          <div class="form-group">
            <label for="eventTitle">Event Title:</label>
            <input type="text" class="form-control" id="eventTitle" name="eventTitle">
          </div>

          <div class="form-group">
            <label for="eventStart">Start Date and Time:</label>
            <input type="datetime-local" class="form-control" id="eventStart" name="eventStart">
        </div>
        <div class="form-group">
            <label for="eventEnd">End Date and Time:</label>
            <input type="datetime-local" class="form-control" id="eventEnd" name="eventEnd">
        </div>

          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Event Confirmation Modal -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" role="dialog" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteEventModalLabel">Delete Event</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this event?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="deleteEventButton">Delete</button>
      </div>
    </div>
  </div>
</div>
   
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {
   
    var SITEURL = "{{ url('/') }}";

    // Initialize FullCalendar
    var calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek'
        },
        editable: true,
        events: SITEURL + "/fullcalender",
        displayEventTime: false,
        selectable: true,
        selectHelper: true,
        defaultView: 'agendaWeek', // Show the weekly view by default
        minTime: '00:00:00', // Start the calendar at midnight
        maxTime: '24:00:00', // End the calendar at midnight
        allDaySlot: false, // Disable the "all day" slot
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },
        select: function (start, end, allDay) {
            $('#addEventModal').modal('show');
            var startDate = moment(start).format('YYYY-MM-DD HH:mm:ss');
            var endDate = moment(end).format('YYYY-MM-DD HH:mm:ss');
            $('#eventStart').val(startDate);
            $('#eventEnd').val(endDate);
        },
        eventClick: function (event) {
            $('#deleteEventModal').modal('show');
            $('#deleteEventButton').data('event', event);
        }
    });

    // Submit Add Event Form
    $('#addEventForm').submit(function (e) {
        e.preventDefault();
        var title = $('#eventTitle').val();
        var start = $('#eventStart').val();
        var end = $('#eventEnd').val();

        $.ajax({
            url: SITEURL + "/fullcalenderAjax",
            data: {
                title: title,
                start: start,
                end: end,
                type: 'add'
            },
            type: "POST",
            success: function (data) {
                displayMessage("Event Created Successfully");
                calendar.fullCalendar('refetchEvents');
                $('#addEventModal').modal('hide');
            }
        });
    });

    // Delete Event
    $('#deleteEventButton').click(function () {
        var event = $(this).data('event');
        $.ajax({
            type: "POST",
            url: SITEURL + '/fullcalenderAjax',
            data: {
                id: event.id,
                type: 'delete'
            },
            success: function (response) {
                displayMessage("Event Deleted Successfully");
                calendar.fullCalendar('removeEvents', event.id);
                $('#deleteEventModal').modal('hide');
            }
        });
    });

    // Display toastr message
    function displayMessage(message) {
        toastr.success(message, 'Event');
    } 
});

</script>
  
</body>
</html>

