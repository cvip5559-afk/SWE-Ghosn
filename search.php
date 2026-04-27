
<?php
session_start();
require_once 'includes/connection.php';

$query = "
SELECT *
FROM report
ORDER BY CreatedAt DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Search Reports</title>

<link rel="stylesheet" href="shared.css">

<style>

body{
    background:#e9e4d8;
}

/* PAGE */

.search-wrap{
    max-width:1200px;
    margin:auto;
    padding:
    calc(var(--nav-h) + 2rem)
    2rem
    4rem;
}

/* TITLE */

.search-header{
    text-align:center;
    margin-bottom:2rem;
}

.search-title{
    font-size:3rem;
    font-weight:700;
}

.search-sub{
    color:#5d9e41;
    font-size:1.1rem;
    margin-top:.5rem;
}

/* SEARCH */

.search-bar{
    display:flex;
    gap:1rem;
    margin-bottom:2rem;
    flex-wrap:wrap;
}

.search-bar input,
.search-bar select{

    padding:.9rem 1rem;
    border-radius:12px;
    border:1px solid #dcdcdc;
    font-size:.9rem;
}

.search-bar input{
    flex:1;
}

/* GRID */

.report-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:1.2rem;
}

/* CARD */

.report-card{

    background:white;

    border-radius:18px;

    overflow:hidden;

    border:1px solid rgba(0,0,0,.06);

    box-shadow:0 4px 16px rgba(0,0,0,.05);

    transition:.25s;
}

.report-card:hover{
    transform:translateY(-4px);
}

/* IMAGE */

.report-image{

    width:100%;
    height:220px;

    object-fit:cover;
}

/* CONTENT */

.report-content{
    padding:1rem;
}

.report-title{
    font-size:1.1rem;
    font-weight:700;
    margin-bottom:.5rem;
}

.report-description{

    color:#555;
    line-height:1.6;
    font-size:.9rem;

    margin-bottom:1rem;
}

/* LOCATION */

.location-link{

    color:#2d7a2d;

    font-weight:600;

    text-decoration:none;
}

/* BADGES */

.badges{

    display:flex;
    gap:.5rem;
    flex-wrap:wrap;

    margin-top:1rem;
}

.badge{

    padding:.35rem .8rem;

    border-radius:999px;

    font-size:.75rem;

    font-weight:700;
}

.status-review{

    background:rgba(59,130,246,.12);
    color:#2563eb;
}

.status-resolved{

    background:rgba(16,185,129,.15);
    color:#047857;
}

/* VOLUNTEER BUTTON */

.volunteer-btn{

    margin-top:1rem;

    padding:.55rem 1rem;

    border:none;

    border-radius:10px;

    background:#2d7a2d;

    color:white;

    font-size:.82rem;

    font-weight:700;

    cursor:pointer;

    transition:.2s;
}

.volunteer-btn:hover{

    background:#246224;
}

/* EMPTY */

.empty-msg{

    text-align:center;

    margin-top:3rem;

    color:#666;
}

</style>

</head>

<body>

<!-- NAV -->

<nav class="nav">

    <a href="index.php" class="nav-logo">
        Ghosn
    </a>

    <ul class="nav-links">

        <li>
            <a href="index.php">
                Home
            </a>
        </li>

        <li>
            <a href="submitReport.php">
                Submit Report
            </a>
        </li>

        <li>
            <a class="active" href="search.php">
                Search
            </a>
        </li>

        <li>
            <a href="volunteerProfile.php">
                Profile
            </a>
        </li>

    </ul>

</nav>

<!-- CONTENT -->

<main class="search-wrap">

    <div class="search-header">

        <div class="search-title">
            Search Reports
        </div>

        <div class="search-sub">
            Find environmental cases near you
        </div>

    </div>

    <!-- SEARCH -->

    <div class="search-bar">

        <input
            type="text"
            id="searchInput"
            placeholder="Search by title or location..."
        >

        <select id="severityFilter">

            <option value="">
                All Severity
            </option>

            <option value="5">
                5 - Very High
            </option>

            <option value="4">
                4 - High
            </option>

            <option value="3">
                3 - Medium
            </option>

            <option value="2">
                2 - Low
            </option>

            <option value="1">
                1 - Very Low
            </option>

        </select>

    </div>

    <!-- REPORTS -->

    <div class="report-grid" id="reportList">

        <?php if(mysqli_num_rows($result) > 0): ?>

            <?php while($r = mysqli_fetch_assoc($result)): ?>

                <div class="report-card">

                    <img
                        src="<?= htmlspecialchars($r['photo']) ?>"
                        class="report-image"
                    >

                    <div class="report-content">

                        <div class="report-title">

                            <?= htmlspecialchars($r['Title']) ?>

                        </div>

                        <div class="report-description">

                            <?= htmlspecialchars($r['Description']) ?>

                        </div>

                        <a
                            class="location-link"

                            target="_blank"

                            href="https://www.google.com/maps?q=<?= $r['Latitude'] ?>,<?= $r['Longitude'] ?>"
                        >

                            📍
                            <?= htmlspecialchars($r['LocationName']) ?>

                        </a>

                        <div class="badges">

                            <span class="badge
                            <?= strtolower($r['Status']) === 'resolved'
                            ? 'status-resolved'
                            : 'status-review' ?>
                            ">

                                <?= htmlspecialchars($r['Status']) ?>

                            </span>

                            <span class="badge">

                                Severity:
                                <?= $r['Severity_Level'] ?>/5

                            </span>

                        </div>

                        <?php if(strtolower($r['Status']) !== 'resolved'): ?>

                            <form
                                action="join_activity.php"
                                method="POST"
                            >

                                <input
                                    type="hidden"
                                    name="report_id"
                                    value="<?= $r['ReportID'] ?>"
                                >

                                <button class="volunteer-btn">

                                    Volunteer

                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty-msg">

                No reports found

            </div>

        <?php endif; ?>

    </div>

</main>

<script>

const searchInput =
document.getElementById("searchInput");

const severityFilter =
document.getElementById("severityFilter");

const cards =
document.querySelectorAll(".report-card");

function filterReports(){

    const query =
    searchInput.value.toLowerCase();

    const severity =
    severityFilter.value;

    cards.forEach(card => {

        const title =
        card.querySelector(".report-title")
        .textContent
        .toLowerCase();

        const location =
        card.querySelector(".location-link")
        .textContent
        .toLowerCase();

        const severityText =
        card.querySelectorAll(".badge")[1]
        .textContent;

        const matchSearch =

            title.includes(query)
            ||
            location.includes(query);

        const matchSeverity =

            severity === ""
            ||
            severityText.includes(severity + "/5");

        if(matchSearch && matchSeverity){

            card.style.display = "block";

        }else{

            card.style.display = "none";
        }

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

</body>
</html>


