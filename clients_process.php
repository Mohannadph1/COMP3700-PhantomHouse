<?php


require_once 'db_connect.php';


$submitted = false;
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $name        = trim(mysqli_real_escape_string($conn, $_POST['fullname2']   ?? ''));
    $email       = trim(mysqli_real_escape_string($conn, $_POST['email2']      ?? ''));
    $projectType = trim(mysqli_real_escape_string($conn, $_POST['project2']    ?? ''));
    $year        = intval($_POST['year2'] ?? date('Y'));
    $status      = 'Upcoming'; 

    
    if (empty($name)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (empty($projectType)) {
        $errors[] = "Project type is required.";
    }

    
    $sectorMap = [
        'commercial'    => 'Commercial',
        'event'         => 'Events',
        'documentary'   => 'Arts & Media',
        'creative_film' => 'Creative'
    ];
    $sector = $sectorMap[$projectType] ?? 'General';

    
    $typeMap = [
        'commercial'    => 'Commercial Ad',
        'event'         => 'Event Coverage',
        'documentary'   => 'Documentary',
        'creative_film' => 'Creative Film'
    ];
    $projectLabel = $typeMap[$projectType] ?? $projectType;

    if (empty($errors)) {
        $sql = "INSERT INTO clients (name, sector, project_type, year, status)
                VALUES ('$name', '$sector', '$projectLabel', $year, '$status')";

        if (mysqli_query($conn, $sql)) {
            $submitted = true;
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}


$result = mysqli_query($conn, "SELECT * FROM clients ORDER BY year DESC, id DESC");
$allClients = [];
while ($row = mysqli_fetch_assoc($result)) {
    $allClients[] = $row;
}

$pageTitle  = "Client Portfolio";
$activePage = "clients";
include 'header.php';
?>

<div class="page-header" style="background:linear-gradient(135deg,#260b04,#145b61);">
    <h1>Client Portfolio</h1>
    <p>Project request submissions and client database</p>
</div>

<section class="section-maroon">
    <div class="container">

        <?php if ($submitted): ?>
        <div class="alert-ph text-success mb-4">
            &#10003; <strong>Project request submitted successfully!</strong>
            Your request has been added to the client database.
        </div>
        <?php elseif (!empty($errors)): ?>
        <div class="card bg-dark border-danger p-4 mb-4">
            <h5 class="text-danger">&#10005; Submission Errors:</h5>
            <ul><?php foreach ($errors as $e): ?>
                <li class="text-danger"><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?></ul>
            <a href="clients.html" class="btn btn-outline-light mt-2">Go Back</a>
        </div>
        <?php endif; ?>

        
        <h3 class="mb-4 text-center">All Clients
            <span class="badge bg-warning text-dark ms-2"><?php echo count($allClients); ?> records</span>
        </h3>

        <div class="table-responsive">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Sector</th>
                        <th>Project Type</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    $counter = 1;
                    foreach ($allClients as $client):

                        
                        $badgeClass = 'bg-secondary';
                        if ($client['status'] === 'Completed') $badgeClass = 'bg-success';
                        elseif ($client['status'] === 'Ongoing')   $badgeClass = 'bg-warning text-dark';
                        elseif ($client['status'] === 'Upcoming')  $badgeClass = 'bg-info text-dark';
                    ?>
                    <tr>
                        <td><?php echo $counter; ?></td>
                        <td><?php echo htmlspecialchars($client['name']);         ?></td>
                        <td><?php echo htmlspecialchars($client['sector']);       ?></td>
                        <td><?php echo htmlspecialchars($client['project_type']); ?></td>
                        <td><?php echo htmlspecialchars($client['year']);         ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $client['status']; ?></span></td>
                        <td>
                            <a href="manage.php?action=delete_client&id=<?php echo $client['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this client record?')">Delete</a>
                            <a href="manage.php?action=edit_client&id=<?php echo $client['id']; ?>"
                               class="btn btn-warning btn-sm ms-1">Edit</a>
                        </td>
                    </tr>
                    <?php $counter++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="clients.html" class="btn btn-outline-light me-2">&#43; Add New Client Request</a>
            <a href="search.php"   class="btn btn-outline-warning me-2">&#128269; Search</a>
            <a href="manage.php"   class="btn btn-outline-info">&#9881; Manage</a>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>
