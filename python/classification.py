import os
import pandas as pd


folder = os.path.dirname(__file__)


input_file = os.path.join(
    folder,
    "preprocessed_data.csv"
)


output_file = os.path.join(
    folder,
    "classified_data.csv"
)


df = pd.read_csv(
    input_file
)


def classify(value):

    if value < 300:

        return "Low"

    elif value < 600:

        return "Medium"

    else:

        return "High"


df["category"] = (
    df["consumption"]
    .apply(classify)
)


df.to_csv(
    output_file,
    index=False
)


print(
    "Classification completed."
)


print(
    df["category"].value_counts()
)