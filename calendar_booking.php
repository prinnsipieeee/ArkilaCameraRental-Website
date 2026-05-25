<style>
.body {
    font-family: Arial, sans-serif;
    padding: 20px;
}

.calendar {
    width: 100%;
    height: 600px;
    }

.availabilityModal, .bookingModal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background: #f0f0f0;
    border: 1px solid #ccc;
    z-index: 1000;
}

.availabilityModal {
    width: 400px;
}

.availabilityResult {
    margin-top: 15px;
}

.bookingModal form {
    display: flex;
    flex-direction: column;
}

.bookingModal label {
    margin-top: 10px;
}

.bookingModal input, .bookingModal select {
    margin-top: 5px;
    padding: 5px;
}

.bookingModal button {
    margin-top: 15px;
    padding: 10px;
}

</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Booking Calendar</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.0/main.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.0/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <h1>Camera Booking Calendar</h1>
    <div id="calendar"></div>

            <script src="script.js">
                document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            // Initialize the calendar using FullCalendar library
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                events: 'load_bookings.php', // Load booking data
                dateClick: function (info) {
                    checkAvailability(info.dateStr);
                }
            });

            calendar.render();

            // Check camera availability
            function checkAvailability(date) {
                $.ajax({
                    url: 'check_availability.php',
                    method: 'POST',
                    data: { booking_date: date },
                    success: function (response) {
                        $('#availabilityResult').html(response);
                        $('#availabilityModal').show();
                    }
                });
            }

            // Handle form submission for booking
            document.getElementById('bookingForm').addEventListener('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'submit_booking.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        alert(response);
                        calendar.refetchEvents(); // Refresh calendar events
                        $('#bookingModal').hide();
                    }
                });
            });
        });
            </script>

    <!-- Availability Modal -->
    <div id="availabilityModal" style="display: none;">
        <h2>Check Camera Availability</h2>
        <div id="availabilityResult"></div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" style="display: none;">
        <h2>Book a Camera</h2>
        <form id="bookingForm">
            <label for="camera_model">Select Camera Model:</label>
            <select id="camera_model" name="camera_model"></select>
            
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required><br>
            
            <label for="booking_date">Date:</label>
            <input type="date" id="booking_date" name="booking_date" required><br>
            
            <label for="booking_time">Time:</label>
            <input type="time" id="booking_time" name="booking_time" required><br>
            
            <button type="submit">Book Now</button>
        </form>
    </div>
</body>
</html>
