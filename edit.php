<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$host = "localhost";
$user = "root";
$password = "root";
$database = "ghosn_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("No report selected.");
}

$reportID = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $severity = $_POST['severity'];
    $oldImage = $_POST['oldImage'];

    $imageName = $oldImage;

    if (!empty($_FILES['image']['name'])) {
        $folder = "uploads/reports/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $imageName = $folder . time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imageName);
    }

    $stmt = $conn->prepare("
        UPDATE report 
        SET Title = ?, Description = ?, Severity_Level = ?, photo = ?
        WHERE ReportID = ?
    ");

    $stmt->bind_param("ssiss", $title, $description, $severity, $imageName, $reportID);

    if ($stmt->execute()) {
        header("Location: residentProfile.php?updated=1");
        exit();
    } else {
        echo "Error updating report.";
    }
}

$stmt = $conn->prepare("SELECT * FROM report WHERE ReportID = ?");
$stmt->bind_param("s", $reportID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Report not found.");
}

$report = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Report</title>
<link rel="stylesheet" href="shared.css">

<style>
body {
    background: #e9e4d8;
    font-family: Arial, sans-serif;
}

.submit-wrap {
    max-width: 800px;
    margin: auto;
    padding: 7rem 2rem 4rem;
}

.form-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.submit-title {
    text-align: center;
    font-size: 2.3rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

label {
    font-weight: 600;
    display: block;
    margin-bottom: .4rem;
}

input, textarea, select {
    width: 100%;
    padding: .8rem;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.submit-btn {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 10px;
    background: #2d7a2d;
    color: white;
    font-weight: 700;
    cursor: pointer;
}

.submit-btn:hover {
    background: #1f5f1f;
}

#preview {
    width: 100%;
    max-height: 350px;
    object-fit: cover;
    margin-top: 10px;
    border-radius: 10px;
}
</style>
</head>

<body>

<main class="submit-wrap">
    <div class="form-card">

        <div class="submit-title">Edit Report</div>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Title</label>
                <input 
                    type="text" 
                    name="title" 
                    value="<?php echo htmlspecialchars($report['Title']); ?>" 
                    required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4" required><?php echo htmlspecialchars($report['Description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Severity</label>
                <select name="severity" required>
                    <option value="1" <?php if($report['Severity_Level'] == 1) echo "selected"; ?>>1 - Very Low</option>
                    <option value="2" <?php if($report['Severity_Level'] == 2) echo "selected"; ?>>2 - Low</option>
                    <option value="3" <?php if($report['Severity_Level'] == 3) echo "selected"; ?>>3 - Medium</option>
                    <option value="4" <?php if($report['Severity_Level'] == 4) echo "selected"; ?>>4 - High</option>
                    <option value="5" <?php if($report['Severity_Level'] == 5) echo "selected"; ?>>5 - Very High</option>
                </select>
            </div>

            <div class="form-group">
                <label>Current Image</label>
                <img id="preview" src="<?php echo htmlspecialchars($report['photo']); ?>" alt="Report image">
            </div>

            <div class="form-group">
                <label>Upload New Image Optional</label>
                <input type="file" name="image" id="imageInput" accept="image/*">
                <input type="hidden" name="oldImage" value="<?php echo htmlspecialchars($report['photo']); ?>">
            </div>

            <button type="submit" class="submit-btn">Save Changes</button>

        </form>

    </div>
</main>

<script>
document.getElementById("imageInput").addEventListener("change", function(e) {
    const file = e.target.files[0];

    if (!file) return;

    const reader = new FileReader();

    reader.onload = function() {
        document.getElementById("preview").src = reader.result;
    };

    reader.readAsDataURL(file);
});
</script>

</body>
</html>