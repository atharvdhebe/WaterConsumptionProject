<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<nav class="navbar navbar-expand-lg navbar-dark water-navbar">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="/WaterConsumptionProject/index.php"
        >
            💧 WaterWise
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/WaterConsumptionProject/index.php"
                    >
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/WaterConsumptionProject/about.php"
                    >
                        About
                    </a>
                </li>

                <?php if (isset($_SESSION["user_id"])): ?>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="/WaterConsumptionProject/dashboard.php"
                        >
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="/WaterConsumptionProject/prediction.php"
                        >
                            Prediction
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="/WaterConsumptionProject/history.php"
                        >
                            History
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="/WaterConsumptionProject/analytics.php"
                        >
                            Analytics
                        </a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a
                            class="btn btn-light btn-sm"
                            href="/WaterConsumptionProject/logout.php"
                        >
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="/WaterConsumptionProject/login.php"
                        >
                            Login
                        </a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a
                            class="btn btn-light btn-sm"
                            href="/WaterConsumptionProject/register.php"
                        >
                            Register
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>

    </div>

</nav>