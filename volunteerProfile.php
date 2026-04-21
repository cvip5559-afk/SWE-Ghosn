<?php
session_start();
require_once 'includes/connection.php';

// حماية الصفحة
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'volunteer') {
    header('Location: ghusn_home1.php');
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Volunteer';
$email    = $_SESSION['email'] ?? '';

// ─────────────────────────────────────────────
// 1) Fetch volunteer/user info
// ─────────────────────────────────────────────
$profile = [
    'User_name' => $userName,
    'email' => $email,
    'phone' => '',
    'DateOfJoining' => '',
];

$stmt = $conn->prepare("
    SELECT u.User_name, u.email, u.phone, v.DateOfJoining
    FROM user u
    JOIN volunteer v ON v.Volunteer_ID = u.User_ID
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
// 2) Fetch volunteer activities
// ملاحظة: هذا يفترض أن activity فيها Volunteer_ID و Report_ID و Status
// إذا اختلفت أسماء الأعمدة عندكم، عدلي الاستعلام فقط
// ─────────────────────────────────────────────
$activities = [];

$stmt2 = $conn->prepare("
    SELECT 
        a.Activity_ID,
        a.Status,
        a.Report_ID,
        r.Description,
        r.Severity_Level
    FROM activity a
    LEFT JOIN report r ON r.ReportID = a.Report_ID
   WHERE a.Volunteer_ID = ?
    ORDER BY a.Activity_ID DESC
");
$stmt2->bind_param('s', $userId);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2) {
    while ($row = $result2->fetch_assoc()) {
        $activities[] = $row;
    }
}
$stmt2->close();

$activitiesCount = count($activities);
$avatarLetter = strtoupper(substr(trim($profile['User_name'] ?? 'V'), 0, 1));

function activityStatusClass($status) {
    $status = strtolower(trim($status));
    if ($status === 'completed') return 'completed';
    if ($status === 'in progress' || $status === 'progress') return 'progress';
    return 'planned';
}

function formatJoinDate($date) {
    if (!$date) return 'Not added';
    $timestamp = strtotime($date);
    if (!$timestamp) return $date;
    return date('M Y', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Volunteer Profile</title>
  <link rel="stylesheet" href="shared.css">

  <style>
    body {
      background-color: #ded8c8d0;
      font-family: 'DM Sans', sans-serif;
      margin: 0;
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
      font-size: 22px;
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
      height: auto;
      width: 140px;
      text-align: center;
    }

    .role {
      font-size: 14px;
      font-weight: bold;
      border: solid 0.1px rgb(156, 156, 21);
      background-color: rgb(156, 156, 21);
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
      align-items: center;
      flex-wrap: wrap;
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

    .custom-select {
      position: relative;
      width: 180px;
    }

    .status-dropdown {
      width: 100%;
      height: 50px;
      padding: 10px 40px 10px 15px;
      border-radius: 14px;
      border: none;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
    }

    .planned {
      background: #fef3c7;
      color: #92400e;
    }

    .progress {
      background: #dbeafe;
      color: #1d4ed8;
    }

    .completed {
      background: #d1fae5;
      color: #065f46;
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

      #mainNav {
        height: 150px;
      }
    }
  </style>
</head>

<body>

<!-- HEADER -->
<nav class="nav" id="mainNav" role="navigation" aria-label="Main navigation">
  <a href="ghusn_home1.php" class="nav-logo">
    <img src="images/logoo.png" alt="Ghosn Logo"
         style="width:107px; height:107px; object-fit:contain; display:block;">
  </a>

  <ul class="nav-links">
    <li>
      <a href="ghusn_home1.php" id="nav-home">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Home
      </a>
    </li>
    <li>
      <a href="search.html" id="nav-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        Search
      </a>
    </li>
    <li>
      <a href="volunteerProfile.php" id="nav-profile" class="active" style="color: #b7deb7;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        Profile
      </a>
    </li>
  </ul>

  <div class="nav-actions">
    <button class="btn-nav-signout" style="color: #b7deb7;" onclick="signOut()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
      </svg>
      Sign Out
    </button>
  </div>
</nav>

<br><br><br>

<div class="container">

  <!-- PROFILE INFO -->
  <div class="profile">
    <div class="user">
      <div class="avatar"><?php echo htmlspecialchars($avatarLetter); ?></div>
      <div>
        <div style="display:flex; align-items:center; gap:10px; flex-wrap: wrap; padding-bottom: 10px;">
          <div class="name"><?php echo htmlspecialchars($profile['User_name'] ?? 'Volunteer'); ?></div>
          <div class="role">Volunteer</div>
        </div>
        <div class="info">
          <p>📧 Email: <?php echo htmlspecialchars($profile['email'] ?? ''); ?></p>
          <p>📞 Phone: <?php echo htmlspecialchars($profile['phone'] ?: 'Not added'); ?></p>
          <p>📅 Member since: <?php echo htmlspecialchars(formatJoinDate($profile['DateOfJoining'] ?? '')); ?></p>
        </div>
      </div>
    </div>

    <div>
      <div class="reports-count">
        <span>Contributions</span>
        <h2><?php echo $activitiesCount; ?></h2>
      </div>
    </div>
  </div>

  <!-- VOLUNTEER ACTIVITIES -->
  <div class="reports">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3>My Activities</h3>
      <p class="btn"><a href="search.html">+ Join Activity</a></p>
    </div>

    <?php if (empty($activities)): ?>
      <div class="empty-box">No activities found.</div>
    <?php else: ?>
      <?php foreach ($activities as $activity): ?>
        <div class="report">
          <img src="images/report1.jpg" alt="Activity Image"
               style="width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:10px;">

          <div style="display:flex; justify-content:space-between;">
            <h4>Activity #<?php echo htmlspecialchars($activity['Activity_ID']); ?></h4>
          </div>

          <div class="meta">
            Linked to Report #<?php echo htmlspecialchars($activity['Report_ID'] ?? 'N/A'); ?>
          </div>

          <p>
            <?php echo htmlspecialchars($activity['Description'] ?: 'No description available.'); ?>
          </p>

          <div class="tags">
            <?php if (!empty($activity['Severity_Level'])): ?>
              <span class="tag <?php echo ((int)$activity['Severity_Level'] >= 4 ? 'high' : (((int)$activity['Severity_Level'] == 3) ? 'medium' : 'low')); ?>">
                Severity: <?php echo htmlspecialchars($activity['Severity_Level']); ?>/5
              </span>
            <?php endif; ?>

            <div class="custom-select">
              <select class="status-dropdown <?php echo activityStatusClass($activity['Status'] ?? 'planned'); ?>"
                      onchange="updateStatus(this)">
                <option value="planned" <?php echo (activityStatusClass($activity['Status'] ?? '') === 'planned') ? 'selected' : ''; ?>>
                  Planned
                </option>
                <option value="progress" <?php echo (activityStatusClass($activity['Status'] ?? '') === 'progress') ? 'selected' : ''; ?>>
                  In Progress
                </option>
                <option value="completed" <?php echo (activityStatusClass($activity['Status'] ?? '') === 'completed') ? 'selected' : ''; ?>>
                  Completed
                </option>
              </select>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>

<script>
function updateStatus(select) {
  select.classList.remove("planned", "progress", "completed");
  select.classList.add(select.value);
}
</script>

<script src="shared.js"></script>
</body>
</html>