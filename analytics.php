<?php

require_once "includes/auth.php";

require_once "config/db.php";


/*
|--------------------------------------------------------------------------
| DATASET
|--------------------------------------------------------------------------
*/

$dataset_file =
    __DIR__
    . "/python/dataset.csv";


$dataset_rows = [];


if (
    file_exists($dataset_file)
) {

    $file =
        fopen(
            $dataset_file,
            "r"
        );


    $header =
        fgetcsv($file);


    while (
        ($row = fgetcsv($file))
        !== false
    ) {

        $dataset_rows[] =
            array_combine(
                $header,
                $row
            );

    }


    fclose($file);

}


/*
|--------------------------------------------------------------------------
| DATASET STATISTICS
|--------------------------------------------------------------------------
*/

$consumptions = [];

$low = 0;

$medium = 0;

$high = 0;


foreach (
    $dataset_rows
    as $row
) {

    $value =
        (float)$row[
            "consumption"
        ];


    $consumptions[] =
        $value;


    if ($value < 300) {

        $low++;

    }

    elseif ($value < 600) {

        $medium++;

    }

    else {

        $high++;

    }

}


$dataset_average =
    count($consumptions) > 0

    ? round(
        array_sum($consumptions)
        /
        count($consumptions),
        2
    )

    : 0;


$dataset_min =
    count($consumptions) > 0

    ? round(
        min($consumptions),
        2
    )

    : 0;


$dataset_max =
    count($consumptions) > 0

    ? round(
        max($consumptions),
        2
    )

    : 0;


/*
|--------------------------------------------------------------------------
| FAMILY SIZE ANALYTICS
|--------------------------------------------------------------------------
*/

$family_sum = [];

$family_count = [];


foreach (
    $dataset_rows
    as $row
) {

    $family =
        (int)$row[
            "family_members"
        ];


    $value =
        (float)$row[
            "consumption"
        ];


    if (
        !isset(
            $family_sum[$family]
        )
    ) {

        $family_sum[$family] = 0;

        $family_count[$family] = 0;

    }


    $family_sum[$family] +=
        $value;


    $family_count[$family]++;

}


$family_labels = [];

$family_averages = [];


ksort($family_sum);


foreach (
    $family_sum
    as $family => $sum
) {

    $family_labels[] =
        $family . " People";


    $family_averages[] =
        round(
            $sum
            /
            $family_count[$family],
            2
        );

}


/*
|--------------------------------------------------------------------------
| LAUNDRY ANALYTICS
|--------------------------------------------------------------------------
*/

$laundry_values = [];

$laundry_consumption = [];


foreach (
    array_slice(
        $dataset_rows,
        0,
        150
    )
    as $row
) {

    $laundry_values[] =
        (float)$row[
            "laundry_kg_day"
        ];


    $laundry_consumption[] =
        (float)$row[
            "consumption"
        ];

}


/*
|--------------------------------------------------------------------------
| USER ACTUAL
|--------------------------------------------------------------------------
*/

$user_id =
    $_SESSION["user_id"];


