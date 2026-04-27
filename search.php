<?php
session_start();
require_once 'includes/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Search Reports — Ghosn</title>

  <link rel="stylesheet" href="shared.css"/>

  <style>
    body {
      background: #e9e4d8;
      min-height: 100vh;
    }

    .search-wrap {
      max-width: 1100px;
      margin: 0 auto;
      padding: calc(var(--nav-h) + 2rem) 2rem 4rem;
    }

    /* العنوان */
    .search-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .search-title {
      font-family: 'Fraunces', serif;
      font-size: clamp(2.5rem,5vw,4rem);
      font-weight: 300;
      color: var(--n900);
      line-height: 1;
    }

    .search-sub {
      font-family: 'Fraunces', serif;
      font-size: clamp(2rem,4vw,3rem);
      font-style: italic;
      color: var(--g300);
      margin-top: .3rem;
    }

    /* السيرتش */
    .search-bar {
      display: flex;
      gap: .6rem;
      margin-top: 1.5rem;
    }

    .search-bar input,
    .search-bar select {
      padding: .85rem 1rem;
      border-radius: var(--r-sm);
      border: 1px solid var(--border-l);
      font-size: .9rem;
    }

    .search-bar input {
      flex: 1;
    }

    /* grid */
    .report-grid {
      margin-top: 2.5rem;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    @media (max-width: 700px) {
      .report-grid {
        grid-template-columns: 1fr;
      }
    }
     @media (max-width: 768px) {
  #mainNav {
    height: 150px;
  } }
    
    body, main {
  transform: none !important;
}
  </style>
</head>
<body class="no-birds">

<!-- HEADER -->

<nav class="nav" id="mainNav" role="navigation" aria-label="Main navigation">


  <a href="#hero" class="nav-logo">
  <img src="images/logoo.png" alt="Ghosn Logo" 
       style="width:107px; height:107px; object-fit:contain; display:block;">
  
</a>

  <ul class="nav-links">
    <li>
      <a href="ghusn_home1.php" id="nav-home">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
      </a>
    </li>
    <li>
      <a href="submit.php" id="nav-report" >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
        Submit Report
      </a>
    </li>
    <li>
      <a href="search.php" id="nav-search" class="active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Search
      </a>
    </li>
    <li>
      <a href="residentProfile.php" id="nav-profile"  style="color: #b7deb7;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profile
      </a>
    </li>
  </ul>

  <div class="nav-actions">
    <button class="btn-nav-signout" style="color: #b7deb7;" onclick="signOut()"
">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      Sign Out
    </button>
  </div>
</nav>
<br><br><br>

<!-- CONTENT -->
<main class="search-wrap">

  <div class="search-header">
    <div class="search-title">Search reports</div>
    <div class="search-sub">find what matters.</div>
  </div>

  <div class="search-bar">
    <input id="searchInput" placeholder="Search by title or location...">
    
    <select id="severityFilter">
      <option value="">All (1-5)</option>
      <option value="5">5 - Very High</option>
      <option value="4">4 - High</option>
      <option value="3">3 - Medium</option>
      <option value="2">2 - Low</option>
      <option value="1">1 - Very Low</option>
    </select>
  </div>

<div class="report-grid">

<?php

require_once 'includes/connection.php';

$sql = "

SELECT

report.ReportID,
report.Title,
report.Description,
report.Status,
report.Severity_Level,
report.photo,

location.DistrictName

FROM report

LEFT JOIN location
ON report.LocationID = location.LocationID

ORDER BY report.ReportID DESC

";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)){

$severityColor = "#22c55e";

if($row['Severity_Level'] == 2){
    $severityColor = "#84cc16";
}

if($row['Severity_Level'] == 3){
    $severityColor = "#facc15";
}

if($row['Severity_Level'] == 4){
    $severityColor = "#f97316";
}

if($row['Severity_Level'] == 5){
    $severityColor = "#ef4444";
}

?>

<article style="
background:white;
border-radius:14px;
padding:1rem;
box-shadow: var(--sh-sm);
border:1px solid var(--border-l);
transition: all .25s ease;
position:relative;
">

<img

src="<?php echo $row['photo']; ?>"

style="
width:100%;
height:160px;
object-fit:cover;
border-radius:10px;
margin-bottom:.8rem;
">

<h3 style="
font-size:1.05rem;
font-weight:700;
margin-bottom:.3rem;
">

<?php echo $row['Title']; ?>

</h3>

<span style="
display:inline-block;
padding:.35rem .9rem;
border-radius:999px;
font-size:.75rem;
font-weight:700;

background:
<?php echo $row['Status'] == 'Completed'
? 'rgba(16,185,129,.15)'
: 'rgba(59,130,246,.12)';
?>;

color:
<?php echo $row['Status'] == 'Completed'
? '#047857'
: '#2563eb';
?>;
">

<?php echo $row['Status']; ?>

</span>

<p style="
font-size:.8rem;
margin:.4rem 0;
">

📍 <?php echo $row['DistrictName']; ?>

<span style="
background:<?php echo $severityColor; ?>;
color:white;
padding:.2rem .5rem;
border-radius:999px;
font-size:.7rem;
margin-left:6px;
">

<?php echo $row['Severity_Level']; ?>/5

</span>

</p>

