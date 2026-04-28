<?php
session_start();
require_once 'includes/connection.php';

function showAlert($message) {
    echo "<script>
        alert('$message');
        window.history.back();
    </script>";
    exit;
}

if (empty($_SESSION['user_id'])) {
    showAlert('Please login first');
}

$userId = $_SESSION['user_id'];
$reportId = $_POST['report_id'] ?? '';

if ($reportId === '') {
    showAlert('Missing report ID');
}

/* check volunteer */
$stmt = $conn->prepare("
    SELECT Volunteer_ID
    FROM volunteer
    WHERE Volunteer_ID = ?
");
$stmt->bind_param("s", $userId);
$stmt->execute();
$volunteerResult = $stmt->get_result();

if ($volunteerResult->num_rows === 0) {
    showAlert('Only volunteers can volunteer');
}
$stmt->close();

/* get existing activity for this report */
$stmt = $conn->prepare("
    SELECT Activity_ID
    FROM activity
    WHERE Report_ID = ?
");
$stmt->bind_param("s", $reportId);
$stmt->execute();
$activityResult = $stmt->get_result();
$activity = $activityResult->fetch_assoc();
$stmt->close();

if (!$activity) {
    showAlert('No activity exists for this report yet');
}

$activityId = $activity['Activity_ID'];

/* check if volunteer already assigned */
$stmt = $conn->prepare("
    SELECT *
    FROM assign
    WHERE Volunteer_ID = ?
    AND Activity_ID = ?
");
$stmt->bind_param("ss", $userId, $activityId);
$stmt->execute();
$assignResult = $stmt->get_result();

if ($assignResult->num_rows > 0) {
    showAlert('You already joined this activity');
}
$stmt->close();

/* insert assign only */
$stmt = $conn->prepare("
    INSERT INTO assign
    (Volunteer_ID, Activity_ID, ParticipationStatus)
    VALUES (?, ?, 'Assigned')
");
$stmt->bind_param("ss", $userId, $activityId);

if ($stmt->execute()) {
    echo "<script>
        alert('You joined the activity successfully');
        window.location.href='volunteerProfile.php';
    </script>";
} else {
    showAlert('Error joining activity');
}

$stmt->close();
?>