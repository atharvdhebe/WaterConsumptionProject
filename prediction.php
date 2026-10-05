<?php

require_once "includes/auth.php";
require_once "config/db.php";
require_once "config/config.php";


$prediction = null;

$activity_estimate = null;

$category = null;

$difference = null;

$breakdown = [];

$error = "";


/*
|--------------------------------------------------------------------------
| CATEGORY
|--------------------------------------------------------------------------
*/

function classifyConsumption($value)
{
    if ($value < 300) {
        return "Low";
    }

    if ($value < 600) {
        return "Medium";
    }

    return "High";
}


/*
|--------------------------------------------------------------------------
| LAUNDRY WATER RATE
|--------------------------------------------------------------------------
*/

function laundryWaterPerKg($method)
{
    switch ($method) {

        case "Hand Wash":
            return 20;

        case "Top Load":
            return 15;

        case "Front Load":
            return 10;

        default:
            return 15;
    }
}


/*
|--------------------------------------------------------------------------
| GARDEN FACTOR
|--------------------------------------------------------------------------
*/

function gardenFactor($method)
{
    switch ($method) {

        case "Bucket":
            return 1.00;

        case "Hose":
            return 1.20;

        case "Sprinkler":
            return 1.10;

        case "Drip":
            return 0.70;

        default:
            return 1.00;
    }
}


/*
|--------------------------------------------------------------------------
| VEHICLE WATER
|--------------------------------------------------------------------------
*/

function vehicleWaterPerWash(
    $two,
    $four,
    $auto,
    $truck
) {

    return
        ($two * 25) +
        ($four * 100) +
        ($auto * 80) +
        ($truck * 200);
}


