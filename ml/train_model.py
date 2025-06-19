"""
Train a machine learning model for coffee dataset classification/regression.
Best practices: reproducibility, logging, validation, model persistence.
"""

import os
import sys
import pickle
import logging
import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.metrics import classification_report, accuracy_score

# Logging setup
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)]
)

DATASET_PATH = os.path.join(os.path.dirname(__file__), "datasets", "coffee.csv")
MODEL_PATH = os.path.join(os.path.dirname(__file__), "model.pkl")
RANDOM_SEED = 42

def load_data(path):
    logging.info(f"Loading data from {path}")
    if not os.path.exists(path):
        logging.error(f"Dataset not found at {path}")
        sys.exit(1)
    df = pd.read_csv(path)
    return df

def preprocess(df):
    logging.info("Preprocessing data")
    df = df.dropna()
    # Example: assume 'label' is the target, rest are features
    X = df.drop("label", axis=1)
    y = df["label"]
    return X, y

def train(X, y):
    logging.info("Training model")
    model = RandomForestClassifier(
        n_estimators=100,
        random_state=RANDOM_SEED,
        class_weight="balanced"
    )
    scores = cross_val_score(model, X, y, cv=5)
    logging.info(f"Cross-validation accuracy: {scores.mean():.4f} ± {scores.std():.4f}")
    model.fit(X, y)
    return model

def evaluate(model, X_test, y_test):
    logging.info("Evaluating model")
    y_pred = model.predict(X_test)
    acc = accuracy_score(y_test, y_pred)
    report = classification_report(y_test, y_pred)
    logging.info(f"Test Accuracy: {acc:.4f}")
    logging.info("Classification Report:\n" + report)

def save_model(model, path):
    logging.info(f"Saving model to {path}")
    with open(path, "wb") as f:
        pickle.dump(model, f)

def main():
    df = load_data(DATASET_PATH)
    X, y = preprocess(df)
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=RANDOM_SEED)
    model = train(X_train, y_train)
    evaluate(model, X_test, y_test)
    save_model(model, MODEL_PATH)
    logging.info("Training complete.")

if __name__ == "__main__":
    main()