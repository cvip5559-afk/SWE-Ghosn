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

$stmt = $conn->prepare("
    SELECT Report_ID
    FROM activity
    WHERE Activity_ID = ? AND Volunteer_ID = ?
    LIMIT 1
");
$stmt->bind_param("ss", $activityId, $volunteerId);
$stmt->execute();
$result = $stmt->get_result();
$activity = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$activity) {
    exit("Activity not found");
}

$reportId = $activity['Report_ID'];

$conn->begin_transaction();

try {
    $stmt2 = $conn->prepare("
        UPDATE activity
        SET Status = ?
        WHERE Activity_ID = ? AND Volunteer_ID = ?
    ");
    $stmt2->bind_param("sss", $status, $activityId, $volunteerId);
    $stmt2->execute();
    $stmt2->close();

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
    echo "Error";
}
?>