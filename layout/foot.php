<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!-- Ensure jQuery is loaded first -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chartsjs/Chart.min.js"></script>
<script src="assets/js/dashboard-charts.js"></script>
<script src="assets/js/script.js"></script>
<script src="assets/js/form-validator.js"></script>
<script src="assets/vendor/airdatepicker/js/datepicker.min.js"></script>
<script src="assets/vendor/airdatepicker/js/i18n/datepicker.en.js"></script>
<script src="assets/vendor/mdtimepicker/mdtimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

<script type="text/javascript">
    // Initiate time picker
    mdtimepicker('.timepicker', { format: 'h:mm tt', hourPadding: 'true' });

    // Initiate date picker
    document.addEventListener("DOMContentLoaded", function() {
        new AirDatepicker('.datepicker-here', {
            // Options for the datepicker
            language: 'en',
            dateFormat: 'yyyy-mm-dd'
        });
    });
</script>
</body>
</html>
