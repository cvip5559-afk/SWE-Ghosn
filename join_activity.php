
<?php
session_start();
require_once 'includes/connection.php';

if (empty($_SESSION['user_id'])) {
    exit("Not logged in");
}

if (($_SESSION['role'] ?? '') !== 'volunteer') {
    exit("Only volunteers can join.");
}

$volunteerId = $_SESSION['user_id'];
$reportId = $_POST['report_id'] ?? '';

if (!$reportId) {
    exit("Missing report ID");
}

/* CHECK REPORT */
$stmt = $conn->prepare("
    SELECT Status
    FROM report
    WHERE ReportID = ?
    LIMIT 1
");

$stmt->bind_param("s", $reportId);
$stmt->execute();

$result = $stmt->get_result();
$report = $result->fetch_assoc();

$stmt->close();

if (!$report) {
    exit("Report not found");
}

if (strtolower($report['Status']) === 'resolved') {
    exit("This report is already resolved.");
}

/* PREVENT DUPLICATE JOIN */
$check = $conn->prepare("
    SELECT Activity_ID
    FROM activity
    WHERE Volunteer_ID = ? AND Report_ID = ?
    LIMIT 1
");

$check->bind_param("ss", $volunteerId, $reportId);
$check->execute();

$exists = $check->get_result();

if ($exists->num_rows > 0) {
    exit("You already joined this report.");
}

$check->close();

/* CREATE ACTIVITY */
$activityId = 'ACT-' . uniqid();

$insert = $conn->prepare("
    INSERT INTO activity
    (Activity_ID, Volunteer_ID, Report_ID, Status)
    VALUES (?, ?, ?, 'Pending')
");

$insert->bind_param(
    "sss",
    $activityId,
    $volunteerId,
    $reportId
);

if ($insert->execute()) {

    /* update report status */
    $update = $conn->prepare("
        UPDATE report
        SET Status = 'In Progress'
        WHERE ReportID = ?
    ");

    $update->bind_param("s", $reportId);
    $update->execute();
    $update->close();

    header("Location: volunteerProfile.php");
    exit;

} else {
    echo "Error joining activity.";
}
?>
```
