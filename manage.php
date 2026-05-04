<?php


require_once 'db_connect.php';


$action  = $_GET['action']  ?? $_POST['action']  ?? '';
$id      = intval($_GET['id'] ?? $_POST['id'] ?? 0);
$message = '';
$msgType = 'success';
$editClient = null;




if ($action === 'delete_eq' && $id > 0) {
    $sql = "DELETE FROM equipment WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $message = "Equipment record #$id deleted successfully.";
    } else {
        $message = "Error deleting equipment: " . mysqli_error($conn);
        $msgType = 'danger';
    }
}


if ($action === 'delete_client' && $id > 0) {
    $sql = "DELETE FROM clients WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $message = "Client record #$id deleted successfully.";
    } else {
        $message = "Error deleting client: " . mysqli_error($conn);
        $msgType = 'danger';
    }
}


if ($action === 'edit_client' && $id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM clients WHERE id = $id");
    $editClient = mysqli_fetch_assoc($res);
    if (!$editClient) {
        $message = "Client record #$id not found.";
        $msgType = 'danger';
    }
}


if ($action === 'update_client' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim(mysqli_real_escape_string($conn, $_POST['name']         ?? ''));
    $sector      = trim(mysqli_real_escape_string($conn, $_POST['sector']       ?? ''));
    $projectType = trim(mysqli_real_escape_string($conn, $_POST['project_type'] ?? ''));
    $year        = intval($_POST['year']   ?? date('Y'));
    $status      = trim(mysqli_real_escape_string($conn, $_POST['status']       ?? 'Upcoming'));

    
    if (empty($name) || empty($sector) || empty($projectType) || $year < 2000) {
        $message = "All fields are required and year must be valid.";
        $msgType = 'danger';
        
        $editClient = [
            'id' => $id, 'name' => $name, 'sector' => $sector,
            'project_type' => $projectType, 'year' => $year, 'status' => $status
        ];
        $action = 'edit_client';
    } else {
        $sql = "UPDATE clients
                SET name = '$name', sector = '$sector',
                    project_type = '$projectType', year = $year, status = '$status'
                WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $message = "Client record #$id updated successfully.";
        } else {
            $message = "Error updating client: " . mysqli_error($conn);
            $msgType = 'danger';
        }
    }
}


if ($action === 'insert_client' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim(mysqli_real_escape_string($conn, $_POST['name']         ?? ''));
    $sector      = trim(mysqli_real_escape_string($conn, $_POST['sector']       ?? ''));
    $projectType = trim(mysqli_real_escape_string($conn, $_POST['project_type'] ?? ''));
    $year        = intval($_POST['year']   ?? date('Y'));
    $status      = trim(mysqli_real_escape_string($conn, $_POST['status']       ?? 'Upcoming'));

    if (empty($name) || empty($sector) || empty($projectType)) {
        $message = "All fields are required.";
        $msgType = 'danger';
    } else {
        $sql = "INSERT INTO clients (name, sector, project_type, year, status)
                VALUES ('$name', '$sector', '$projectType', $year, '$status')";
        if (mysqli_query($conn, $sql)) {
            $message = "New client added successfully! ID: " . mysqli_insert_id($conn);
        } else {
            $message = "Error inserting client: " . mysqli_error($conn);
            $msgType = 'danger';
        }
    }
}


$equipList  = [];
$res = mysqli_query($conn, "SELECT * FROM equipment ORDER BY type, id");
while ($row = mysqli_fetch_assoc($res)) { $equipList[] = $row; }

$clientList = [];
$res = mysqli_query($conn, "SELECT * FROM clients ORDER BY year DESC, id DESC");
while ($row = mysqli_fetch_assoc($res)) { $clientList[] = $row; }

$feedbackList = [];
$res = mysqli_query($conn, "SELECT * FROM feedback ORDER BY submitted_at DESC");
while ($row = mysqli_fetch_assoc($res)) { $feedbackList[] = $row; }

$pageTitle  = "Database Management";
$activePage = "manage";
include 'header.php';
?>

<div class="page-header" style="background:linear-gradient(135deg,#5a1200,#145b61);">
    <h1>&#9881; Database Management</h1>
    <p>Insert, update, and delete records across all tables</p>
</div>

