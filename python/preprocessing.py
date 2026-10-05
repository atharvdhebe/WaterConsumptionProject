import os
import pandas as pd


folder = os.path.dirname(
    __file__
)


input_file = os.path.join(
    folder,
    "dataset.csv"
)


output_file = os.path.join(
    folder,
    "preprocessed_data.csv"
)


df = pd.read_csv(
    input_file
)


df = df.dropna()


df.to_csv(
    output_file,
    index=False
)


print(
    "Preprocessing completed."
)


print(
    "Rows:",
    len(df)
)


print(
    "Columns:",
    list(df.columns)
)