<?php



class Equipment {

    
    private $id;
    private $type;
    private $model;
    private $purpose;
    private $quantity;

    
    public function __construct($id, $type, $model, $purpose, $quantity) {
        $this->id       = $id;
        $this->type     = $type;
        $this->model    = $model;
        $this->purpose  = $purpose;
        $this->quantity = $quantity;
    }

    
    public function getId()       { return $this->id; }
    public function getType()     { return $this->type; }
    public function getModel()    { return $this->model; }
    public function getPurpose()  { return $this->purpose; }
    public function getQuantity() { return $this->quantity; }

    
    public function setType($type)         { $this->type     = $type; }
    public function setModel($model)       { $this->model    = $model; }
    public function setPurpose($purpose)   { $this->purpose  = $purpose; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    
    public function getSummary() {
        return $this->type . " — " . $this->model .
               " (" . $this->quantity . " units)";
    }
}


require_once 'db_connect.php';


$addMessage = "";
$addError   = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {

    
    $type     = trim(mysqli_real_escape_string($conn, $_POST['type']));
    $model    = trim(mysqli_real_escape_string($conn, $_POST['model']));
    $purpose  = trim(mysqli_real_escape_string($conn, $_POST['purpose']));
    $quantity = intval($_POST['quantity']);

    
    if (empty($type) || empty($model) || empty($purpose) || $quantity < 1) {
        $addError = "All fields are required and quantity must be at least 1.";
    } else {
        
        $sql = "INSERT INTO equipment (type, model, purpose, quantity)
                VALUES ('$type', '$model', '$purpose', $quantity)";

        if (mysqli_query($conn, $sql)) {
            $addMessage = "Equipment item added successfully!";
        } else {
            $addError = "Database error: " . mysqli_error($conn);
        }
    }
}




$result = mysqli_query($conn, "SELECT * FROM equipment ORDER BY type, id");


$equipmentArray = [];
while ($row = mysqli_fetch_assoc($result)) {
    $equipmentArray[] = new Equipment(
        $row['id'],
        $row['type'],
        $row['model'],
        $row['purpose'],
        $row['quantity']
    );
}


function displayEquipmentTable($equipmentArray) {

    
    if (empty($equipmentArray)) {
        echo "<p class='text-warning'>No equipment records found.</p>";
        return;
    }

    
    echo '<div class="table-responsive">';
    echo '<table class="result-table">';
    echo '<thead>';
    echo '  <tr>';
    echo '    <th>#</th>';
    echo '    <th>Type</th>';
    echo '    <th>Model / Example</th>';
    echo '    <th>Purpose</th>';
    echo '    <th>Quantity</th>';
    echo '    <th>Summary</th>';
    echo '    <th>Action</th>';
    echo '  </tr>';
    echo '</thead>';
    echo '<tbody>';

    
    $counter = 1;
    foreach ($equipmentArray as $eq) {

        
        echo '<tr>';
        echo '  <td>' . $counter . '</td>';
        echo '  <td>' . htmlspecialchars($eq->getType())     . '</td>';
        echo '  <td>' . htmlspecialchars($eq->getModel())    . '</td>';
        echo '  <td>' . htmlspecialchars($eq->getPurpose())  . '</td>';
        echo '  <td>' . htmlspecialchars($eq->getQuantity()) . '</td>';
        echo '  <td>' . htmlspecialchars($eq->getSummary())  . '</td>';  
        echo '  <td>';
        echo '    <a href="manage.php?action=delete_eq&id=' . $eq->getId() . '"';
        echo '       class="btn btn-danger btn-sm"';
        echo '       onclick="return confirm(\'Delete this equipment?\')">Delete</a>';
        echo '  </td>';
        echo '</tr>';

        $counter++;
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}


$pageTitle  = "Equipment Management";
$activePage = "equipment";
include 'header.php';
?>


<div class="page-header" style="background:linear-gradient(135deg,#5a1200,#145b61);">
    <h1>Equipment Management</h1>
    <p>View, add, and manage Phantom House equipment inventory.</p>
</div>


<section class="section-maroon">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card bg-dark border-secondary p-4 mb-4">
                    <h4 class="mb-3" style="color:#fff;">&#43; Add New Equipment</h4>

                    
                    <?php if ($addMessage): ?>
                        <div class="alert-ph text-success mb-3">&#10003; <?php echo htmlspecialchars($addMessage); ?></div>
                    <?php endif; ?>
                    <?php if ($addError): ?>
                        <div class="alert-ph text-danger mb-3">&#10005; <?php echo htmlspecialchars($addError); ?></div>
                    <?php endif; ?>

                    
                    <form name="addEquipForm" action="equipment_process.php" method="POST" onsubmit="return validateEquipForm()">
                        <input type="hidden" name="action" value="add" />
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="eq_type">Type *</label>
                                <input type="text" id="eq_type" name="type" class="form-control" placeholder="e.g. Cameras" />
                                <span class="error-msg" id="err_type"></span>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="eq_model">Model / Example *</label>
                                <input type="text" id="eq_model" name="model" class="form-control" placeholder="e.g. ARRI Alexa Mini" />
                                <span class="error-msg" id="err_model"></span>
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="form-label" for="eq_purpose">Purpose *</label>
                                <input type="text" id="eq_purpose" name="purpose" class="form-control" placeholder="e.g. High-quality cinematic capture" />
                                <span class="error-msg" id="err_purpose"></span>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="eq_qty">Quantity *</label>
                                <input type="number" id="eq_qty" name="quantity" class="form-control" placeholder="e.g. 3" min="1" />
                                <span class="error-msg" id="err_qty"></span>
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-warning" value="Add Equipment" />
                                <input type="reset"  class="btn btn-outline-light ms-2" value="Clear" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section-teal">
    <div class="container">
        <h3 class="mb-4 text-center">Equipment Inventory
            <span class="badge bg-warning text-dark ms-2"><?php echo count($equipmentArray); ?> items</span>
        </h3>

        <?php
        
        displayEquipmentTable($equipmentArray);
        ?>

        <div class="text-center mt-4">
            <a href="search.php" class="btn btn-outline-light me-2">&#128269; Search Equipment</a>
            <a href="manage.php" class="btn btn-outline-warning">&#9881; Manage Database</a>
        </div>
    </div>
</section>

<?php

?>
<script>

function validateEquipForm() {

    
    var errorIds = ["err_type", "err_model", "err_purpose", "err_qty"];
    for (var i = 0; i < errorIds.length; i++) {
        document.getElementById(errorIds[i]).innerHTML = "";
    }

    var isValid = true;

    
    var type = document.getElementById("eq_type").value.trim();
    if (type === "") {
        document.getElementById("err_type").innerHTML = "Equipment type is required.";
        document.getElementById("eq_type").focus();
        isValid = false;
    } else if (type.search(/^[A-Za-z\s&\-\/]+$/) === -1) {
        document.getElementById("err_type").innerHTML = "Type must contain letters only.";
        document.getElementById("eq_type").focus();
        isValid = false;
    }

    
    var model = document.getElementById("eq_model").value.trim();
    if (model === "") {
        document.getElementById("err_model").innerHTML = "Model/Example is required.";
        if (isValid) { document.getElementById("eq_model").focus(); isValid = false; }
        else isValid = false;
    } else if (model.length < 3) {
        document.getElementById("err_model").innerHTML = "Model must be at least 3 characters.";
        isValid = false;
    }

    
    var purpose = document.getElementById("eq_purpose").value.trim();
    if (purpose === "") {
        document.getElementById("err_purpose").innerHTML = "Purpose description is required.";
        isValid = false;
    } else if (purpose.length < 10) {
        document.getElementById("err_purpose").innerHTML = "Purpose must be at least 10 characters.";
        isValid = false;
    }

    
    var qty = parseInt(document.getElementById("eq_qty").value);
    if (isNaN(qty) || document.getElementById("eq_qty").value === "") {
        document.getElementById("err_qty").innerHTML = "Quantity is required.";
        isValid = false;
    } else if (qty < 1) {
        document.getElementById("err_qty").innerHTML = "Quantity must be at least 1.";
        isValid = false;
    } else if (qty > 100) {
        document.getElementById("err_qty").innerHTML = "Quantity cannot exceed 100.";
        isValid = false;
    }

    
    return isValid;
}
</script>

<?php include 'footer.php'; ?>