<section class="section-maroon">
<div class="container">

    
    <?php if ($message): ?>
    <div class="alert-ph mb-4 text-<?php echo $msgType; ?>">
        <?php echo $msgType === 'success' ? '&#10003;' : '&#10005;'; ?>
        <strong><?php echo htmlspecialchars($message); ?></strong>
    </div>
    <?php endif; ?>

    
    <div class="card bg-dark border-secondary p-4 mb-5">
        <h4 style="color:var(--gold);">&#43; Insert New Client</h4>
        <form name="insertClientForm" action="manage.php" method="POST"
              onsubmit="return validateInsertForm()">
            <input type="hidden" name="action" value="insert_client" />
            <div class="row g-3 mt-1">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="ins_name">Client Name *</label>
                    <input type="text" id="ins_name" name="name" class="form-control"
                           placeholder="e.g. Oman Tourism Authority" />
                    <span class="error-msg" id="err_ins_name"></span>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="ins_sector">Sector *</label>
                    <input type="text" id="ins_sector" name="sector" class="form-control"
                           placeholder="e.g. Tourism" />
                    <span class="error-msg" id="err_ins_sector"></span>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label" for="ins_project">Project Type *</label>
                    <input type="text" id="ins_project" name="project_type" class="form-control"
                           placeholder="e.g. Promotional Film" />
                    <span class="error-msg" id="err_ins_project"></span>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="ins_year">Year *</label>
                    <input type="number" id="ins_year" name="year" class="form-control"
                           value="<?php echo date('Y'); ?>" min="2000" max="2030" />
                    <span class="error-msg" id="err_ins_year"></span>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="ins_status">Status</label>
                    <select id="ins_status" name="status" class="form-select">
                        <option value="Upcoming">Upcoming</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="col-12">
                    <input type="submit" class="btn btn-success px-4" value="Insert Client" />
                    <input type="reset"  class="btn btn-outline-light ms-2" value="Clear" />
                </div>
            </div>
        </form>
    </div>

    
    <?php if ($action === 'edit_client' && $editClient): ?>
    <div class="card bg-dark border-warning p-4 mb-5">
        <h4 style="color:var(--gold);">&#9998; Update Client — ID #<?php echo $editClient['id']; ?></h4>
        <form name="updateClientForm" action="manage.php" method="POST"
              onsubmit="return validateUpdateForm()">
            <input type="hidden" name="action" value="update_client" />
            <input type="hidden" name="id"     value="<?php echo $editClient['id']; ?>" />
            <div class="row g-3 mt-1">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="upd_name">Client Name *</label>
                    <input type="text" id="upd_name" name="name" class="form-control"
                           value="<?php echo htmlspecialchars($editClient['name']); ?>" />
                    <span class="error-msg" id="err_upd_name"></span>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="upd_sector">Sector *</label>
                    <input type="text" id="upd_sector" name="sector" class="form-control"
                           value="<?php echo htmlspecialchars($editClient['sector']); ?>" />
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label" for="upd_project">Project Type *</label>
                    <input type="text" id="upd_project" name="project_type" class="form-control"
                           value="<?php echo htmlspecialchars($editClient['project_type']); ?>" />
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="upd_year">Year *</label>
                    <input type="number" id="upd_year" name="year" class="form-control"
                           value="<?php echo $editClient['year']; ?>" min="2000" max="2030" />
                    <span class="error-msg" id="err_upd_year"></span>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="upd_status">Status</label>
                    <select id="upd_status" name="status" class="form-select">
                        <?php foreach (['Upcoming','Ongoing','Completed'] as $s): ?>
                        <option value="<?php echo $s; ?>"
                            <?php echo $editClient['status']===$s ? 'selected' : ''; ?>>
                            <?php echo $s; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <input type="submit" class="btn btn-warning px-4" value="Save Update" />
                    <a href="manage.php" class="btn btn-outline-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>

    
    <h3 class="mb-3" style="color:var(--gold);">Clients Table
        <span class="badge bg-warning text-dark ms-2"><?php echo count($clientList); ?></span>
    </h3>
    <div class="table-responsive mb-5">
        <table class="result-table">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Sector</th><th>Project</th><th>Year</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($clientList as $c):
                    $bc = $c['status']==='Completed'?'bg-success':($c['status']==='Ongoing'?'bg-warning text-dark':'bg-info text-dark');
                ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['name']);         ?></td>
                    <td><?php echo htmlspecialchars($c['sector']);       ?></td>
                    <td><?php echo htmlspecialchars($c['project_type']); ?></td>
                    <td><?php echo $c['year']; ?></td>
                    <td><span class="badge <?php echo $bc; ?>"><?php echo $c['status']; ?></span></td>
                    <td>
                        <a href="manage.php?action=edit_client&id=<?php echo $c['id']; ?>"
                           class="btn btn-warning btn-sm">Edit</a>
                        <a href="manage.php?action=delete_client&id=<?php echo $c['id']; ?>"
                           class="btn btn-danger btn-sm ms-1"
                           onclick="return confirm('Delete client <?php echo htmlspecialchars(addslashes($c['name'])); ?>?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    
    <h3 class="mb-3" style="color:var(--gold);">Equipment Table
        <span class="badge bg-warning text-dark ms-2"><?php echo count($equipList); ?></span>
    </h3>
    <div class="table-responsive mb-5">
        <table class="result-table">
            <thead>
                <tr><th>ID</th><th>Type</th><th>Model</th><th>Purpose</th><th>Qty</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($equipList as $e): ?>
                <tr>
                    <td><?php echo $e['id']; ?></td>
                    <td><?php echo htmlspecialchars($e['type']);     ?></td>
                    <td><?php echo htmlspecialchars($e['model']);    ?></td>
                    <td><?php echo htmlspecialchars($e['purpose']);  ?></td>
                    <td><?php echo $e['quantity']; ?></td>
                    <td>
                        <a href="manage.php?action=delete_eq&id=<?php echo $e['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this equipment?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    
    <h3 class="mb-3" style="color:var(--gold);">Feedback Submissions
        <span class="badge bg-warning text-dark ms-2"><?php echo count($feedbackList); ?></span>
    </h3>
    <div class="table-responsive mb-5">
        <table class="result-table">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Service</th><th>Rating</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($feedbackList as $f): ?>
                <tr>
                    <td><?php echo $f['id']; ?></td>
                    <td><?php echo htmlspecialchars($f['full_name']);   ?></td>
                    <td><?php echo htmlspecialchars($f['email']);       ?></td>
                    <td><?php echo htmlspecialchars($f['service']);     ?></td>
                    <td><?php echo htmlspecialchars($f['experience']);  ?></td>
                    <td><?php echo date('d M Y', strtotime($f['submitted_at'])); ?></td>
                    <td>
                        <a href="manage.php?action=delete_feedback&id=<?php echo $f['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this feedback?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</section>

