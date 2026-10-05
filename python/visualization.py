import os

import pandas as pd

import matplotlib.pyplot as plt

import seaborn as sns


folder = os.path.dirname(
    __file__
)


project_root = os.path.dirname(
    folder
)


results_folder = os.path.join(

    project_root,

    "results"

)


os.makedirs(

    results_folder,

    exist_ok=True

)


df = pd.read_csv(

    os.path.join(

        folder,

        "preprocessed_data.csv"

    )

)


# =====================================================
# CONSUMPTION DISTRIBUTION
# =====================================================

plt.figure(
    figsize=(8, 5)
)


sns.histplot(

    df["consumption"],

    kde=True

)


plt.xlabel(
    "Water Consumption (Litres/Day)"
)


plt.ylabel(
    "Number of Households"
)


plt.title(
    "Household Water Consumption Distribution"
)


plt.tight_layout()


plt.savefig(

    os.path.join(

        results_folder,

        "consumption_distribution.png"

    ),

    dpi=150

)


plt.close()


# =====================================================
# FAMILY SIZE
# =====================================================

plt.figure(
    figsize=(8, 5)
)


sns.scatterplot(

    data=df,

    x="family_members",

    y="consumption"

)


plt.xlabel(
    "Family Members"
)


plt.ylabel(
    "Water Consumption (L/day)"
)


plt.title(
    "Family Size vs Water Consumption"
)


plt.tight_layout()


plt.savefig(

    os.path.join(

        results_folder,

        "family_consumption.png"

    ),

    dpi=150

)


plt.close()


# =====================================================
# LAUNDRY
# =====================================================

plt.figure(
    figsize=(8, 5)
)


sns.scatterplot(

    data=df,

    x="laundry_kg_day",

    y="consumption"

)


plt.xlabel(
    "Laundry (kg/day)"
)


plt.ylabel(
    "Water Consumption (L/day)"
)


plt.title(
    "Laundry Quantity vs Water Consumption"
)


plt.tight_layout()


plt.savefig(

    os.path.join(

        results_folder,

        "laundry_consumption.png"

    ),

    dpi=150

)


plt.close()


print(
    "Visualizations created successfully."
)