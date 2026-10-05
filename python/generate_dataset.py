import os
import numpy as np
import pandas as pd


np.random.seed(42)

records = 1000


# =====================================================
# HOUSEHOLD
# =====================================================

family_members = np.random.randint(
    1,
    8,
    records
)


# =====================================================
# BATHING
# =====================================================

bath_litres_per_person = np.random.uniform(
    25,
    60,
    records
)


baths_per_person_day = np.random.uniform(
    0.8,
    1.5,
    records
)


# =====================================================
# LAUNDRY
# =====================================================

laundry_kg_day = np.random.uniform(
    1,
    10,
    records
)


laundry_method = np.random.randint(
    1,
    4,
    records
)


laundry_rate = np.where(
    laundry_method == 1,
    20,
    np.where(
        laundry_method == 2,
        15,
        10
    )
)


# =====================================================
# DISHWASHING
# =====================================================

utensils_per_day = np.random.randint(
    10,
    80,
    records
)


function_utensils_month = np.random.randint(
    0,
    250,
    records
)


# =====================================================
# GARDEN
# =====================================================

has_garden = np.random.randint(
    0,
    2,
    records
)


garden_area_sqft = np.where(

    has_garden == 1,

    np.random.uniform(
        100,
        1500,
        records
    ),

    0

)


garden_watering_days = np.where(

    has_garden == 1,

    np.random.uniform(
        1,
        7,
        records
    ),

    0

)


garden_method = np.random.randint(
    1,
    5,
    records
)


garden_factor = np.select(

    [
        garden_method == 1,
        garden_method == 2,
        garden_method == 3,
        garden_method == 4
    ],

    [
        1.00,
        1.20,
        1.10,
        0.70
    ],

    default=1.00

)


# =====================================================
# VEHICLES
# =====================================================

two_wheeler_count = np.random.randint(
    0,
    4,
    records
)


four_wheeler_count = np.random.randint(
    0,
    3,
    records
)


auto_count = np.random.randint(
    0,
    2,
    records
)


truck_count = np.random.randint(
    0,
    2,
    records
)


vehicle_washes_month = np.random.randint(
    0,
    9,
    records
)


vehicle_method = np.random.randint(
    1,
    4,
    records
)


# =====================================================
# OTHER
# =====================================================

cooking_drinking = np.random.uniform(
    5,
    30,
    records
)


cleaning = np.random.uniform(
    10,
    80,
    records
)


# =====================================================
# TARGET CALCULATION
# =====================================================

bath_water = (

    family_members
    *
    bath_litres_per_person
    *
    baths_per_person_day

)


laundry_water = (

    laundry_kg_day
    *
    laundry_rate

)


dish_water = (

    utensils_per_day
    *
    2

)


function_water = (

    function_utensils_month
    *
    2
    /
    30

)


garden_water = (

    garden_area_sqft
    *
    0.15
    *
    (
        garden_watering_days
        /
        7
    )
    *
    garden_factor

)


vehicle_water_per_wash = (

    two_wheeler_count * 25
    +
    four_wheeler_count * 100
    +
    auto_count * 80
    +
    truck_count * 200

)


vehicle_water = (

    vehicle_water_per_wash
    *
    vehicle_washes_month
    /
    30

)


other_water = (

    cooking_drinking
    +
    cleaning

)


consumption = (

    bath_water
    +
    laundry_water
    +
    dish_water
    +
    function_water
    +
    garden_water
    +
    vehicle_water
    +
    other_water

    +

    np.random.normal(
        0,
        20,
        records
    )

)


consumption = np.maximum(
    consumption,
    50
)


# =====================================================
# DATAFRAME
# =====================================================

df = pd.DataFrame({

    "family_members":
        family_members,

    "bath_litres_per_person":
        bath_litres_per_person,

    "baths_per_person_day":
        baths_per_person_day,

    "laundry_kg_day":
        laundry_kg_day,

    "laundry_method":
        laundry_method,

    "utensils_per_day":
        utensils_per_day,

    "function_utensils_month":
        function_utensils_month,

    "has_garden":
        has_garden,

    "garden_area_sqft":
        garden_area_sqft,

    "garden_watering_days":
        garden_watering_days,

    "garden_method":
        garden_method,

    "two_wheeler_count":
        two_wheeler_count,

    "four_wheeler_count":
        four_wheeler_count,

    "auto_count":
        auto_count,

    "truck_count":
        truck_count,

    "vehicle_washes_month":
        vehicle_washes_month,

    "vehicle_method":
        vehicle_method,

    "cooking_drinking":
        cooking_drinking,

    "cleaning":
        cleaning,

    "consumption":
        consumption

})


output = os.path.join(
    os.path.dirname(__file__),
    "dataset.csv"
)


df.to_csv(
    output,
    index=False
)


print(
    "New logical dataset created!"
)


print(
    "Records:",
    len(df)
)


print(
    df.head()
)