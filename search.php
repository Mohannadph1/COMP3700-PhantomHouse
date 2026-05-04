<?php


require_once 'db_connect.php';


$searchTerm   = trim($_GET['keyword']  ?? '');
$searchTable  = trim($_GET['table']    ?? 'all');
$searchStatus = trim($_GET['status']   ?? '');
$searchYear   = intval($_GET['year']   ?? 0);


$equipResults   = [];
$clientResults  = [];
$feedbackResults= [];
$searched       = false;


if (isset($_GET['keyword']) || isset($_GET['status']) || isset($_GET['year'])) {
    $searched = true;
    $kw = mysqli_real_escape_string($conn, $searchTerm);

    
    if ($searchTable === 'all' || $searchTable === 'equipment') {
        $sql = "SELECT * FROM equipment WHERE 1=1";
        if (!empty($kw)) {
            $sql .= " AND (type LIKE '%$kw%' OR model LIKE '%$kw%' OR purpose LIKE '%$kw%')";
        }
        $sql .= " ORDER BY type";
        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            $equipResults[] = $row;
        }
    }

    
    if ($searchTable === 'all' || $searchTable === 'clients') {
        $sql = "SELECT * FROM clients WHERE 1=1";
        if (!empty($kw)) {
            $sql .= " AND (name LIKE '%$kw%' OR sector LIKE '%$kw%' OR project_type LIKE '%$kw%')";
        }
        if (!empty($searchStatus)) {
            $st  = mysqli_real_escape_string($conn, $searchStatus);
            $sql .= " AND status = '$st'";
        }
        if ($searchYear > 0) {
            $sql .= " AND year = $searchYear";
        }
        $sql .= " ORDER BY year DESC";
        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            $clientResults[] = $row;
        }
    }

    
    if ($searchTable === 'all' || $searchTable === 'feedback') {
        $sql = "SELECT * FROM feedback WHERE 1=1";
        if (!empty($kw)) {
            $sql .= " AND (full_name LIKE '%$kw%' OR service LIKE '%$kw%' OR experience LIKE '%$kw%' OR comments LIKE '%$kw%')";
        }
        $sql .= " ORDER BY submitted_at DESC";
        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            $feedbackResults[] = $row;
        }
    }
}

$pageTitle  = "Search Database";
$activePage = "search";
include 'header.php';
?>

<div class="page-header" style="background:linear-gradient(135deg,#145b61,#260b04);">
    <h1>&#128269; Search Database</h1>
    <p>Search equipment, clients, and feedback records</p>
</div>