$stmt =
    $conn->prepare(

        "SELECT
            date_recorded,
            consumption

         FROM consumption

         WHERE user_id = ?

         ORDER BY
            date_recorded ASC,
            id ASC"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result =
    $stmt->get_result();


$user_dates = [];

$user_actual = [];


while (
    $row =
    $result->fetch_assoc()
) {

    $user_dates[] =
        $row[
            "date_recorded"
        ];


    $user_actual[] =
        (float)$row[
            "consumption"
        ];

}


/*
|--------------------------------------------------------------------------
| USER PREDICTIONS
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(

        "SELECT
            DATE(prediction_date)
            AS prediction_day,

            predicted_consumption

         FROM predictions

         WHERE user_id = ?

         ORDER BY prediction_date ASC"

    );


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result =
    $stmt->get_result();


$prediction_dates = [];

$user_predictions = [];


while (
    $row =
    $result->fetch_assoc()
) {

    $prediction_dates[] =
        $row[
            "prediction_day"
        ];


    $user_predictions[] =
        (float)$row[
            "predicted_consumption"
        ];

}


$page_title =
    "Analytics | WaterWise";


include "includes/header.php";

include "includes/navbar.php";

?>


<div class="container py-5">


<h1>
📊 Water Consumption Analytics
</h1>


<p class="text-secondary">

WaterWise analyzes the generated
training dataset and your own
household records.

</p>


<!-- =====================================================
     DATASET CARDS
===================================================== -->


<div class="row g-4 mb-5">


<div class="col-md-4">

<div class="stat-card">

<small>
Dataset Average
</small>

<h2>
<?= $dataset_average ?> L
</h2>

<small>
per day
</small>

</div>

</div>


<div class="col-md-4">

<div class="stat-card">

<small>
Minimum
</small>

<h2>
<?= $dataset_min ?> L
</h2>

</div>

</div>


<div class="col-md-4">

<div class="stat-card">

<small>
Maximum
</small>

<h2>
<?= $dataset_max ?> L
</h2>

</div>

</div>


</div>


<!-- =====================================================
     CATEGORY
===================================================== -->


<div class="row g-4">


<div class="col-lg-5">

<div class="water-card">

<h4>
Consumption Categories
</h4>

<canvas
id="categoryChart"
></canvas>

</div>

</div>


<div class="col-lg-7">

<div class="water-card">

<h4>
Average Consumption by Family Size
</h4>

<canvas
id="familyChart"
></canvas>

</div>

</div>


</div>


<!-- =====================================================
     LAUNDRY
===================================================== -->


<div class="water-card mt-4">

<h4>
Laundry Quantity vs Consumption
</h4>

<canvas
id="laundryChart"
></canvas>

</div>


<!-- =====================================================
     PERSONAL
===================================================== -->


<div class="water-card mt-4">

<h4>
Your Actual Consumption
</h4>


<?php if (
    count($user_actual) > 0
): ?>

<canvas
id="actualChart"
></canvas>


<?php else: ?>

<div class="alert alert-info">

No actual consumption records yet.

<a
href="consumption.php"
class="alert-link"
>

Add your first record.

</a>

</div>

<?php endif; ?>


</div>


<!-- =====================================================
     PERSONAL PREDICTIONS
===================================================== -->


<div class="water-card mt-4">

<h4>
Your Random Forest Predictions
</h4>


<?php if (
    count($user_predictions) > 0
): ?>

<canvas
id="predictionChart"
></canvas>


<?php else: ?>

<div class="alert alert-info">

No ML predictions yet.

<a
href="prediction.php"
class="alert-link"
>

Make your first prediction.

</a>

</div>

<?php endif; ?>


</div>


</div>


<script
src="https://cdn.jsdelivr.net/npm/chart.js"
></script>


<script>


/*
|--------------------------------------------------------------------------
| CATEGORY
|--------------------------------------------------------------------------
*/

new Chart(

document.getElementById(
    "categoryChart"
),

{

type: "doughnut",

data: {

labels: [
"Low",
"Medium",
"High"
],

datasets: [{

data: [

<?= $low ?>,

<?= $medium ?>,

<?= $high ?>

]

}]

},

options: {

responsive: true

}

}

);


/*
|--------------------------------------------------------------------------
| FAMILY
|--------------------------------------------------------------------------
*/

new Chart(

document.getElementById(
    "familyChart"
),

{

type: "bar",

data: {

labels:

<?= json_encode(
    $family_labels
) ?>,

datasets: [{

label:
"Average L/day",

data:

<?= json_encode(
    $family_averages
) ?>

}]

},

options: {

responsive: true,

scales: {

y: {

beginAtZero: true

}

}

}

}

);


/*
|--------------------------------------------------------------------------
| LAUNDRY
|--------------------------------------------------------------------------
*/

new Chart(

document.getElementById(
    "laundryChart"
),

{

type: "scatter",

data: {

datasets: [{

label:
"Laundry kg/day vs Consumption",

data:

[

<?php

for (
    $i = 0;
    $i < count($laundry_values);
    $i++
):

?>

{

x:
<?= $laundry_values[$i] ?>,

y:
<?= $laundry_consumption[$i] ?>

},

<?php endfor; ?>

]

}]

},

options: {

responsive: true,

scales: {

x: {

title: {

display: true,

text:
"Laundry (kg/day)"

}

},

y: {

title: {

display: true,

text:
"Consumption (L/day)"

}

}

}

}

}

);


/*
|--------------------------------------------------------------------------
| ACTUAL
|--------------------------------------------------------------------------
*/

<?php if (
    count($user_actual) > 0
): ?>

new Chart(

document.getElementById(
    "actualChart"
),

{

type: "line",

data: {

labels:

<?= json_encode(
    $user_dates
) ?>,

datasets: [{

label:
"Actual Consumption (L/day)",

data:

<?= json_encode(
    $user_actual
) ?>,

tension: 0.3

}]

},

options: {

responsive: true

}

}

);

<?php endif; ?>


/*
|--------------------------------------------------------------------------
| PREDICTIONS
|--------------------------------------------------------------------------
*/

<?php if (
    count($user_predictions) > 0
): ?>

new Chart(

document.getElementById(
    "predictionChart"
),

{

type: "line",

data: {

labels:

<?= json_encode(
    $prediction_dates
) ?>,

datasets: [{

label:
"Random Forest Prediction (L/day)",

data:

<?= json_encode(
    $user_predictions
) ?>,

tension: 0.3

}]

},

options: {

responsive: true

}

}

);

<?php endif; ?>


</script>


<?php

include "includes/footer.php";

?>