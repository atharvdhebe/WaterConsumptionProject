import pandas as pd
import numpy as np

np.random.seed(42)

records = 500

family_members = np.random.randint(1, 8, records)

bathrooms = np.random.randint(1, 4, records)

bathing_freq = family_members + np.random.randint(-1, 2, records)

bathing_freq = np.maximum(bathing_freq, 1)

laundry_freq = np.random.randint(1, 8, records)

dishwashing = np.maximum(
    family_members + np.random.randint(-2, 2, records),
    1
)

garden = np.random.choice(
    ["Yes", "No"],
    records
)

vehicle_wash = np.random.randint(
    0,
    5,
    records
)

consumption = (
    family_members * 70
    + bathrooms * 35
    + bathing_freq * 20
    + laundry_freq * 12
    + dishwashing * 8
    + (garden == "Yes") * 70
    + vehicle_wash * 25
    + np.random.normal(0, 30, records)
)

consumption = np.maximum(
    consumption.astype(int),
    100
)

df = pd.DataFrame({

    "family_members": family_members,

    "bathrooms": bathrooms,

    "bathing_freq": bathing_freq,

    "laundry_freq": laundry_freq,

    "dishwashing": dishwashing,

    "garden": garden,

    "vehicle_wash": vehicle_wash,

    "consumption": consumption

})

df.to_csv(
    "dataset.csv",
    index=False
)

print("Dataset created successfully.")
print(df.head())