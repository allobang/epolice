<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'connection.php';
require_once 'functions.php';

$userId = $_SESSION['userid'];
$appointment = null;
$isEdit = isset($_GET['action']) && $_GET['action'] === 'edit';

// Fetch existing appointment details if editing
if ($isEdit) {
    $stmt = $conn->prepare("SELECT appointment_date, time_slot FROM appointments WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentDate = $_POST['appointment_date'];
    $timeSlot = $_POST['time_slot'];

    // Check if the slot is still available
    $sql = "SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND time_slot = ? AND user_id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $appointmentDate, $timeSlot, $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count < 30) {
        if ($isEdit) {
            // Update existing appointment
            $sql = "UPDATE appointments SET appointment_date = ?, time_slot = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $appointmentDate, $timeSlot, $userId);
            if ($stmt->execute()) {
                $successMessage = "Appointment successfully updated!";
            } else {
                $errorMessage = "Error updating appointment. Please try again.";
            }
        } else {
            // Book a new appointment
            $sql = "INSERT INTO appointments (user_id, appointment_date, time_slot, status) VALUES (?, ?, ?, 'Scheduled')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iss', $userId, $appointmentDate, $timeSlot);
            if ($stmt->execute()) {
                $successMessage = "Appointment successfully booked!";
            } else {
                $errorMessage = "Error booking appointment. Please try again.";
            }
        }
        $stmt->close();
    } else {
        $errorMessage = "Selected time slot is full. Please choose another.";
    }
}
?>

<!doctype html>
<html lang="en">

<?php include 'layout/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'layout/side.php'; ?>
        <div id="body" class="active">
            <?php include 'layout/nav.php'; ?>
            <div class="content">
                <div class="container">
                    <!-- title -->
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">New</div>
                            <h2 class="page-title"><?= $isEdit ? 'Edit' : 'Book' ?> Appointment</h2>
                        </div>
                    </div>
                    <!-- end title -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="content">
                                    <div class="head">
                                        <h5 class="mb-0">Appointment Booking</h5>
                                        <p class="text-muted">Select your appointment date and time slot.</p>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="newClearance.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                                    </div>
                                    <div class="canvas-wrapper">
                                        <?php if (isset($successMessage)) : ?>
                                            <div class="alert alert-success"><?= $successMessage ?></div>
                                        <?php endif; ?>
                                        <?php if (isset($errorMessage)) : ?>
                                            <div class="alert alert-danger"><?= $errorMessage ?></div>
                                        <?php endif; ?>

                                        <div id="calendar"></div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="appointmentModalLabel">Select Time Slot</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" id="appointment_date" name="appointment_date" value="<?= $appointment['appointment_date'] ?? '' ?>">
                                                            <div class="form-group">
                                                                <label for="time_slot">Time Slot</label>
                                                                <select class="form-control" id="time_slot" name="time_slot" required>
                                                                    <option value="morning" <?= isset($appointment['time_slot']) && $appointment['time_slot'] == 'morning' ? 'selected' : '' ?>>Morning (8:00 AM - 12:00 PM)</option>
                                                                    <option value="afternoon" <?= isset($appointment['time_slot']) && $appointment['time_slot'] == 'afternoon' ? 'selected' : '' ?>>Afternoon (1:00 PM - 5:00 PM)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Book' ?> Appointment</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal -->

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php include 'layout/foot.php'; ?>

    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                selectable: true,
                selectHelper: true,
                select: function(start, end) {
                    const now = moment();
                    if (start.isBefore(now, 'day') || start.day() === 0 || start.day() === 6) {
                        $('#calendar').fullCalendar('unselect');
                        return;
                    }

                    $('#appointmentModal').modal('show');
                    $('#appointment_date').val(start.format('YYYY-MM-DD'));
                },
                dayRender: function(date, cell) {
                    const now = moment();
                    if (date.isBefore(now, 'day') || date.day() === 0 || date.day() === 6) {
                        cell.css("background-color", "#e6e6e6");
                    }
                }
            });

            // Pre-select date if editing an appointment
            <?php if ($isEdit && isset($appointment['appointment_date'])) : ?>
                $('#calendar').fullCalendar('gotoDate', '<?= $appointment['appointment_date'] ?>');
                $('#appointment_date').val('<?= $appointment['appointment_date'] ?>');
                $('#appointmentModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>

</html>
