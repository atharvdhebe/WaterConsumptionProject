<?php

require_once "includes/auth.php";
require_once "config/db.php";


$user_id =
    $_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| ACTUAL CONSUMPTION
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT
            date_recorded,
            family_members,
            activity_estimate,
            consumption,
            category

         FROM consumption

         WHERE user_id = ?

         ORDER BY
            date_recorded DESC,
            id DESC"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$consumption_records =
    $stmt->get_result();


/*
|--------------------------------------------------------------------------
| PREDICTIONS
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT
            prediction_date,
            activity_estimate,
            predicted_consumption,
            difference,
            category

         FROM predictions

         WHERE user_id = ?

         ORDER BY id DESC"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$prediction_records =
    $stmt->get_result();


$page_title =
    "History | WaterWise";


include "includes/header.php";

include "includes/navbar.php";

?>


<div class="container py-5">


<h2 class="mb-4">
📜 Water Consumption History
</h2>


<!-- =====================================================
     ACTUAL RECORDS
===================================================== -->


<div class="water-card mb-5">


<h4 class="mb-4">
Actual Consumption Records
</h4>


<div class="table-responsive">


<table class="table table-hover">


<thead>

<tr>

<th>Date</th>

<th>Family</th>

<th>Activity Estimate</th>

<th>Actual Consumption</th>

<th>Category</th>

</tr>

</thead>


<tbody>


<?php if (
    $consumption_records->num_rows > 0
): ?>


<?php while (
    $row =
    $consumption_records->fetch_assoc()
): ?>


<tr>


<td>

<?= htmlspecialchars(
    $row["date_recorded"]
) ?>

</td>


<td>

<?= $row["family_members"] ?>

</td>


<td>

<?= round(
    $row["activity_estimate"],
    2
) ?>

L/day

</td>


<td>

<strong>

<?= round(
    $row["consumption"],
    2
) ?>

L/day

</strong>

</td>


<td>

<span class="badge bg-primary">

<?= htmlspecialchars(
    $row["category"]
) ?>

</span>

</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td
colspan="5"
class="text-center"
>

No actual consumption
records yet.

</td>

</tr>


<?php endif; ?>


</tbody>

</table>


</div>


</div>


<!-- =====================================================
     ML PREDICTIONS
===================================================== -->


<div class="water-card">


<h4 class="mb-4">

Random Forest Predictions

</h4>


<div class="table-responsive">


<table class="table table-hover">


<thead>

<tr>

<th>Date</th>

<th>Activity Estimate</th>

<th>ML Prediction</th>

<th>Difference</th>

<th>Category</th>

</tr>

</thead>


<tbody>


<?php if (
    $prediction_records->num_rows > 0
): ?>


<?php while (
    $row =
    $prediction_records->fetch_assoc()
): ?>


<tr>


<td>

<?= htmlspecialchars(
    $row["prediction_date"]
) ?>

</td>


<td>

<?= round(
    $row["activity_estimate"],
    2
) ?>

L/day

</td>


<td>

<strong>

<?= round(
    $row["predicted_consumption"],
    2
) ?>

L/day

</strong>

</td>


<td>

<?= round(
    $row["difference"],
    2
) ?>

L/day

</td>


<td>

<span class="badge bg-primary">

<?= htmlspecialchars(
    $row["category"]
) ?>

</span>

</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td
colspan="5"
class="text-center"
>

No predictions yet.

</td>

</tr>


<?php endif; ?>


</tbody>

</table>


</div>


</div>


</div>


<?php

include "includes/footer.php";

?>