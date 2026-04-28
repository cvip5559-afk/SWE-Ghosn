<?php
session_start();
require_once 'includes/connection.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reportId = $_POST['report_id'] ?? '';

    if ($reportId === '') {
        header('Location: residentProfile.php');
        exit;
    }

    $stmt = $conn->prepare("
        SELECT Status
        FROM report
        WHERE ReportID = ? AND resident_ID = ?
    ");
    $stmt->bind_param("ss", $reportId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $report = $result->fetch_assoc();
    $stmt->close();

    if (!$report) {
        header('Location: residentProfile.php');
        exit;
    }

    if (strtolower($report['Status']) !== 'pending') {
        echo "<script>
            alert('You can only delete reports with Pending status.');
            window.location.href = 'residentProfile.php';
        </script>";
        exit;
    }

    $stmt2 = $conn->prepare("
        DELETE FROM report
        WHERE ReportID = ? AND resident_ID = ?
    ");
    $stmt2->bind_param("ss", $reportId, $userId);

    if ($stmt2->execute()) {
        header('Location: residentProfile.php');
        exit;
    } else {
        echo 'Error deleting report.';
    }

    $stmt2->close();
}
?>