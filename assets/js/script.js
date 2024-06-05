$(document).ready(function() {
    $('#calendar').fullCalendar({
        defaultView: 'agendaDay',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: 'fetch_events.php',
    });
});
