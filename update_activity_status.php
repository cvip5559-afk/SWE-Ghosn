<?php
session_start();
require_once 'includes/connection.php';

if (empty($_SESSION['user_id'])) {
    exit("Not logged in");
}

if (($_SESSION['role'] ?? '') !== 'volunteer') {
    exit("Not allowed");
}

$volunteerId = $_SESSION['user_id'];
$activityId  = $_POST['activity_id'] ?? '';
$status      = $_POST['status'] ?? '';

if ($activityId === '') {
    exit("Missing activity ID");
}

if (!in_array($status, ['Pending', 'In Progress', 'Completed'])) {
    exit("Invalid status");
}

/* تأكد أن هذا الفولنتير assigned على هذا النشاط + جيب report id */
$stmt = $conn->prepare("
    SELECT a.Report_ID
    FROM assign s
    JOIN activity a ON a.Activity_ID = s.Activity_ID
    WHERE s.Activity_ID = ?
      AND s.Volunteer_ID = ?
    LIMIT 1
");

$stmt->bind_param("ss", $activityId, $volunteerId);
$stmt->execute();
$result = $stmt->get_result();
$activity = $result->fetch_assoc();
$stmt->close();

if (!$activity) {
    exit("Activity not found or not assigned to you");
}

$reportId = $activity['Report_ID'];

$conn->begin_transaction();

try {

    /* تحديث activity بدون Volunteer_ID */
    $stmt2 = $conn->prepare("
        UPDATE activity
        SET Status = ?
        WHERE Activity_ID = ?
    ");
    $stmt2->bind_param("ss", $status, $activityId);
    $stmt2->execute();
    $stmt2->close();

    /* تحديث report */
    $stmt3 = $conn->prepare("
        UPDATE report
        SET Status = ?
        WHERE ReportID = ?
    ");
    $stmt3->bind_param("ss", $status, $reportId);
    $stmt3->execute();
    $stmt3->close();

    $conn->commit();
    echo "Updated";

} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?>