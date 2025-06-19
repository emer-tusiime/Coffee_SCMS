"""
Predict using the trained coffee model.
Best practices: input checks, robust loading, batch & single prediction, CLI.
"""

import os
import sys
import pickle
import pandas as pd
import logging

MODEL_PATH = os.path.join(os.path.dirname(__file__), "model.pkl")

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)]
)

def load_model(path):
    if not os.path.isfile(path):
        logging.error(f"Model file not found: {path}")
        sys.exit(1)
    with open(path, "rb") as f:
        model = pickle.load(f)
    return model

def predict(model, input_features):
    return model.predict(input_features)

def main():
    if len(sys.argv) < 2:
        print("Usage: python predict.py '<comma-separated-features>' OR 'path/to/input.csv'")
        sys.exit(1)
    arg = sys.argv[1]
    model = load_model(MODEL_PATH)
    # If input is a file
    if os.path.isfile(arg):
        df = pd.read_csv(arg)
        logging.info(f"Loaded input data from {arg}")
        preds = predict(model, df)
        for i, p in enumerate(preds):
            print(f"Sample {i+1}: Predicted label = {p}")
    else:
        # Assume comma-separated features for a single sample
        features = [float(x.strip()) for x in arg.split(",")]
        df = pd.DataFrame([features])
        pred = predict(model, df)[0]
        print(f"Predicted label: {pred}")

if __name__ == "__main__":
    main()