<?php
session_start();
require_once 'includes/connection.php';

// تأكد اليوزر مسجل
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reportId = (int)($_POST['report_id'] ?? 0);

    // 1) نجيب حالة الريبورت
    $stmt = $conn->prepare("
        SELECT Status
        FROM report
        WHERE ReportID = ? AND resident_ID = ?
    ");
    $stmt->bind_param("is", $reportId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $report = $result->fetch_assoc();
    $stmt->close();

    // إذا ما لقى التقرير
    if (!$report) {
        header('Location: residentProfile.php');
        exit;
    }

    // 2) نتحقق من الحالة
    if (strtolower($report['Status']) !== 'pending') {

        echo "<script>
            alert('You can only delete reports with Pending status.');
            window.location.href = 'residentProfile.php';
        </script>";
        exit;
    }

    // 3) نحذف
    $stmt2 = $conn->prepare("
        DELETE FROM report
        WHERE ReportID = ? AND resident_ID = ?
    ");
    $stmt2->bind_param("is", $reportId, $userId);

    if ($stmt2->execute()) {
        header('Location: residentProfile.php');
        exit;
    } else {
        echo "Error deleting report.";
    }

    $stmt2->close();
}
?>