/*
|--------------------------------------------------------------------------
| FORM SUBMISSION
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /*
    |--------------------------------------------------------------------------
    | GET INPUTS
    |--------------------------------------------------------------------------
    */

    $family_members =
        (int)($_POST["family_members"] ?? 0);


    /* Bathing */

    $bath_method =
        $_POST["bath_method"] ?? "Bucket";

    $bath_litres_per_person =
        (float)($_POST["bath_litres_per_person"] ?? 0);

    $baths_per_person_day =
        (float)($_POST["baths_per_person_day"] ?? 0);


    /* Laundry */

    $laundry_kg_day =
        (float)($_POST["laundry_kg_day"] ?? 0);

    $laundry_method =
        $_POST["laundry_method"] ?? "Top Load";


    /* Dishwashing */

    $utensils_per_day =
        (int)($_POST["utensils_per_day"] ?? 0);

    $function_utensils_month =
        (int)($_POST["function_utensils_month"] ?? 0);


    /* Garden */

    $has_garden =
        $_POST["has_garden"] ?? "No";

    $garden_area_sqft =
        (float)($_POST["garden_area_sqft"] ?? 0);

    $garden_watering_days =
        (float)($_POST["garden_watering_days_week"] ?? 0);

    $garden_method =
        $_POST["garden_method"] ?? "Hose";


    /* Vehicles */

    $two_wheeler_count =
        (int)($_POST["two_wheeler_count"] ?? 0);

    $four_wheeler_count =
        (int)($_POST["four_wheeler_count"] ?? 0);

    $auto_count =
        (int)($_POST["auto_count"] ?? 0);

    $truck_count =
        (int)($_POST["truck_count"] ?? 0);

    $vehicle_washes_month =
        (int)($_POST["vehicle_washes_month"] ?? 0);

    $vehicle_washing_method =
        $_POST["vehicle_washing_method"] ?? "Bucket";


    /* Other */

    $cooking_drinking =
        (float)($_POST["cooking_drinking_lpd"] ?? 0);

    $cleaning =
        (float)($_POST["cleaning_lpd"] ?? 0);


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($family_members < 1) {

        $error =
            "Family members must be at least 1.";

    }

    elseif ($bath_litres_per_person <= 0) {

        $error =
            "Enter valid bathing water.";

    }

    elseif ($baths_per_person_day <= 0) {

        $error =
            "Enter valid bathing frequency.";

    }

    elseif ($laundry_kg_day < 0) {

        $error =
            "Laundry quantity cannot be negative.";

    }

    elseif ($utensils_per_day < 0) {

        $error =
            "Utensil quantity cannot be negative.";

    }

    else {


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY CALCULATIONS
        |--------------------------------------------------------------------------
        */


        /*
        Bathing
        */

        $bath_water =
            $family_members
            * $bath_litres_per_person
            * $baths_per_person_day;


        /*
        Laundry
        */

        $laundry_rate =
            laundryWaterPerKg(
                $laundry_method
            );

        $laundry_water =
            $laundry_kg_day
            * $laundry_rate;


        /*
        Normal dishwashing
        */

        $dish_water =
            $utensils_per_day * 2;


        /*
        Function / party utensils
        converted to daily average
        */

        $function_water =
            (
                $function_utensils_month * 2
            ) / 30;


        $total_dish_water =
            $dish_water
            + $function_water;


        /*
        Garden
        */

        $garden_water = 0;

        if ($has_garden === "Yes") {

            $garden_water =
                $garden_area_sqft
                * 0.15
                * (
                    $garden_watering_days
                    / 7
                )
                * gardenFactor(
                    $garden_method
                );
        }


        /*
        Vehicles
        */

        $vehicle_base =
            vehicleWaterPerWash(
                $two_wheeler_count,
                $four_wheeler_count,
                $auto_count,
                $truck_count
            );


        $vehicle_water =
            (
                $vehicle_base
                * $vehicle_washes_month
            ) / 30;


        /*
        Other household usage
        */

        $other_water =
            $cooking_drinking
            + $cleaning;


        /*
        Total activity estimate
        */

        $activity_estimate =
            $bath_water
            + $laundry_water
            + $total_dish_water
            + $garden_water
            + $vehicle_water
            + $other_water;


        $activity_estimate =
            round(
                $activity_estimate,
                2
            );


        /*
        |--------------------------------------------------------------------------
        | BREAKDOWN
        |--------------------------------------------------------------------------
        */

        $breakdown = [

            "Bathing" =>
                round(
                    $bath_water,
                    2
                ),

            "Laundry" =>
                round(
                    $laundry_water,
                    2
                ),

            "Dishwashing" =>
                round(
                    $total_dish_water,
                    2
                ),

            "Garden" =>
                round(
                    $garden_water,
                    2
                ),

            "Vehicles" =>
                round(
                    $vehicle_water,
                    2
                ),

            "Cooking & Cleaning" =>
                round(
                    $other_water,
                    2
                )

        ];


        /*
        |--------------------------------------------------------------------------
        | ML MODEL
        |--------------------------------------------------------------------------
        */

        $python_file =
            __DIR__
            . DIRECTORY_SEPARATOR
            . "python"
            . DIRECTORY_SEPARATOR
            . "predict.py";


        $model_file =
            __DIR__
            . DIRECTORY_SEPARATOR
            . "models"
            . DIRECTORY_SEPARATOR
            . "water_model.pkl";


        if (!file_exists($model_file)) {

            $error =
                "Machine Learning model not found. "
                . "Run train_model.py first.";

        }

        else {


            /*
            Convert categorical values to numbers.
            */

            $garden_binary =
                $has_garden === "Yes"
                ? 1
                : 0;


            switch ($laundry_method) {

                case "Hand Wash":
                    $laundry_method_code = 1;
                    break;

                case "Top Load":
                    $laundry_method_code = 2;
                    break;

                case "Front Load":
                    $laundry_method_code = 3;
                    break;

                default:
                    $laundry_method_code = 2;
            }


            switch ($garden_method) {

                case "Bucket":
                    $garden_method_code = 1;
                    break;

                case "Hose":
                    $garden_method_code = 2;
                    break;

                case "Sprinkler":
                    $garden_method_code = 3;
                    break;

                case "Drip":
                    $garden_method_code = 4;
                    break;

                default:
                    $garden_method_code = 2;
            }


            switch ($vehicle_washing_method) {

                case "Bucket":
                    $vehicle_method_code = 1;
                    break;

                case "Hose":
                    $vehicle_method_code = 2;
                    break;

                case "Pressure Washer":
                    $vehicle_method_code = 3;
                    break;

                default:
                    $vehicle_method_code = 1;
            }


            /*
            |--------------------------------------------------------------------------
            | PYTHON COMMAND
            |--------------------------------------------------------------------------
            |
            | EXACTLY 19 FEATURES
            |--------------------------------------------------------------------------
            */

            $command =
                PYTHON_COMMAND
                . " "
                . escapeshellarg(
                    $python_file
                )
                . " "
                . escapeshellarg($family_members)
                . " "
                . escapeshellarg($bath_litres_per_person)
                . " "
                . escapeshellarg($baths_per_person_day)
                . " "
                . escapeshellarg($laundry_kg_day)
                . " "
                . escapeshellarg($laundry_method_code)
                . " "
                . escapeshellarg($utensils_per_day)
                . " "
                . escapeshellarg($function_utensils_month)
                . " "
                . escapeshellarg($garden_binary)
                . " "
                . escapeshellarg($garden_area_sqft)
                . " "
                . escapeshellarg($garden_watering_days)
                . " "
                . escapeshellarg($garden_method_code)
                . " "
                . escapeshellarg($two_wheeler_count)
                . " "
                . escapeshellarg($four_wheeler_count)
                . " "
                . escapeshellarg($auto_count)
                . " "
                . escapeshellarg($truck_count)
                . " "
                . escapeshellarg($vehicle_washes_month)
                . " "
                . escapeshellarg($vehicle_method_code)
                . " "
                . escapeshellarg($cooking_drinking)
                . " "
                . escapeshellarg($cleaning);


            $output =
                shell_exec(
                    $command
                );


            if (
                $output !== null
                &&
                is_numeric(
                    trim($output)
                )
            ) {

                $prediction =
                    round(
                        (float)trim($output),
                        2
                    );


                $category =
                    classifyConsumption(
                        $prediction
                    );


                $difference =
                    round(
                        abs(
                            $prediction
                            - $activity_estimate
                        ),
                        2
                    );


                /*
                Save prediction
                */

                $stmt =
                    $conn->prepare(
                        "INSERT INTO predictions
                        (
                            user_id,
                            activity_estimate,
                            predicted_consumption,
                            difference,
                            category
                        )
                        VALUES (?, ?, ?, ?, ?)"
                    );


                $stmt->bind_param(
                    "iddds",
                    $_SESSION["user_id"],
                    $activity_estimate,
                    $prediction,
                    $difference,
                    $category
                );


                $stmt->execute();

            }

            else {

                $error =
                    "ML prediction failed. "
                    . "Check Python setup.";

            }
        }
    }
}


