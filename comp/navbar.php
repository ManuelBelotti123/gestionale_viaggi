<nav class="navbar navbar-expand-lg navbar-dark shadow">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="/gestionale_viaggi/img/logo.png" alt="Travel Manager" height="40" class="d-inline-block align-text-top me-2">
        </a>
        <!-- Toggle button for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="/gestionale_viaggi/dashboard_choose.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <!-- Profile dropdown -->
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="p-2 bi bi-person-circle"></i>
                                <span></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="/gestionale_viaggi/profile.php">Profilo</a></li>
                                <li><a class="dropdown-item" href="/gestionale_viaggi/settings.php">Impostazioni</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/gestionale_viaggi/auth/logout.php">Logout</a></li>
                            </ul>
                        </div>
                </li>
            </ul>
        </div>
    </div>
</nav>