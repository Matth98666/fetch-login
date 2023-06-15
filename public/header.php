<header id="dashheader" class="d-flex flex-wrap justify-content-between align-middle py-3 mb-4 border-bottom">
    <a href="dashboard.php" title="Back to Dashboard home">
        <img class="toplogo" src="imgs/syradev.svg" alt="Syradev &copy; <?= date('Y'); ?>">
    </a>
    <span class="fs-4">Dashboard</span>
    <?php
    if (isset($_SESSION['username'])) {
        echo '<p class="small mt-2">Hello ' . $_SESSION['firstname'] . ' ' . $_SESSION['lastname'] . ' ;-)</p>';
    }
    ?>
    <ul class="nav">
        <li class="nav-item">
            <a href="listusers.php" class="btn btn-secondary" aria-current="page">Utilisateurs</a>
            <a href="#" id="logout" class="btn btn-secondary" aria-current="page">Se déconnecter</a>
        </li>
    </ul>
</header>