$page_title =
    "Prediction | WaterWise";

include "includes/header.php";

include "includes/navbar.php";

?>


<div class="container py-5">

<div class="form-card mx-auto wide-card">


<h2>
💧 Smart Water Consumption Prediction
</h2>


<p class="text-secondary">

Enter measurable household activities.
WaterWise first calculates an activity-based
estimate and then uses Random Forest Machine
Learning to predict daily consumption.

</p>


<?php if ($error): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>


<?php if ($prediction !== null): ?>

<div class="prediction-result mb-4">

<h4>
Estimated Daily Consumption
</h4>


<div class="display-5 fw-bold">

<?= $prediction ?>

L/day

</div>


<p>

Per Person:

<strong>

<?= round(
    $prediction /
    max(
        1,
        $family_members
    ),
    2
) ?>

L/person/day

</strong>

</p>


<span class="badge bg-primary">

<?= htmlspecialchars($category) ?>

Consumption

</span>

</div>


<div class="water-card mb-4">

<h4>
📊 Water Usage Breakdown
</h4>


<table class="table">

<thead>

<tr>

<th>
Activity
</th>

<th>
Estimated L/day
</th>

</tr>

</thead>


<tbody>

<?php foreach (
    $breakdown
    as $name => $value
): ?>

<tr>

<td>

<?= htmlspecialchars($name) ?>

</td>

<td>

<strong>

<?= $value ?>

L/day

</strong>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>


<hr>


<p>

Activity-based estimate:

<strong>

<?= $activity_estimate ?>

L/day

</strong>

</p>


<p>

Random Forest prediction:

<strong>

<?= $prediction ?>

L/day

</strong>

</p>


<p>

Difference:

<strong>

<?= $difference ?>

L/day

</strong>

</p>

</div>

<?php endif; ?>


<form method="POST">


<h4 class="section-title">
👨‍👩‍👧 Household
</h4>


<div class="row g-3 mb-4">

<div class="col-md-6">

<label class="form-label">

Family Members

</label>

<input
type="number"
name="family_members"
class="form-control"
min="1"
max="20"
required
>

</div>

</div>


<h4 class="section-title">
🚿 Bathing
</h4>


<div class="row g-3 mb-4">


<div class="col-md-4">

<label class="form-label">

Bathing Method

</label>

<select
name="bath_method"
class="form-select"
>

<option>
Bucket
</option>

<option>
Shower
</option>

<option>
Mixed
</option>

</select>

</div>


<div class="col-md-4">

<label class="form-label">

Water Used / Person / Bath (L)

</label>

<input
type="number"
step="0.1"
name="bath_litres_per_person"
class="form-control"
min="1"
required
>

<small class="text-secondary">

