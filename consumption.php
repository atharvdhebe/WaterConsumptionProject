<?php

require_once "includes/auth.php";
require_once "config/db.php";


$message = "";

$error = "";


/*
|--------------------------------------------------------------------------
| CALCULATION FUNCTIONS
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
| FORM
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $user_id =
        $_SESSION["user_id"];


    $family_members =
        (int)($_POST["family_members"] ?? 0);


    /* Bath */

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


    /* Actual */

    $actual_consumption =
        (float)($_POST["consumption"] ?? 0);

    $date_recorded =
        $_POST["date_recorded"]
        ?? date("Y-m-d");


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

    elseif ($actual_consumption <= 0) {

        $error =
            "Enter actual consumption.";

    }

    else {


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY ESTIMATE
        |--------------------------------------------------------------------------
        */

        $bath_water =
            $family_members
            * $bath_litres_per_person
            * $baths_per_person_day;


        $laundry_water =
            $laundry_kg_day
            * laundryWaterPerKg(
                $laundry_method
            );


        $dish_water =
            ($utensils_per_day * 2)
            +
            (
                $function_utensils_month
                * 2
                / 30
            );


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


        $vehicle_water =
            (
                vehicleWaterPerWash(
                    $two_wheeler_count,
                    $four_wheeler_count,
                    $auto_count,
                    $truck_count
                )
                * $vehicle_washes_month
            )
            / 30;


        $other_water =
            $cooking_drinking
            + $cleaning;


        $activity_estimate =
            $bath_water
            + $laundry_water
            + $dish_water
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
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        if ($actual_consumption < 300) {

            $category = "Low";

        }

        elseif ($actual_consumption < 600) {

            $category = "Medium";

        }

        else {

            $category = "High";

        }


        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        $stmt =
            $conn->prepare(
                "INSERT INTO consumption
                (
                    user_id,
                    family_members,

                    bath_method,
                    bath_litres_per_person,
                    baths_per_person_day,

                    laundry_kg_day,
                    laundry_method,

                    utensils_per_day,
                    function_utensils_month,

                    has_garden,
                    garden_area_sqft,
                    garden_watering_days_week,
                    garden_method,

                    two_wheeler_count,
                    four_wheeler_count,
                    auto_count,
                    truck_count,
                    vehicle_washes_month,
                    vehicle_washing_method,

                    cooking_drinking_lpd,
                    cleaning_lpd,

                    activity_estimate,
                    consumption,
                    category,
                    date_recorded
                )

                VALUES
                (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?,
                    ?, ?, ?, ?
                )"
            );


        $stmt->bind_param(

            "iisdddsii"
            . "sdds"
            . "iiiiis"
            . "ddddss",

            $user_id,

            $family_members,

            $bath_method,

            $bath_litres_per_person,

            $baths_per_person_day,

            $laundry_kg_day,

            $laundry_method,

            $utensils_per_day,

            $function_utensils_month,

            $has_garden,

            $garden_area_sqft,

            $garden_watering_days,

            $garden_method,

            $two_wheeler_count,

            $four_wheeler_count,

            $auto_count,

            $truck_count,

            $vehicle_washes_month,

            $vehicle_washing_method,

            $cooking_drinking,

            $cleaning,

            $activity_estimate,

            $actual_consumption,

            $category,

            $date_recorded

        );


        if ($stmt->execute()) {

            $message =
                "Actual consumption record saved successfully.";

        }

        else {

            $error =
                "Unable to save record: "
                . $stmt->error;

        }

    }

}


$page_title =
    "Record Consumption | WaterWise";

include "includes/header.php";

include "includes/navbar.php";

?>


<div class="container py-5">

<div class="form-card mx-auto wide-card">


<h2>
💧 Record Actual Water Consumption
</h2>


<p class="text-secondary">

Use this page when you know your actual
household water consumption from a meter
or another reliable measurement.

</p>


<?php if ($message): ?>

<div class="alert alert-success">

<?= htmlspecialchars($message) ?>

</div>

<?php endif; ?>


<?php if ($error): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error) ?>

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
Water / Person / Bath (L)
</label>

<input
type="number"
step="0.1"
name="bath_litres_per_person"
class="form-control"
min="1"
required
>

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
👕 Laundry
</h4>


<div class="row g-3 mb-4">

<div class="col-md-6">

<label class="form-label">
Laundry (kg/day)
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
Laundry Method
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
Utensils / Day
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
Function Utensils / Month
</label>

<input
type="number"
name="function_utensils_month"
class="form-control"
min="0"
value="0"
>

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
Method
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
🏠 Other Usage
</h4>


<div class="row g-3 mb-4">


<div class="col-md-4">

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


<div class="col-md-4">

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


<div class="col-md-4">

<label class="form-label">
Actual Consumption (L/day)
</label>

<input
type="number"
step="0.1"
name="consumption"
class="form-control"
min="1"
required
>

</div>

</div>


<div class="mb-4">

<label class="form-label">
Date Recorded
</label>

<input
type="date"
name="date_recorded"
class="form-control"
value="<?= date("Y-m-d") ?>"
required
>

</div>


<button
class="btn btn-primary btn-lg"
>

💧 Save Actual Consumption

</button>


</form>

</div>

</div>


<?php

include "includes/footer.php";

?>