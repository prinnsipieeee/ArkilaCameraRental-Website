<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/fullcalendar.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/main.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: dimgray;
            color: black;
        }
        .container {
            margin-top: 30px;
        }
        .form-container {
            background: whitesmoke;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: blue;
            border: none;
        }
        .btn-primary:hover {
            background-color: dimgray;
        }
        .calendar-container {
            background: whitesmoke;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            padding: 15px;
        }
        #receipt-section {
            border-top: 2px solid black;
            margin-top: 15px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- Calendar Section -->
            <div class="col-lg-6">
                <div class="calendar-container">
                    <div id="calendar"></div>
                    <div class="legend mt-3">
                        <div class="d-flex align-items-center">
                            <div style="width: 15px; height: 15px; background-color: red; margin-right: 10px;"></div>
                            <span style="font-size: 14px;">Booked</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-lg-6">
                <div class="form-container">
                    <h4>Appointment Form</h4>
                    <form id="appointment-form" action="save_appointment.php" method="POST">
                        <!-- Step 1 -->
                        <div id="step-1">
                            <h5>Terms and Conditions</h5>
                            <p>We strongly encourage you to read and understand the terms and conditions before proceeding.</p>
                            <div class="form-check">
                                <input type="checkbox" id="agree-terms" class="form-check-input">
                                <label for="agree-terms" class="form-check-label">I have read and agree to the terms</label>
                            </div>
                            <button type="button" id="next-step" class="btn btn-primary w-100 mt-3">Next</button>
                        </div>

                        <!-- Step 2 -->
                        <div id="step-2" style="display: none;">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="camera-model" class="form-label">Camera Model</label>
                                <select id="camera-model" name="camera_model" class="form-control" required>
                                    <option value="">Select Camera Model</option>
                                    <option value="Canon EOS M3">Canon EOS M3</option>
                                    <option value="Canon EOS 1200D">Canon EOS 1200D</option>
                                    <option value="Canon EOS 1300D">Canon EOS 1300D</option>
                                    <option value="Canon 400D">Canon 400D</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="start-date" class="form-label">Start Date</label>
                                <input type="date" id="start-date" name="start_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="end-date" class="form-label">End Date</label>
                                <input type="date" id="end-date" name="end_date" class="form-control" required>
                            </div>
                            <button type="button" id="generate-receipt" class="btn btn-primary w-100">Generate Receipt</button>

                            <!-- Receipt Section -->
                            <div id="receipt-section" style="display: none;">
                                <h5>Receipt</h5>
                                <div id="receipt-details"></div>
                                <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const nextStepButton = document.getElementById('next-step');
        const agreeTermsCheckbox = document.getElementById('agree-terms');
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const generateReceiptButton = document.getElementById('generate-receipt');
        const receiptSection = document.getElementById('receipt-section');
        const receiptDetails = document.getElementById('receipt-details');

        // Next Step (Agreement)
        nextStepButton.addEventListener('click', function () {
            if (agreeTermsCheckbox.checked) {
                step1.style.display = 'none';
                step2.style.display = 'block';
            } else {
                alert('Please agree to the terms and conditions.');
            }
        });

        // Generate Receipt
        generateReceiptButton.addEventListener('click', function () {
            const email = document.getElementById('email').value;
            const cameraModel = document.getElementById('camera-model').value;
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;

            if (email && cameraModel && startDate && endDate) {
                const rentalDays = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 3600 * 24));
                receiptDetails.innerHTML = `
                    <p><strong>Email:</strong> ${email}</p>
                    <p><strong>Camera Model:</strong> ${cameraModel}</p>
                    <p><strong>Rental Days:</strong> ${rentalDays}</p>
                    <p><strong>Start Date:</strong> ${startDate}</p>
                    <p><strong>End Date:</strong> ${endDate}</p>
                `;
                receiptSection.style.display = 'block';
            } else {
                alert('Please fill all fields to generate the receipt.');
            }
        });

        // Initialize Calendar
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                dateClick: function (info) {
                    alert('Selected date: ' + info.dateStr);
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
