import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
import pickle
import os

# Load your coffee dataset
DATASET_PATH = os.path.join(os.path.dirname(__file__), "datasets", "coffee.csv")
df = pd.read_csv(DATASET_PATH)

# Minimal preprocessing (remove non-numeric/categorical columns as needed)
X = df[["quantity", "unit_price", "total_price"]]  # Example: use numeric features only
y = df["label"]

# Encode target if needed
from sklearn.preprocessing import LabelEncoder
label_encoder = LabelEncoder()
y_encoded = label_encoder.fit_transform(y)

# Train/test split
X_train, X_test, y_train, y_test = train_test_split(X, y_encoded, test_size=0.2, random_state=42)

# Train the model
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Save the model
MODEL_PATH = os.path.join(os.path.dirname(__file__), "model.pkl")
with open(MODEL_PATH, "wb") as f:
    pickle.dump(model, f)

# Save the label encoder for future use
with open(os.path.join(os.path.dirname(__file__), "label_encoder.pkl"), "wb") as f:
    pickle.dump(label_encoder, f)

print("model.pkl and label_encoder.pkl have been generated successfully.")