<section class="section-maroon">
    <div class="container">
        <div class="card bg-dark border-secondary p-4 mb-5">
            <h5 class="mb-3" style="color:#fff;">Search Criteria</h5>

            
            <form name="searchForm" action="search.php" method="GET" onsubmit="return validateSearchForm()">
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="keyword">Keyword</label>
                        <input type="text" id="keyword" name="keyword" class="form-control"
                               placeholder="e.g. Camera, Oman, Documentary..."
                               value="<?php echo htmlspecialchars($searchTerm); ?>" />
                        <span class="error-msg" id="err_keyword"></span>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label" for="table">Search In</label>
                        <select id="table" name="table" class="form-select">
                            <option value="all"      <?php echo $searchTable==='all'     ?'selected':''; ?>>All Tables</option>
                            <option value="equipment"<?php echo $searchTable==='equipment'?'selected':''; ?>>Equipment</option>
                            <option value="clients"  <?php echo $searchTable==='clients' ?'selected':''; ?>>Clients</option>
                            <option value="feedback" <?php echo $searchTable==='feedback'?'selected':''; ?>>Feedback</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">All</option>
                            <option value="Completed"<?php echo $searchStatus==='Completed'?'selected':''; ?>>Completed</option>
                            <option value="Ongoing"  <?php echo $searchStatus==='Ongoing'  ?'selected':''; ?>>Ongoing</option>
                            <option value="Upcoming" <?php echo $searchStatus==='Upcoming' ?'selected':''; ?>>Upcoming</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label" for="year">Year</label>
                        <input type="number" id="year" name="year" class="form-control"
                               placeholder="e.g. 2025" min="2000" max="2030"
                               value="<?php echo $searchYear > 0 ? $searchYear : ''; ?>" />
                        <span class="error-msg" id="err_year"></span>
                    </div>
                    <div class="col-12">
                        <input type="submit" class="btn btn-warning px-4" value="&#128269; Search" />
                        <a href="search.php" class="btn btn-outline-light ms-2">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($searched): ?>

        
        <?php if ($searchTable === 'all' || $searchTable === 'equipment'): ?>
        <div class="mb-5">
            <h4 style="color:var(--gold);">Equipment Results
                <span class="badge bg-warning text-dark ms-2"><?php echo count($equipResults); ?></span>
            </h4>
            <?php if (empty($equipResults)): ?>
                <p class="text-secondary">No equipment found matching your search.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="result-table">
                        <thead><tr><th>#</th><th>Type</th><th>Model</th><th>Purpose</th><th>Qty</th></tr></thead>
                        <tbody>
                            <?php $n=1; foreach ($equipResults as $r): ?>
                            <tr>
                                <td><?php echo $n++; ?></td>
                                <td><?php echo htmlspecialchars($r['type']);     ?></td>
                                <td><?php echo htmlspecialchars($r['model']);    ?></td>
                                <td><?php echo htmlspecialchars($r['purpose']);  ?></td>
                                <td><?php echo htmlspecialchars($r['quantity']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <?php if ($searchTable === 'all' || $searchTable === 'clients'): ?>
        <div class="mb-5">
            <h4 style="color:var(--gold);">Client Results
                <span class="badge bg-warning text-dark ms-2"><?php echo count($clientResults); ?></span>
            </h4>
            <?php if (empty($clientResults)): ?>
                <p class="text-secondary">No clients found matching your search.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="result-table">
                        <thead><tr><th>#</th><th>Name</th><th>Sector</th><th>Project</th><th>Year</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php $n=1; foreach ($clientResults as $r):
                                $bc = $r['status']==='Completed' ? 'bg-success' :
                                     ($r['status']==='Ongoing'   ? 'bg-warning text-dark' : 'bg-info text-dark');
                            ?>
                            <tr>
                                <td><?php echo $n++; ?></td>
                                <td><?php echo htmlspecialchars($r['name']);         ?></td>
                                <td><?php echo htmlspecialchars($r['sector']);       ?></td>
                                <td><?php echo htmlspecialchars($r['project_type']); ?></td>
                                <td><?php echo htmlspecialchars($r['year']);         ?></td>
                                <td><span class="badge <?php echo $bc; ?>"><?php echo $r['status']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <?php if ($searchTable === 'all' || $searchTable === 'feedback'): ?>
        <div class="mb-5">
            <h4 style="color:var(--gold);">Feedback Results
                <span class="badge bg-warning text-dark ms-2"><?php echo count($feedbackResults); ?></span>
            </h4>
            <?php if (empty($feedbackResults)): ?>
                <p class="text-secondary">No feedback found matching your search.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="result-table">
                        <thead><tr><th>#</th><th>Name</th><th>Service</th><th>Experience</th><th>Comments</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php $n=1; foreach ($feedbackResults as $r): ?>
                            <tr>
                                <td><?php echo $n++; ?></td>
                                <td><?php echo htmlspecialchars($r['full_name']);   ?></td>
                                <td><?php echo htmlspecialchars($r['service']);     ?></td>
                                <td><?php echo htmlspecialchars($r['experience']);  ?></td>
                                <td><?php echo htmlspecialchars(substr($r['comments'],0,60)).'...'; ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($r['submitted_at']))); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <div class="alert-ph text-center">
            <strong>Total results found:
            <?php echo count($equipResults) + count($clientResults) + count($feedbackResults); ?>
            </strong>
            <?php if (!empty($searchTerm)): ?>
                &nbsp;for keyword: <em>"<?php echo htmlspecialchars($searchTerm); ?>"</em>
            <?php endif; ?>
        </div>

        <?php endif;  ?>

    </div>
</section>

<script>

function validateSearchForm() {
    document.getElementById("err_keyword").innerHTML = "";
    document.getElementById("err_year").innerHTML    = "";

    var keyword = document.getElementById("keyword").value.trim();
    var year    = document.getElementById("year").value.trim();
    var table   = document.getElementById("table").value;
    var isValid = true;

    
    var status = document.getElementById("status").value;
    if (keyword === "" && year === "" && status === "") {
        document.getElementById("err_keyword").innerHTML =
            "Please enter a keyword or select at least one filter.";
        document.getElementById("keyword").focus();
        isValid = false;
    }

    
    if (year !== "") {
        var yr = parseInt(year);
        if (isNaN(yr) || yr < 2000 || yr > 2030) {
            document.getElementById("err_year").innerHTML =
                "Year must be between 2000 and 2030.";
            isValid = false;
        }
    }

    return isValid;
}
</script>

<?php include 'footer.php'; ?>