<script>

function validateInsertForm() {

    
    var ids = ["err_ins_name","err_ins_sector","err_ins_project","err_ins_year"];
    for (var i = 0; i < ids.length; i++) {
        document.getElementById(ids[i]).innerHTML = "";
    }
    var isValid = true;

    
    var name = document.getElementById("ins_name").value.trim();
    if (name === "") {
        document.getElementById("err_ins_name").innerHTML = "Client name is required.";
        document.getElementById("ins_name").focus();
        isValid = false;
    } else if (name.length < 3) {
        document.getElementById("err_ins_name").innerHTML = "Name must be at least 3 characters.";
        isValid = false;
    }

    
    var sector = document.getElementById("ins_sector").value.trim();
    if (sector === "") {
        document.getElementById("err_ins_sector").innerHTML = "Sector is required.";
        isValid = false;
    } else if (sector.search(/^[A-Za-z\s&\-]+$/) === -1) {
        document.getElementById("err_ins_sector").innerHTML = "Sector must contain letters only.";
        isValid = false;
    }

    
    var project = document.getElementById("ins_project").value.trim();
    if (project === "") {
        document.getElementById("err_ins_project").innerHTML = "Project type is required.";
        isValid = false;
    } else if (project.length < 5) {
        document.getElementById("err_ins_project").innerHTML = "Project type must be at least 5 characters.";
        isValid = false;
    }

    
    var year = parseInt(document.getElementById("ins_year").value);
    if (isNaN(year)) {
        document.getElementById("err_ins_year").innerHTML = "Year is required.";
        isValid = false;
    } else if (year < 2000 || year > 2030) {
        document.getElementById("err_ins_year").innerHTML = "Year must be between 2000 and 2030.";
        isValid = false;
    }

    return isValid;
}


function validateUpdateForm() {
    document.getElementById("err_upd_name").innerHTML = "";
    document.getElementById("err_upd_year").innerHTML = "";
    var isValid = true;

    var name = document.getElementById("upd_name").value.trim();
    if (name === "") {
        document.getElementById("err_upd_name").innerHTML = "Client name is required.";
        document.getElementById("upd_name").focus();
        isValid = false;
    }

    var year = parseInt(document.getElementById("upd_year").value);
    if (isNaN(year) || year < 2000 || year > 2030) {
        document.getElementById("err_upd_year").innerHTML = "Year must be between 2000 and 2030.";
        isValid = false;
    }

    return isValid;
}
</script>

<?php

if ($action === 'delete_feedback' && $id > 0) {
    mysqli_query($conn, "DELETE FROM feedback WHERE id = $id");
}
include 'footer.php';
?>
