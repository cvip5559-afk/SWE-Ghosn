<?php
session_start();
require_once 'includes/connection.php';

// حماية الصفحة
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'resident') {
    header('Location: ghusn_home1.php');
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Resident';
$email    = $_SESSION['email'] ?? '';

// ─────────────────────────────────────────────
// 1) Fetch resident/user info
// ─────────────────────────────────────────────
$profile = [
    'User_name' => $userName,
    'email' => $email,
    'phone' => '',
    'ResidentNeighbourhood' => ''
];

$stmt = $conn->prepare("
    SELECT u.User_name, u.email, u.phone, r.ResidentNeighbourhood
    FROM user u
    JOIN resident r ON r.resident_ID = u.User_ID
    WHERE u.User_ID = ?
    LIMIT 1
");
$stmt->bind_param('s', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $profile = $result->fetch_assoc();
}
$stmt->close();

// ─────────────────────────────────────────────
// 2) Fetch resident reports
// ─────────────────────────────────────────────
$reports = [];

$stmt2 = $conn->prepare("
    SELECT r.ReportID, r.Severity_Level, r.Status, r.Description, r.Title, l.DistrictName, r.photo
    FROM report r
    JOIN location l ON r.LocationID = l.LocationID
    WHERE r.resident_ID = ?
    ORDER BY r.ReportID DESC
");
$stmt2->bind_param('s', $userId);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2) {
    while ($row = $result2->fetch_assoc()) {
        $reports[] = $row;
    }
}
$stmt2->close();

$reportsCount = count($reports);

// أول حرف للأفاتار
$avatarLetter = strtoupper(substr(trim($profile['User_name'] ?? 'R'), 0, 1));

function severityClass($level) {
    $level = (int)$level;
    if ($level >= 4) return 'high';
    if ($level == 3) return 'medium';
    return 'low';
}

function statusClass($status) {
    $status = strtolower(trim($status));
    if ($status === 'resolved') return 'resolved';
    return 'review';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resident Profile</title>
  <link rel="stylesheet" href="shared.css">

  <style>
    body {
      background-color: #ded8c8d0;
    }

    .container {
      width: 85%;
      margin: 100px auto 40px;
    }

    .profile {
      background: rgb(246, 244, 239);
      padding: 50px;
      border-radius: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .user {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .avatar {
      width: 100px;
      height: 100px;
      background: rgb(2, 74, 2);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      font-weight: bold;
    }

    .name {
      font-size: 25px;
      font-weight: bold;
    }

    .info {
      font-size: 16px;
      color: rgb(69, 119, 68);
    }

    .reports-count {
      border: solid 1.5px #8fac8f;
      border-radius: 10px;
      background-color: #e9f6e9;
      padding: 10px;
      width: 125px;
      text-align: center;
    }

    .role {
      font-size: 14px;
      font-weight: bold;
      border: solid 0.1px rgb(53, 123, 167);
      background-color: rgb(53, 123, 167);
      border-radius: 10px;
      padding: 2px 8px;
      color: white;
      text-align: center;
    }

    .reports-count h2 {
      margin: 0;
      color: green;
    }

    .btn {
      background: rgb(3, 55, 3);
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      display: inline-block;
    }

    .btn a {
      color: white;
      text-decoration: none;
    }

    .reports {
      background: rgb(246, 244, 239);
      padding: 20px;
      border-radius: 12px;
    }

    .report {
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 12px;
    }

    .report h4 {
      margin: 0;
    }

    .meta {
      font-size: 13px;
      color: gray;
      margin: 5px 0;
    }

    .tags {
      display: flex;
      gap: 10px;
      margin: 10px 0;
    }

    .tag {
      font-size: 12px;
      padding: 4px 8px;
      border-radius: 10px;
      font-weight: bold;
    }

    .high { background: #b91c1c; color: #ffffff; }
    .medium { background: #b45309; color: #ffffff; }
    .low { background: #15803d; color: #ffffff; }

    .review { background: #dbeafe; color: #1d4ed8; }
    .resolved { background: #d1fae5; color: #047857; }

    .buttons {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 10px;
    }

    .editButton {
      background: #38723a;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    .editButton a {
      color: white;
      text-decoration: none;
    }

    .deleteButton {
      background: #b03d35;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    .empty-box {
      text-align: center;
      color: #6b7280;
      padding: 30px 10px;
    }

    @media (max-width: 768px) {
      .profile {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
      }
    }
  </style>
</head>

<body>

<nav class="nav" id="mainNav" role="navigation" aria-label="Main navigation">
  <a href="ghusn_home1.php" class="nav-logo">
    <img src="images/logoo.png" alt="Ghosn Logo" style="width:107px; height:107px; object-fit:contain; display:block;">
  </a>

  <ul class="nav-links">
    <li>
      <a href="ghusn_home1.php" id="nav-home">
        Home
      </a>
    </li>
    <li>
      <a href="submit.php" id="nav-report">
        Submit Report
      </a>
    </li>
    <li>
      <a href="residentProfile.php" id="nav-profile" class="active" style="color: #b7deb7;">
        Profile
      </a>
    </li>
  </ul>

  <div class="nav-actions">
    <button class="btn-nav-signout" style="color: #b7deb7;" onclick="signOut()">
      Sign Out
    </button>
  </div>
</nav>

<br><br><br>

<div class="container">

  <div class="profile">
    <div class="user">
      <div class="avatar"><?php echo htmlspecialchars($avatarLetter); ?></div>
      <div>
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding-bottom:10px;">
          <div class="name"><?php echo htmlspecialchars($profile['User_name'] ?? 'Resident'); ?></div>
          <div class="role">Resident</div>
        </div>
        <div class="info">
          <p>📍Location: <?php echo htmlspecialchars($profile['ResidentNeighbourhood'] ?: 'Not added'); ?></p>
          <p>📧 Email: <?php echo htmlspecialchars($profile['email'] ?? ''); ?></p>
           </div>
      </div>
    </div>

    <div>
      <div class="reports-count">
        <span>Reports</span>
        <h2><?php echo $reportsCount; ?></h2>
      </div>
      <br>
      <p class="btn"><a href="submit.php">+ New Report</a></p>
    </div>
  </div>

  <div class="reports">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3>My Reports</h3>
    </div>

    <?php if (empty($reports)): ?>
      <div class="empty-box">No reports found.</div>
    <?php else: ?>
      <?php foreach ($reports as $report): ?>
        <div class="report">
          <?php
$imageName = $report['photo']; // اسم الصورة من الداتابيس
$imagePath =  $imageName;
?>

<img src="<?php echo $imagePath; ?>" 
     alt="Report Image" 
     style="width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:10px;">
          <div style="display:flex; justify-content:space-between;">
            <h4><?php echo htmlspecialchars($report['Title']); ?></h4>
            <div class="buttons">
              <button class="editButton">
                <a href="edit.php?id=<?php echo urlencode($report['ReportID']); ?>">Edit</a>
              </button>

              <form action="delete_report.php" method="POST" onsubmit="return confirm('Delete this report?');" style="display:inline;">
                <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['ReportID']); ?>">
                <button type="submit" class="deleteButton">Delete</button>
              </form>
            </div>
          </div>

          <div class="meta">
            📍<?php echo htmlspecialchars($report['DistrictName'] ?: 'Unknown location'); ?>
          </div>

          <div class="tags">
            <span class="tag <?php echo severityClass($report['Severity_Level']); ?>">
              <?php echo htmlspecialchars($report['Severity_Level']); ?>/5
            </span>
            <span class="tag <?php echo statusClass($report['Status']); ?>">
              <?php echo htmlspecialchars($report['Status']); ?>
            </span>
          </div>

          <p><?php echo htmlspecialchars($report['Description']); ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script src="shared.js"></script>
</body>
</html>