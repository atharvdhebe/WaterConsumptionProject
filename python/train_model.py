import os
import pandas as pd
import joblib

from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, r2_score


# --------------------------------------------------
# PATHS
# --------------------------------------------------

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

DATASET_PATH = os.path.join(
    BASE_DIR,
    "python",
    "preprocessed_data.csv"
)

MODEL_DIR = os.path.join(
    BASE_DIR,
    "models"
)

MODEL_PATH = os.path.join(
    MODEL_DIR,
    "water_model.pkl"
)


# --------------------------------------------------
# CREATE MODEL FOLDER
# --------------------------------------------------

os.makedirs(MODEL_DIR, exist_ok=True)


# --------------------------------------------------
# DELETE OLD MODEL
# --------------------------------------------------

if os.path.exists(MODEL_PATH):
    os.remove(MODEL_PATH)
    print("Old model deleted.")


# --------------------------------------------------
# LOAD DATA
# --------------------------------------------------

df = pd.read_csv(DATASET_PATH)

print("Dataset loaded.")
print("Rows:", len(df))
print("Columns:", list(df.columns))


# --------------------------------------------------
# 19 FEATURES
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
# CHECK FEATURES
# --------------------------------------------------

missing_features = []

for feature in features:
    if feature not in df.columns:
        missing_features.append(feature)

if len(missing_features) > 0:

    print("\nERROR: Missing features:")
    print(missing_features)

    print("\nAvailable columns:")
    print(list(df.columns))

    exit()


# --------------------------------------------------
# INPUT AND OUTPUT
# --------------------------------------------------

X = df[features]

y = df["consumption"]


print("\nNumber of input features:", X.shape[1])

print("Features used by model:")
for i, feature in enumerate(features, start=1):
    print(i, "-", feature)


# --------------------------------------------------
# TRAIN / TEST SPLIT
# --------------------------------------------------

X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.20,
    random_state=42
)


# --------------------------------------------------
# RANDOM FOREST MODEL
# --------------------------------------------------

model = RandomForestRegressor(
    n_estimators=150,
    random_state=42,
    min_samples_leaf=2
)


# --------------------------------------------------
# TRAIN
# --------------------------------------------------

print("\nTraining Random Forest...")

model.fit(X_train, y_train)


# --------------------------------------------------
# TEST
# --------------------------------------------------

predictions = model.predict(X_test)

mae = mean_absolute_error(
    y_test,
    predictions
)

r2 = r2_score(
    y_test,
    predictions
)


# --------------------------------------------------
# SAVE MODEL
# --------------------------------------------------

joblib.dump(
    model,
    MODEL_PATH
)


# --------------------------------------------------
# VERIFY SAVED MODEL
# --------------------------------------------------

loaded_model = joblib.load(MODEL_PATH)

print("\n===================================")
print("MODEL TRAINING SUCCESSFUL")
print("===================================")

print("Features:", loaded_model.n_features_in_)
print("MAE:", round(mae, 2))
print("R2 Score:", round(r2, 4))

print("\nModel saved at:")
print(MODEL_PATH)

print("\nExpected features: 19")
print("Actual model features:", loaded_model.n_features_in_)

if loaded_model.n_features_in_ == 19:
    print("\nSUCCESS: 19-feature model created.")
else:
    print("\nERROR: Model still has wrong number of features.")