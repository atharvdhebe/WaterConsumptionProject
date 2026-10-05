import pandas as pd

df = pd.read_csv("dataset.csv")

print("First five records:")
print(df.head())

print("\nDataset Information:")
print(df.info())

print("\nStatistical Summary:")
print(df.describe())

print("\nMissing Values:")
print(df.isnull().sum())