<?php
$page_title = "WaterWise | Smart Water Management";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center min-vh-75">

            <div class="col-lg-7">

                <h1 class="display-4 fw-bold">
                    Understand Your Water.
                    <br>
                    <span class="text-primary">Use It Wisely.</span>
                </h1>

                <p class="lead text-secondary mt-3">
                    WaterWise helps households understand their daily water
                    usage, estimate consumption and discover their water
                    consumption patterns.
                </p>

                <p class="text-secondary">
                    From bathing and laundry to gardening and vehicle washing,
                    understand how everyday activities contribute to your
                    household's water usage.
                </p>

                <div class="mt-4">

                    <?php if (isset($_SESSION["user_id"])): ?>

                        <a href="prediction.php"
                           class="btn btn-primary btn-lg me-2">
                            Check My Usage
                        </a>

                        <a href="dashboard.php"
                           class="btn btn-outline-primary btn-lg">
                            Dashboard
                        </a>

                    <?php else: ?>

                        <a href="register.php"
                           class="btn btn-primary btn-lg me-2">
                            Get Started
                        </a>

                        <a href="about.php"
                           class="btn btn-outline-primary btn-lg">
                            Learn More
                        </a>

                    <?php endif; ?>

                </div>

            </div>


            <div class="col-lg-5 mt-4 mt-lg-0">

                <div class="water-card hero-card">

                    <div class="display-1">💧</div>

                    <h2>WaterWise</h2>

                    <p class="text-secondary">
                        A smarter way to understand household water
                        consumption.
                    </p>

                </div>

            </div>

        </div>
    </div>
</section>


<!-- Features -->
<section class="container py-5">

    <div class="text-center mb-5">

        <h2>What WaterWise Can Do</h2>

        <p class="text-secondary">
            Simple tools to help you understand your household water usage.
        </p>

    </div>


    <div class="row g-4">

        <div class="col-md-4">

            <div class="water-card h-100">

                <h4>💧 Understand</h4>

                <p>
                    Enter information about household activities such as
                    bathing, laundry, dishwashing, gardening and vehicle
                    washing.
                </p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="water-card h-100">

                <h4>🤖 Predict</h4>

                <p>
                    Get an estimated daily water consumption using your
                    household information and our machine learning
                    prediction system.
                </p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="water-card h-100">

                <h4>📊 Analyze</h4>

                <p>
                    Explore your consumption patterns through simple
                    statistics, charts and historical information.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- Household Activities -->
<section class="hero-section py-5">

    <div class="container">

        <div class="text-center mb-5">

            <h2>Every Drop Has a Story</h2>

            <p class="text-secondary">
                WaterWise considers the activities that contribute to
                everyday household water consumption.
            </p>

        </div>


        <div class="row g-4">

            <div class="col-6 col-md-4 col-lg-2">

                <div class="water-card text-center h-100">

                    <div class="fs-1">🚿</div>

                    <h6 class="mt-3">Bathing</h6>

                </div>

            </div>


            <div class="col-6 col-md-4 col-lg-2">

                <div class="water-card text-center h-100">

                    <div class="fs-1">👕</div>

                    <h6 class="mt-3">Laundry</h6>

                </div>

            </div>


            <div class="col-6 col-md-4 col-lg-2">

                <div class="water-card text-center h-100">

                    <div class="fs-1">🍽️</div>

                    <h6 class="mt-3">Dishwashing</h6>

                </div>

            </div>


            <div class="col-6 col-md-4 col-lg-2">

                <div class="water-card text-center h-100">

                    <div class="fs-1">🌱</div>

                    <h6 class="mt-3">Gardening</h6>

                </div>

            </div>


            <div class="col-6 col-md-4 col-lg-2">

                <div class="water-card text-center h-100">

                    <div class="fs-1">🚗</div>

                    <h6 class="mt-3">Vehicles</h6>

                </div>

            </div>


            <div class="col-6 col-md-4 col-lg-2">

                <div class="water-card text-center h-100">

                    <div class="fs-1">🧹</div>

                    <h6 class="mt-3">Cleaning</h6>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- How It Works -->
<section class="container py-5">

    <div class="text-center mb-5">

        <h2>How WaterWise Works</h2>

        <p class="text-secondary">
            Get a clearer picture of your household water usage in a few
            simple steps.
        </p>

    </div>


    <div class="row g-4">

        <div class="col-md-3">

            <div class="stat-card">

                <div class="fs-2">1️⃣</div>

                <h5>Enter Details</h5>

                <small>
                    Provide information about your household and daily
                    activities.
                </small>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="fs-2">2️⃣</div>

                <h5>Estimate</h5>

                <small>
                    WaterWise calculates an activity-based daily
                    consumption estimate.
                </small>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="fs-2">3️⃣</div>

                <h5>Predict</h5>

                <small>
                    The Random Forest model provides an independent
                    consumption prediction.
                </small>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="fs-2">4️⃣</div>

                <h5>Analyze</h5>

                <small>
                    View your results, history and consumption patterns.
                </small>

            </div>

        </div>

    </div>

</section>


<!-- Call To Action -->
<section class="hero-section py-5">

    <div class="container text-center">

        <h2>Ready to Understand Your Water Usage?</h2>

        <p class="text-secondary">
            Start exploring your household water consumption today.
        </p>

        <div class="mt-4">

            <?php if (isset($_SESSION["user_id"])): ?>

                <a href="prediction.php"
                   class="btn btn-primary btn-lg">
                    Check My Usage
                </a>

            <?php else: ?>

                <a href="register.php"
                   class="btn btn-primary btn-lg me-2">
                    Get Started
                </a>

                <a href="login.php"
                   class="btn btn-outline-primary btn-lg">
                    Login
                </a>

            <?php endif; ?>

        </div>

    </div>

</section>


<?php include "includes/footer.php"; ?>