Enter your approximate actual usage.

</small>

</div>


<div class="col-md-4">

<label class="form-label">

Baths / Person / Day

</label>

<input
type="number"
step="0.1"
name="baths_per_person_day"
class="form-control"
min="0.1"
required
>

</div>


</div>


<h4 class="section-title">
👕 Clothes Washing
</h4>


<div class="row g-3 mb-4">


<div class="col-md-6">

<label class="form-label">

Clothes Washed (kg/day)

</label>

<input
type="number"
step="0.1"
name="laundry_kg_day"
class="form-control"
min="0"
required
>

</div>


<div class="col-md-6">

<label class="form-label">

Washing Method

</label>

<select
name="laundry_method"
class="form-select"
>

<option>
Hand Wash
</option>

<option selected>
Top Load
</option>

<option>
Front Load
</option>

</select>

</div>

</div>


<h4 class="section-title">
🍽 Dishwashing
</h4>


<div class="row g-3 mb-4">


<div class="col-md-6">

<label class="form-label">

Utensils Washed / Day

</label>

<input
type="number"
name="utensils_per_day"
class="form-control"
min="0"
required
>

</div>


<div class="col-md-6">

<label class="form-label">

Extra Function Utensils / Month

</label>

<input
type="number"
name="function_utensils_month"
class="form-control"
min="0"
value="0"
>

<small class="text-secondary">

For parties, weddings,
family functions, etc.

</small>

</div>

</div>


<h4 class="section-title">
🌱 Garden
</h4>


<div class="row g-3 mb-4">


<div class="col-md-3">

<label class="form-label">
Garden?
</label>

<select
name="has_garden"
class="form-select"
>

<option>
No
</option>

<option>
Yes
</option>

</select>

</div>


<div class="col-md-3">

<label class="form-label">
Area (sq.ft)
</label>

<input
type="number"
step="0.1"
name="garden_area_sqft"
class="form-control"
min="0"
value="0"
>

</div>


<div class="col-md-3">

<label class="form-label">

Watering Days / Week

</label>

<input
type="number"
step="0.1"
name="garden_watering_days_week"
class="form-control"
min="0"
max="7"
value="0"
>

</div>


<div class="col-md-3">

<label class="form-label">

Watering Method

</label>

<select
name="garden_method"
class="form-select"
>

<option>
Bucket
</option>

<option>
Hose
</option>

<option>
Sprinkler
</option>

<option>
Drip
</option>

</select>

</div>

</div>


<h4 class="section-title">
🚗 Vehicles
</h4>


<div class="row g-3 mb-4">


<div class="col-md-3">

<label class="form-label">
Two-Wheelers
</label>

<input
type="number"
name="two_wheeler_count"
class="form-control"
min="0"
value="0"
>

</div>


<div class="col-md-3">

<label class="form-label">
Four-Wheelers
</label>

<input
type="number"
name="four_wheeler_count"
class="form-control"
min="0"
value="0"
>

</div>


<div class="col-md-3">

<label class="form-label">
Auto-Rickshaws
</label>

<input
type="number"
name="auto_count"
class="form-control"
min="0"
value="0"
>

</div>


<div class="col-md-3">

<label class="form-label">
Trucks
</label>

<input
type="number"
name="truck_count"
class="form-control"
min="0"
value="0"
>

</div>


<div class="col-md-6">

<label class="form-label">

Vehicle Washes / Month

</label>

<input
type="number"
name="vehicle_washes_month"
class="form-control"
min="0"
value="0"
>

</div>


<div class="col-md-6">

<label class="form-label">

Washing Method

</label>

<select
name="vehicle_washing_method"
class="form-select"
>

<option>
Bucket
</option>

<option>
Hose
</option>

<option>
Pressure Washer
</option>

</select>

</div>

</div>


<h4 class="section-title">
🏠 Other Household Usage
</h4>


<div class="row g-3 mb-4">


<div class="col-md-6">

<label class="form-label">

Cooking & Drinking (L/day)

</label>

<input
type="number"
step="0.1"
name="cooking_drinking_lpd"
class="form-control"
min="0"
value="0"
required
>

</div>


<div class="col-md-6">

<label class="form-label">

Cleaning (L/day)

</label>

<input
type="number"
step="0.1"
name="cleaning_lpd"
class="form-control"
min="0"
value="0"
required
>

</div>

</div>


<button
class="btn btn-primary btn-lg"
>

💧 Predict Consumption

</button>


</form>

</div>

</div>


<?php

include "includes/footer.php";

?>