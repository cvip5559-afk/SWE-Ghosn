<?php
session_start();
require_once 'includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$userId = $_SESSION['user_id'];
$reportId = $_POST['report_id'] ?? '';

if (empty($reportId)) {
    die("Missing report ID");
}

/* check if user is volunteer */

$checkVolunteer = mysqli_query($conn, "
SELECT *
FROM volunteer
WHERE Volunteer_ID = '$userId'
");

if (mysqli_num_rows($checkVolunteer) == 0) {
    die("Only volunteers can volunteer");
}

/* prevent duplicate volunteer */

$checkExisting = mysqli_query($conn, "
SELECT *
FROM activity
WHERE Volunteer_ID = '$userId'
AND Report_ID = '$reportId'
");

if (mysqli_num_rows($checkExisting) > 0) {
    die("Already volunteered");
}

/* create activity */

$activityId = uniqid("ACT_");
$date = date("Y-m-d");

mysqli_query($conn, "
INSERT INTO activity
(Activity_ID, Volunteer_ID, Report_ID, Status, ActivityDate)

VALUES

('$activityId', '$userId', '$reportId', 'Pending', '$date')
");

/* insert into assign */

mysqli_query($conn, "
INSERT INTO assign
(Activity_ID, Volunteer_ID, ParticipationStatus)

VALUES

('$activityId', '$userId', 'Assigned')
");

header("Location: volunteerProfile.php");
exit;
?>
