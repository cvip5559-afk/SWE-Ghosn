<?php
session_start();
require_once 'includes/connection.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ── DEBUG: شيل هذا الكود بعد ما تحل المشكلة ──
echo '<div style="background:#111;color:#0f0;padding:10px;font-size:13px;position:fixed;top:0;left:0;z-index:9999;">';
echo "role = " . var_export($_SESSION['role'] ?? 'NOT SET', true) . " | ";
echo "user_id = " . var_export($_SESSION['user_id'] ?? 'NOT SET', true);
echo '</div>';
// ── نهاية DEBUG ──

// مؤقتاً: شيلنا شرط الـ role عشان نشوف الصفحة تفتح
// if (($_SESSION['role'] ?? '') !== 'volunteer') {
//     header('Location: ghusn_home1.php');
//     exit;
// }

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Volunteer';
$email    = $_SESSION['email'] ?? '';

$profile = [
    'User_name' => $userName,
    'email' => $email,
    'phone' => '',
    'DateOfJoining' => '',
];

if (!$conn || $conn->connect_error) {
    die('<div style="color:red;padding:20px;">DB Error: ' . ($conn->connect_error ?? 'null') . '</div>');
}

$stmt = $conn->prepare("SELECT u.User_name, u.email, u.phone, v.DateOfJoining FROM user u JOIN volunteer v ON v.Volunteer_ID = u.User_ID WHERE u.User_ID = ? LIMIT 1");
if (!$stmt) { die('<div style="color:red;padding:20px;">Query1 Error: ' . $conn->error . '</div>'); }
$stmt->bind_param('s', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) { $profile = $result->fetch_assoc(); }
$stmt->close();

$activities = [];
$stmt2 = $conn->prepare("SELECT a.Activity_ID, a.Status, a.Report_ID, r.Title, r.Description, r.Severity_Level, r.photo FROM activity a LEFT JOIN report r ON r.ReportID = a.Report_ID WHERE a.Volunteer_ID = ? ORDER BY a.Activity_ID DESC");
if (!$stmt2) { die('<div style="color:red;padding:20px;">Query2 Error: ' . $conn->error . '</div>'); }
$stmt2->bind_param('s', $userId);
$stmt2->execute();
$result2 = $stmt2->get_result();
if ($result2) { while ($row = $result2->fetch_assoc()) { $activities[] = $row; } }
$stmt2->close();

$activitiesCount = count($activities);
$avatarLetter = strtoupper(substr(trim($profile['User_name'] ?? 'V'), 0, 1));

function activityStatusClass($status) {
    $status = strtolower(trim($status));
    if ($status === 'completed') return 'completed';
    if ($status === 'in progress') return 'progress';
    return 'planned';
}

