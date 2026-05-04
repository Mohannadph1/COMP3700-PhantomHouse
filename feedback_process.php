<?php


require_once 'db_connect.php';


$submitted  = false;
$errors     = [];
$formData   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $fullName   = trim(mysqli_real_escape_string($conn, $_POST['fbName']    ?? ''));
    $email      = trim(mysqli_real_escape_string($conn, $_POST['fbEmail']   ?? ''));
    $phone      = trim(mysqli_real_escape_string($conn, $_POST['fbPhone']   ?? ''));
    $service    = trim(mysqli_real_escape_string($conn, $_POST['fbService'] ?? ''));
    $experience = trim(mysqli_real_escape_string($conn, $_POST['fbExp']     ?? ''));
    $comments   = trim(mysqli_real_escape_string($conn, $_POST['fbComments']?? ''));
    $agreed     = isset($_POST['fbAgree']) ? 1 : 0;

    
    $formData = compact('fullName','email','phone','service','experience','comments');

    

    
    if (empty($fullName)) {
        $errors[] = "Full name is required.";
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $fullName)) {
        $errors[] = "Name must contain letters only.";
    }

    
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[97]\d{7}$/', $phone)) {
        $errors[] = "Phone must be 8 digits starting with 9 or 7.";
    }

    
    if (empty($service)) {
        $errors[] = "Please select the service you used.";
    }

    
    if (empty($experience)) {
        $errors[] = "Please select your overall experience rating.";
    }

    
    if (empty($comments)) {
        $errors[] = "Comments are required.";
    } elseif (strlen($comments) < 20) {
        $errors[] = "Comments must be at least 20 characters.";
    }

    
    if (!$agreed) {
        $errors[] = "You must agree to the terms before submitting.";
    }

    
    if (empty($errors)) {
        $sql = "INSERT INTO feedback (full_name, email, phone, service, experience, comments, agreed)
                VALUES ('$fullName', '$email', '$phone', '$service', '$experience', '$comments', $agreed)";

        if (mysqli_query($conn, $sql)) {
            $submitted  = true;
            $insertedId = mysqli_insert_id($conn);
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}

$pageTitle  = "Feedback Submitted";
$activePage = "feedback";
include 'header.php';
?>

<div class="page-header" style="background:linear-gradient(135deg,#5a1200,#145b61);">
    <h1>Feedback Form</h1>
    <p>Submit your experience with Phantom House</p>
</div>

<section class="section-maroon">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

<?php if ($submitted): ?>
    
    <div class="alert-ph mb-4">
        <h4 class="text-success mb-1">&#10003; Thank You, <?php echo htmlspecialchars($fullName); ?>!</h4>
        <p class="mb-0">Your feedback has been saved successfully. Reference ID: <strong>#<?php echo $insertedId; ?></strong></p>
    </div>

    <h5 class="mb-3" style="color:var(--gold);">Your Submission Summary:</h5>
    <div class="table-responsive">
        <table class="result-table">
            <thead>
                <tr><th>Field</th><th>Your Response</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>Full Name</strong></td>  <td><?php echo htmlspecialchars($fullName);   ?></td></tr>
                <tr><td><strong>Email</strong></td>       <td><?php echo htmlspecialchars($email);      ?></td></tr>
                <tr><td><strong>Phone</strong></td>       <td><?php echo htmlspecialchars($phone);      ?></td></tr>
                <tr><td><strong>Service Used</strong></td><td><?php echo htmlspecialchars($service);    ?></td></tr>
                <tr><td><strong>Experience</strong></td>  <td><?php echo htmlspecialchars($experience); ?></td></tr>
                <tr><td><strong>Comments</strong></td>    <td><?php echo nl2br(htmlspecialchars($comments)); ?></td></tr>
                <tr><td><strong>Submitted At</strong></td><td><?php echo date('l, d F Y  H:i:s'); ?></td></tr>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="questionnaire.html" class="btn btn-warning">Submit Another Response</a>
        <a href="index.html" class="btn btn-outline-light ms-2">Back to Home</a>
    </div>

<?php elseif (!empty($errors)): ?>
    
    <div class="card bg-dark border-danger p-4 mb-4">
        <h5 class="text-danger mb-3">&#10005; Please fix the following errors:</h5>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li class="text-danger"><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="questionnaire.html" class="btn btn-outline-light mt-2">Go Back to Form</a>
    </div>

<?php else: ?>
    
    <div class="alert-ph">
        <p>Please submit the <a href="questionnaire.html" class="text-warning">Feedback Form</a> to see results here.</p>
    </div>
<?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
