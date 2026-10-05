import os
import sys
import joblib
import pandas as pd


# --------------------------------------------------
# PATH
# --------------------------------------------------

BASE_DIR = os.path.dirname(
    os.path.dirname(
        os.path.abspath(__file__)
    )
)

MODEL_PATH = os.path.join(
    BASE_DIR,
    "models",
    "water_model.pkl"
)


# --------------------------------------------------
# CHECK INPUT
# --------------------------------------------------

if len(sys.argv) != 20:

    print("ERROR: Expected 19 input values.")

    print("Received:", len(sys.argv) - 1)

    sys.exit(1)


# --------------------------------------------------
# LOAD MODEL
# --------------------------------------------------

if not os.path.exists(MODEL_PATH):

    print("ERROR: Model file not found.")

    print(MODEL_PATH)

    sys.exit(1)


model = joblib.load(MODEL_PATH)


# --------------------------------------------------
# CHECK MODEL
# --------------------------------------------------

if model.n_features_in_ != 19:

    print("ERROR: Wrong model detected.")

    print(
        "Model expects:",
        model.n_features_in_,
        "features"
    )

    print("Expected: 19")

    print("\nPlease run:")
    print("python train_model.py")

    sys.exit(1)


# --------------------------------------------------
# READ INPUT
# --------------------------------------------------

values = list(
    map(
        float,
        sys.argv[1:]
    )
)


# --------------------------------------------------
# FEATURE NAMES
# --------------------------------------------------

features = [
    "family_members",
    "bath_litres_per_person",
    "baths_per_person_day",
    "laundry_kg_day",
    "laundry_method",
    "utensils_per_day",
    "function_utensils_month",
    "has_garden",
    "garden_area_sqft",
    "garden_watering_days",
    "garden_method",
    "two_wheeler_count",
    "four_wheeler_count",
    "auto_count",
    "truck_count",
    "vehicle_washes_month",
    "vehicle_method",
    "cooking_drinking",
    "cleaning"
]


# --------------------------------------------------
# CREATE DATAFRAME
# --------------------------------------------------

input_data = pd.DataFrame(
    [values],
    columns=features
)


# --------------------------------------------------
# PREDICTION
# --------------------------------------------------

prediction = model.predict(
    input_data
)[0]


# --------------------------------------------------
# OUTPUT
# --------------------------------------------------

print(
    round(
        float(prediction),
        2
    )
)