function severityClass($level) {
    $level = (int)$level;
    if ($level >= 4) return 'high';
    if ($level == 3) return 'medium';
    return 'low';
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
      width: 140px;
      text-align: center;
    }

    .reports-count h2 {
      margin: 0;
      color: green;
    }

    .role {
      font-size: 14px;
      font-weight: bold;
      background-color: rgb(156, 156, 21);
      border-radius: 10px;
      padding: 2px 8px;
      color: white;
      text-align: center;
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
      background: white;
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

    .planned {
      background: #fef3c7;
      color: #92400e;
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

    .status-btn {
      background: linear-gradient(135deg, #2d7a2d, #4caf50);
      color: white;
      border: none;
      padding: 7px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 12px;
      font-weight: bold;
      transition: 0.2s ease;
    }

    .status-btn:hover {
      transform: scale(1.06);
      box-shadow: 0 8px 18px rgba(0,0,0,0.18);
    }

    .popup-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 99999;
    }

    .popup-box {
      background: white;
      padding: 22px;
      border-radius: 14px;
      width: 330px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.25);
      animation: popupFade 0.2s ease;
    }

    .popup-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .popup-header h3 {
      margin: 0;
      color: #1f3b1f;
    }

    .close-btn {
      cursor: pointer;
      font-size: 18px;
      font-weight: bold;
      color: #555;
      transition: 0.2s;
    }

    .close-btn:hover {
      color: #b03d35;
      transform: scale(1.15);
    }

    #statusSelect {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 10px;
      border: 1px solid #ddd;
      font-weight: 600;
    }

    .save-btn {
      width: 100%;
      background: #2d7a2d;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.2s;
    }

    .save-btn:hover {
      background: #1f5f1f;
      transform: translateY(-1px);
    }

    @keyframes popupFade {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
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

<nav class="nav" id="mainNav" role="navigation" aria-label="Main navigation">
  <a href="ghusn_home1.php" class="nav-logo">
    <img src="images/logoo.png" alt="Ghosn Logo"
         style="width:107px; height:107px; object-fit:contain; display:block;">
  </a>

  <ul class="nav-links">
    <li>
      <a href="ghusn_home1.php" id="nav-home">Home</a>
    </li>
    <li>
      <a href="search.php" id="nav-search">Search</a>
    </li>
    <li>
      <a href="volunteerProfile.php" id="nav-profile" class="active" style="color: #b7deb7;">Profile</a>
    </li>
  </ul>

  <div class="nav-actions">
    <button class="btn-nav-signout" style="color: #b7deb7;" onclick="signOut()">Sign Out</button>
  </div>
</nav>

<br><br><br>

<div class="container">

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

  <div class="reports">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3>My Activities</h3>
      <p class="btn"><a href="search.php">+ Join Activity</a></p>
    </div>

    <?php if (empty($activities)): ?>
      <div class="empty-box">No activities found.</div>
    <?php else: ?>
      <?php foreach ($activities as $activity): ?>
        <div class="report">
          <img src="<?php echo htmlspecialchars($activity['photo'] ?: 'images/report1.jpg'); ?>" alt="Activity Image"
               style="width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:10px;">

          <div style="display:flex; justify-content:space-between;">
            <h4><?php echo htmlspecialchars($activity['Title'] ?: 'Activity #' . $activity['Activity_ID']); ?></h4>
          </div>

          <div class="meta">
            Linked to Report #<?php echo htmlspecialchars($activity['Report_ID'] ?? 'N/A'); ?>
          </div>

          <p>
            <?php echo htmlspecialchars($activity['Description'] ?: 'No description available.'); ?>
          </p>

          <div class="tags">
            <?php if (!empty($activity['Severity_Level'])): ?>
              <span class="tag <?php echo severityClass($activity['Severity_Level']); ?>">
                Severity: <?php echo htmlspecialchars($activity['Severity_Level']); ?>/5
              </span>
            <?php endif; ?>

            <span 
              id="status-<?php echo htmlspecialchars($activity['Activity_ID']); ?>"
              class="tag <?php echo activityStatusClass($activity['Status']); ?>"
            >
              <?php echo htmlspecialchars($activity['Status']); ?>
            </span>

            <button 
              type="button"
              class="status-btn"
              onclick="openStatusPopup('<?php echo htmlspecialchars($activity['Activity_ID']); ?>', '<?php echo htmlspecialchars($activity['Status']); ?>')"
            >
              ✏️ Update Status
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div id="statusPopup" class="popup-overlay">
  <div class="popup-box">
    <div class="popup-header">
      <h3>Update Status</h3>
      <span class="close-btn" onclick="closePopup()">×</span>
    </div>

    <select id="statusSelect">
  <option value="Pending">Pending</option>
  <option value="In Progress">In Progress</option>
  <option value="Completed">Completed</option>
</select>

    <button class="save-btn" onclick="saveStatus()">Save Changes</button>
  </div>
</div>

<script>
let currentActivityId = null;

function openStatusPopup(activityId, currentStatus) {
  currentActivityId = activityId;
  document.getElementById("statusSelect").value = currentStatus;
  document.getElementById("statusPopup").style.display = "flex";
}

function closePopup() {
  document.getElementById("statusPopup").style.display = "none";
}

function saveStatus() {
  const status = document.getElementById("statusSelect").value;

  fetch("update_activity_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "activity_id=" + encodeURIComponent(currentActivityId) +
          "&status=" + encodeURIComponent(status)
  })
  .then(res => res.text())
  .then(data => {
    if (data.trim() === "Updated") {
      const statusTag = document.getElementById("status-" + currentActivityId);

      statusTag.textContent = status;
      statusTag.classList.remove("planned", "progress", "completed");

if (status === "Completed") {
  statusTag.classList.add("completed");
} else if (status === "In Progress") {
  statusTag.classList.add("progress");
} else {
  statusTag.classList.add("planned");
}
      closePopup();
    } else {
      alert("Error: " + data);
    }
  });
}

document.getElementById("statusPopup").addEventListener("click", function(e) {
  if (e.target === this) {
    closePopup();
  }
});
</script>

<script>
  window.GHOSN_ROLE    = "volunteer";
  window.GHOSN_PHP_NAV = true;
</script>
<script src="shared.js"></script>
</body>
</html>