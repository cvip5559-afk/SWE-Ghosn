<?php

session_start();

// ── PROTECT PAGE ──
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'resident') {
    header('Location: ghusn_home1.php');
    exit;
}

$residentID = $_SESSION['user_id'];
$error      = '';
$success    = '';


$host   = 'localhost';
$dbName = 'ghosn_db';
$dbUser = 'root';      
$dbPass = 'root';           

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbName;charset=utf8",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Collect & sanitize inputs
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $lat         = trim($_POST['lat']         ?? '');
    $lng         = trim($_POST['lng']         ?? '');
    $severityRaw = trim($_POST['severity']    ?? '');

   
    $severity = (int) $severityRaw;

    // 2. Basic validation
    if (!$title || !$description || !$lat || !$lng || !$severity) {
        $error = 'Please fill in all fields and select a location on the map.';
    } elseif ($severity < 1 || $severity > 5) {
        $error = 'Invalid severity level.';
    } elseif (empty($_FILES['image']['name'])) {
        $error = 'Please upload an image.';
    } else {

      
        $uploadDir   = 'uploads/reports/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file        = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize      = 5 * 1024 * 1024; // 5 MB

        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'Only JPG, PNG, WEBP, or GIF images are allowed.';
        } elseif ($file['size'] > $maxSize) {
            $error = 'Image must be smaller than 5 MB.';
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Image upload failed. Please try again.';
        } else {
            // Generate a unique filename
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $uploadDir . uniqid('rpt_', true) . '.' . strtolower($ext);

            if (!move_uploaded_file($file['tmp_name'], $filename)) {
                $error = 'Could not save the image. Check folder permissions.';
            }
        }

       
        if (!$error) {
            try {
                $pdo->beginTransaction();

              
                $locationID = 'LOC-' . uniqid();

                $stmtLoc = $pdo->prepare("
                    INSERT INTO location (LocationID, DistrictName, Landmark, StreetName, postalCode)
                    VALUES (:lid, :district, :landmark, :street, :postal)
                ");
                $stmtLoc->execute([
                    ':lid'      => $locationID,
                    ':district' => 'GPS Location',
                    ':landmark' => "Lat: $lat, Lng: $lng",
                    ':street'   => "Lat: $lat, Lng: $lng",
                    ':postal'   => '00000',
                ]);

                
                $reportID = 'RPT-' . uniqid();

                $stmtRpt = $pdo->prepare("
                    INSERT INTO report
                        (ReportID, Severity_Level, Status, Description, Title, photo, resident_ID, LocationID)
                    VALUES
                        (:rid, :severity, 'Pending', :desc, :title, :photo, :resID, :locID)
                ");
                $stmtRpt->execute([
                    ':rid'      => $reportID,
                    ':severity' => $severity,
                    ':desc'     => $description,
                    ':title'    => $title,
                    ':photo'    => $filename,
                    ':resID'    => $residentID,
                    ':locID'    => $locationID,
                ]);

                $pdo->commit();
                $success = 'Your report has been submitted successfully! ✅';

            } catch (PDOException $e) {
                $pdo->rollBack();
                // Remove uploaded image if DB insert failed
                if (isset($filename) && file_exists($filename)) {
                    unlink($filename);
                }
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$role        = $_SESSION['role']      ?? 'unknown';
$userName    = $_SESSION['user_name'] ?? 'User';
$profileHref = ($role === 'volunteer') ? 'volunteerProfile.html' : 'residentProfile.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Submit Report — Ghosn</title>

<link rel="stylesheet" href="shared.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
body { background: #e9e4d8; }

.submit-wrap {
  max-width: 900px;
  margin: auto;
  padding: calc(var(--nav-h, 80px) + 2rem) 2rem 4rem;
}

.form-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.submit-title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
  font-family: 'Fraunces', serif;
}

.form-group { margin-bottom: 1rem; }

label {
  font-weight: 600;
  margin-bottom: .3rem;
  display: block;
}

input, textarea, select {
  width: 100%;
  padding: .8rem;
  border-radius: 10px;
  border: 1px solid #ddd;
  font-family: inherit;
  font-size: 1rem;
  box-sizing: border-box;
}

input:focus, textarea:focus, select:focus {
  outline: none;
  border-color: #2d7a2d;
  box-shadow: 0 0 0 3px rgba(45,122,45,0.1);
}

#map {
  height: 300px;
  border-radius: 12px;
  margin-top: .5rem;
}

.submit-btn {
  width: 100%;
  padding: 1rem;
  border: none;
  border-radius: 10px;
  background: #2d7a2d;
  color: white;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: background .2s;
}
.submit-btn:hover { background: #1f5f1f; }
.submit-btn:disabled { background: #aaa; cursor: not-allowed; }

#preview {
  width: 100%;
  margin-top: 10px;
  border-radius: 10px;
  display: none;
  max-height: 300px;
  object-fit: cover;
}

/* ALERTS */
.alert {
  padding: 1rem 1.2rem;
  border-radius: 10px;
  margin-bottom: 1.2rem;
  font-weight: 500;
}
.alert-error {
  background: #fef2f2;
  border: 1px solid #fca5a5;
  color: #b91c1c;
}
.alert-success {
  background: #f0fdf4;
  border: 1px solid #86efac;
  color: #15803d;
  text-align: center;
  font-size: 1.1rem;
}
.alert-success a {
  color: #2d7a2d;
  font-weight: 700;
  text-decoration: underline;
}
</style>
</head>

<body>


<nav class="nav" id="mainNav" role="navigation" aria-label="Main navigation">
  <a href="#" class="nav-logo">
    <img src="images/logoo.png" alt="Ghosn Logo"
         style="width:107px;height:107px;object-fit:contain;display:block;">
  </a>

  <ul class="nav-links">
    <li>
      <a href="ghusn_home1.php" id="nav-home">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
      </a>
    </li>
    <li>
      <a href="submit.php" id="nav-report" class="active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
        Submit Report
      </a>
    </li>
    <li>
      <a href="search.html" id="nav-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Search
      </a>
    </li>
    <li>
      <a href="<?php echo htmlspecialchars($profileHref); ?>" id="nav-profile">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profile
      </a>
    </li>
  </ul>

  <div class="nav-actions">
    <button class="btn-nav-signout" style="color: #b7deb7;" onclick="signOut()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      Sign Out
    </button>
  </div>
</nav>

<br><br><br>

<!-- CONTENT -->
<main class="submit-wrap">
  <div class="form-card">

    <div class="submit-title">Submit Report</div>

   
    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
        <br><br>
        <a href="ghusn_home1.php">← Back to Home</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="submit.php">Submit Another Report</a>
      </div>
    <?php endif; ?>

    
    <?php if (!$success): ?>
    <form id="reportForm" method="POST" action="submit.php" enctype="multipart/form-data">

      
      <div class="form-group">
        <label for="title">Title</label>
        <input
          type="text"
          id="title"
          name="title"
          placeholder="e.g. Dry land near Al Nakheel Park"
          value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
          required>
      </div>

      
      <div class="form-group">
        <label for="description">Description</label>
        <textarea
          id="description"
          name="description"
          rows="4"
          placeholder="Describe the issue in detail..."
          required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
      </div>

      
      <div class="form-group">
        <label>Select Location</label>

        <button type="button" id="getLocationBtn" class="submit-btn" style="margin-bottom:.6rem;">
          📍 Use My Current Location
        </button>

        <div id="map"></div>

        <input
          id="locationDisplay"
          readonly
          placeholder="Click on map or use GPS"
          style="margin-top:.5rem; background:#f9f9f9;">

        
        <input type="hidden" id="lat" name="lat" value="<?php echo htmlspecialchars($_POST['lat'] ?? ''); ?>">
        <input type="hidden" id="lng" name="lng" value="<?php echo htmlspecialchars($_POST['lng'] ?? ''); ?>">
      </div>

     
      <div class="form-group">
        <label for="severity">Severity</label>
        <select id="severity" name="severity" required>
          <option value="">Select severity level</option>
          <option value="1" <?php echo (($_POST['severity'] ?? '') == '1') ? 'selected' : ''; ?>>1 - Very Low</option>
          <option value="2" <?php echo (($_POST['severity'] ?? '') == '2') ? 'selected' : ''; ?>>2 - Low</option>
          <option value="3" <?php echo (($_POST['severity'] ?? '') == '3') ? 'selected' : ''; ?>>3 - Medium</option>
          <option value="4" <?php echo (($_POST['severity'] ?? '') == '4') ? 'selected' : ''; ?>>4 - High</option>
          <option value="5" <?php echo (($_POST['severity'] ?? '') == '5') ? 'selected' : ''; ?>>5 - Very High</option>
        </select>
      </div>

      
      <div class="form-group">
        <label for="image">Upload Image <span style="color:#b91c1c;">(Required)</span></label>
        <input type="file" id="image" name="image" accept="image/*">
        <img id="preview" alt="Image preview">
      </div>

      <button type="submit" class="submit-btn" id="submitBtn">Submit Report</button>

    </form>
    <?php endif; ?>

  </div>
</main>

<!-- SCRIPTS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const latInput      = document.getElementById('lat');
const lngInput      = document.getElementById('lng');
const locationDisplay = document.getElementById('locationDisplay');
const preview       = document.getElementById('preview');


const map = L.map('map').setView([24.7136, 46.6753], 12); // Default: Riyadh

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

let marker;

function setMarker(lat, lng) {
  if (marker) map.removeLayer(marker);
  marker = L.marker([lat, lng]).addTo(map);
  latInput.value      = lat;
  lngInput.value      = lng;
  locationDisplay.value = `Lat: ${parseFloat(lat).toFixed(4)}, Lng: ${parseFloat(lng).toFixed(4)}`;
}


const savedLat = latInput.value;
const savedLng = lngInput.value;
if (savedLat && savedLng) {
  map.setView([savedLat, savedLng], 15);
  setMarker(savedLat, savedLng);
}

// Click on map
map.on('click', function(e) {
  setMarker(e.latlng.lat, e.latlng.lng);
});

// GPS button
document.getElementById('getLocationBtn').onclick = () => {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser.');
    return;
  }
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;
      map.setView([lat, lng], 15);
      setMarker(lat, lng);
    },
    () => alert('Could not get your location. Please click on the map instead.')
  );
};


document.getElementById('image').addEventListener('change', e => {
  const file = e.target.files[0];
  if (!file) { preview.style.display = 'none'; return; }
  const reader = new FileReader();
  reader.onload = () => {
    preview.src = reader.result;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(file);
});


document.getElementById('reportForm')?.addEventListener('submit', function(e) {
  if (!latInput.value || !lngInput.value) {
    e.preventDefault();
    alert('Please select a location on the map 📍');
    return;
  }
  if (!document.getElementById('image').files.length) {
    e.preventDefault();
    alert('Please upload an image 📷');
    return;
  }
 
  document.getElementById('submitBtn').textContent = 'Submitting…';
  document.getElementById('submitBtn').disabled = true;
});
</script>
<script src="shared.js"></script>
</body>
</html>