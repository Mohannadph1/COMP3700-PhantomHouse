<?php

if (!isset($pageTitle))  $pageTitle  = "Phantom House";
if (!isset($activePage)) $activePage = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Phantom House | <?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
    <style>
        :root { --maroon-dark:#260b04; --maroon:#5a1200; --teal:#145b61; --gold:#ffd6b8; }
        body  { font-family:'Lato',Arial,sans-serif; background-color:var(--maroon-dark); color:#eee; border:8px solid rgba(26,3,3,0.815); }
        h1,h2,h3,h4 { font-family:'Playfair Display',Georgia,serif; }
        .navbar { background-color:rgba(0,0,0,0.75)!important; }
        .navbar-brand { font-family:'Playfair Display',serif; font-size:1.4rem; letter-spacing:2px; color:#fff!important; }
        .nav-link { color:rgba(255,255,255,0.85)!important; }
        .nav-link:hover,.nav-link.active { color:var(--gold)!important; text-decoration:underline; }
        .section-maroon { background-color:var(--maroon); padding:50px 0; }
        .section-teal   { background-color:var(--teal); padding:50px 0; }
        .page-header { padding:60px 20px; text-align:center; }
        .page-header h1 { font-size:2.6rem; color:#fff; }
        .page-header p  { color:var(--gold); font-size:1.1rem; }
        .form-label { color:var(--gold)!important; font-weight:600; }
        .form-control,.form-select { background-color:rgba(255,255,255,0.08)!important; color:#fff!important; border-color:rgba(255,255,255,0.25)!important; }
        .form-control::placeholder { color:rgba(255,255,255,0.4); }
        .form-check-label { color:#eee!important; }
        .card.bg-dark .form-label { color:var(--gold)!important; }
        .card.bg-dark h3,.card.bg-dark h5 { color:#fff!important; }
        .error-msg { color:#ff6b6b; font-size:0.85rem; display:block; margin-top:4px; }
        .result-table { width:100%; border-collapse:collapse; margin-top:20px; }
        .result-table th { background-color:var(--gold); color:#000; padding:10px 14px; text-align:left; font-weight:700; }
        .result-table td { background-color:rgba(255,255,255,0.06); color:#eee; padding:10px 14px; border-bottom:1px solid rgba(255,255,255,0.1); }
        .result-table tr:hover td { background-color:rgba(255,255,255,0.12); }
        .alert-ph { background-color:rgba(20,91,97,0.5); border:1px solid var(--teal); border-radius:8px; padding:16px 20px; margin-bottom:20px; }
        .footer { background-color:var(--maroon-dark); text-align:center; padding:22px 10px; font-size:0.9rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.html">PHANTOM HOUSE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?php echo $activePage=='home'?'active':''; ?>"      href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $activePage=='about'?'active':''; ?>"     href="about.html">About</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $activePage=='services'?'active':''; ?>"  href="services.html">Services</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $activePage=='portfolio'?'active':''; ?>" href="portfolio.html">Portfolio</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $activePage=='equipment'?'active':''; ?>" href="equipment.html">Equipment</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $activePage=='clients'?'active':''; ?>"   href="clients.html">Clients</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $activePage=='contact'?'active':''; ?>"   href="contact.html">Contact</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning <?php echo $activePage=='feedback'?'active':''; ?>"    href="questionnaire.html">Feedback</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning <?php echo $activePage=='calculator'?'active':''; ?>"  href="calculator.html">Calculator</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning <?php echo $activePage=='fun'?'active':''; ?>"          href="funpage.html">Fun</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-info <?php echo $activePage=='search'?'active':''; ?>"         href="search.php">&#128269; Search</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-info <?php echo $activePage=='manage'?'active':''; ?>"         href="manage.php">&#9881; Manage</a></li>
            </ul>
        </div>
    </div>
</nav>