<p style="
font-size:.75rem;
color:#6b7280;
margin-bottom:.4rem;
">

Report ID:
<?php echo $row['ReportID']; ?>

</p>

<p style="
font-size:.85rem;
color:#1f2937;
">

<?php echo $row['Description']; ?>

</p>

<?php if($row['Status'] != 'Completed'){ ?>

<form
action="join_activity.php"
method="POST"
style="
display:flex;
justify-content:flex-end;
margin-top:1rem;
"
>

<input
type="hidden"
name="report_id"
value="<?php echo $row['ReportID']; ?>"
>

<button style="
padding:.35rem .95rem;
border:none;
border-radius:999px;
background:#2d7a2d;
color:white;
font-size:.72rem;
font-weight:600;
cursor:pointer;
">

Volunteer 🌱

</button>

</form>

<?php } ?>

</article>

<?php } ?>

</div>

  <div id="emptyMsg" style="display:none; text-align:center; margin-top:2rem; color:var(--n500);">
    No reports found
  </div>

</main>

<footer>
  <!-- Animated tree silhouettes -->
  <canvas class="footer-trees" id="footerTreesCanvas" aria-hidden="true"></canvas>

  <div class="footer-main">
    <div class="footer-top">

      <div class="footer-brand">
        <div class="footer-brand-logo">
          <svg viewBox="0 0 28 36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 34 C13 28 12 22 14 16" stroke="#6abf69" stroke-width="2" stroke-linecap="round"/>
            <path d="M14 16 C10 14 5 12 2 8" stroke="#4caf50" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M14 18 C9 17 4 18 1 15" stroke="#4caf50" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 15 C11 11 8 7 9 3" stroke="#4caf50" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 16 C18 14 23 12 26 8" stroke="#81c784" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M14 18 C19 17 24 18 27 15" stroke="#81c784" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 15 C17 11 20 7 19 3" stroke="#81c784" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 14 C14 10 13 6 14 2" stroke="#a5d6a7" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 34 C11 33 8 34 6 33" stroke="#6abf69" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 34 C17 33 20 34 22 33" stroke="#6abf69" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span class="footer-brand-name">Ghosn <span>غصن</span></span>
        </div>
        <p class="footer-brand-desc">Combining community action, data tools, and environmental science to reverse desertification across the region.</p>
        <div class="footer-social">
          <a href="#" aria-label="Twitter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
          </a>
          <a href="#" aria-label="Instagram">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
          <a href="#" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
          </a>
        </div>
      </div>

      <div>
        <div class="footer-col-title">Navigation</div>
        <div class="footer-col-links">
          <a href="#hero">Home</a>
          <a href="#about">About</a>
          <a href="#vision">Platform</a>
          <a href="#restoration">How It Works</a>
          <a href="#impact">Our Impact</a>
          <a href="#join">Join</a>
          <a href="#future">Future Vision</a>
        </div>
      </div>

      <div>
        <div class="footer-col-title">Platform</div>
        <div class="footer-col-links">
          <a href="#">Submit a Report</a>
          <a href="#">Tree Campaigns</a>
          <a href="#">Interactive Map</a>
          <a href="#">Impact Dashboard</a>
          <a href="#">Community Forum</a>
          <a href="#">Learning Hub</a>
        </div>
      </div>

      <div>
        <div class="footer-col-title">Contact</div>
        <div class="footer-col-links">
          <a href="#">hello@ghosn.eco</a>
          <a href="#">Partner With Us</a>
          <a href="#">Press & Media</a>
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Use</a>
        </div>
        <div style="margin-top:1.5rem; padding:1rem 1.2rem; background:rgba(106,191,105,.08); border:1px solid rgba(106,191,105,.15); border-radius:10px;">
          <div style="font-size:.72rem; color:var(--oasis-bright); letter-spacing:.1em; text-transform:uppercase; font-family:var(--font-mono); margin-bottom:.4rem;">Status</div>
          <div style="display:flex; align-items:center; gap:.5rem;">
            <span style="width:7px; height:7px; border-radius:50%; background:#4caf50; animation:pulse-dot 2s infinite;"></span>
            <span style="font-size:.8rem; color:rgba(180,220,180,.7);">All systems operational</span>
          </div>
        </div>
      </div>

    </div>

    <div class="footer-bottom-row">
      <span class="footer-copy">© 2025 Ghosn Environmental Platform — Built for a greener planet</span>
      <div class="footer-badge">
        <span class="footer-badge-dot"></span>
        <span>125K+ Trees Planted</span>
      </div>
    </div>
  </div>
</footer>

<script>

const searchInput =
document.getElementById("searchInput");

const severityFilter =
document.getElementById("severityFilter");

const reports =
document.querySelectorAll(".report-grid article");

function filterReports(){

const q =
searchInput.value.toLowerCase();

const severity =
severityFilter.value;

reports.forEach(card => {

const text =
card.innerText.toLowerCase();

const sev =
card.innerText;

const matchSearch =
text.includes(q);

const matchSeverity =
severity === "" ||
sev.includes(`${severity}/5`);

card.style.display =
(matchSearch && matchSeverity)
? "block"
: "none";

});

}

searchInput.addEventListener(
"input",
filterReports
);

severityFilter.addEventListener(
"change",
filterReports
);

</script>

<script src="shared.js"></script>

</body>
</html>






