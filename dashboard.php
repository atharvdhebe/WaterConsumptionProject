<?php

require_once "includes/auth.php";

require_once "config/db.php";


$user_id =
    $_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| TOTAL ACTUAL RECORDS
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT COUNT(*) AS total

         FROM consumption

         WHERE user_id = ?"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$total_records =
    $stmt
    ->get_result()
    ->fetch_assoc()["total"];


/*
|--------------------------------------------------------------------------
| AVERAGE ACTUAL
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT AVG(consumption) AS average

         FROM consumption

         WHERE user_id = ?"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result =
    $stmt
    ->get_result()
    ->fetch_assoc();


$average_actual =
    $result["average"] !== null

    ? round(
        $result["average"],
        2
    )

    : 0;


/*
|--------------------------------------------------------------------------
| AVERAGE ML PREDICTION
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT
            AVG(predicted_consumption)
            AS average_prediction

         FROM predictions

         WHERE user_id = ?"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result =
    $stmt
    ->get_result()
    ->fetch_assoc();


$average_prediction =
    $result["average_prediction"] !== null

    ? round(
        $result["average_prediction"],
        2
    )

    : 0;


/*
|--------------------------------------------------------------------------
| LATEST PREDICTION
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT
            predicted_consumption,
            category,
            activity_estimate,
            difference

         FROM predictions

         WHERE user_id = ?

         ORDER BY id DESC

         LIMIT 1"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$latest_prediction =
    $stmt
    ->get_result()
    ->fetch_assoc();


/*
|--------------------------------------------------------------------------
| LATEST ACTUAL
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT
            consumption,
            activity_estimate,
            category

         FROM consumption

         WHERE user_id = ?

         ORDER BY id DESC

         LIMIT 1"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$latest_actual =
    $stmt
    ->get_result()
    ->fetch_assoc();


$page_title =
    "Dashboard | WaterWise";


include "includes/header.php";

include "includes/navbar.php";

?>


<div class="container py-5">


<div class="mb-4">

<h1>

Welcome,
<?= htmlspecialchars(
    $_SESSION["user_name"]
) ?>

👋

</h1>


<p class="text-secondary">

Household water consumption
overview.

</p>

</div>


<!-- =====================================================
     STATISTICS
===================================================== -->


<div class="row g-4 mb-5">


<div class="col-md-3">

<div class="stat-card">

<small>
Actual Records
</small>

<h2>
<?= $total_records ?>
</h2>

</div>

</div>


<div class="col-md-3">

<div class="stat-card">

<small>
Average Actual
</small>

<h2>

<?= $average_actual ?>

L

</h2>

</div>

</div>


<div class="col-md-3">

<div class="stat-card">

<small>
Average ML Prediction
</small>

<h2>

<?= $average_prediction ?>

L

</h2>

</div>

</div>


<div class="col-md-3">

<div class="stat-card">

<small>
Latest Prediction
</small>


<h2>

<?php if (
    $latest_prediction
): ?>

<?= round(
    $latest_prediction[
        "predicted_consumption"
    ],
    2
) ?>

L

<?php else: ?>

--

<?php endif; ?>

</h2>


<?php if (
    $latest_prediction
): ?>

<span class="badge bg-primary">

<?= htmlspecialchars(
    $latest_prediction[
        "category"
    ]
) ?>

</span>

<?php endif; ?>


</div>

</div>


</div>


<!-- =====================================================
     LATEST COMPARISON
===================================================== -->


<?php if (
    $latest_prediction &&
    $latest_actual
): ?>


<div class="water-card mb-5">


<h4>
Latest Consumption Comparison
</h4>


<div class="row text-center mt-4">


<div class="col-md-4">

<h6>
Activity Estimate
</h6>

<h3>

<?= round(
    $latest_actual[
        "activity_estimate"
    ],
    2
) ?>

L/day

</h3>

</div>


<div class="col-md-4">

<h6>
Actual Consumption
</h6>

<h3>

<?= round(
    $latest_actual[
        "consumption"
    ],
    2
) ?>

L/day

</h3>

</div>


<div class="col-md-4">

<h6>
ML Prediction
</h6>

<h3>

<?= round(
    $latest_prediction[
        "predicted_consumption"
    ],
    2
) ?>

L/day

</h3>

</div>


</div>


</div>


<?php endif; ?>


<!-- =====================================================
     ACTIONS
===================================================== -->


<div class="row g-4">


<div class="col-md-4">

<div class="water-card h-100">

<h4>
💧 Record Consumption
</h4>

<p>

Enter actual measured
household consumption.

</p>

<a
href="consumption.php"
class="btn btn-primary"
>

Add Record

</a>

</div>

</div>


<div class="col-md-4">

<div class="water-card h-100">

<h4>
🤖 Prediction
</h4>

<p>

Predict household consumption
using Random Forest.

</p>

<a
href="prediction.php"
class="btn btn-primary"
>

Predict

</a>

</div>

</div>


<div class="col-md-4">

<div class="water-card h-100">

<h4>
📊 Analytics
</h4>

<p>

Analyze household and
dataset water usage.

</p>

<a
href="analytics.php"
class="btn btn-primary"
>

Analytics

</a>

</div>

</div>


<div class="col-md-4">

<div class="water-card h-100">

<h4>
📜 History
</h4>

<p>

Compare previous activity
estimates and predictions.

</p>

<a
href="history.php"
class="btn btn-outline-primary"
>

History

</a>

</div>

</div>


</div>


</div>


<?php

include "includes/footer.php";

?>