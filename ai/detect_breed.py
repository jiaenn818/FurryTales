#!/usr/bin/env python
import os
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'  # suppress TensorFlow logs
import warnings
warnings.filterwarnings("ignore")  # ignore all warnings

import sys
import cv2
import numpy as np
import tensorflow as tf
from tensorflow.keras.applications.resnet50 import ResNet50, preprocess_input, decode_predictions

tf.get_logger().setLevel("ERROR")  # suppress TF logs

def map_to_pet_type(breed_label):
    label_lower = breed_label.lower()
    if any(k in label_lower for k in ["dog", "retriever", "terrier", "shepherd", "poodle", "husky"]):
        return "Dog"
    elif any(k in label_lower for k in ["cat", "kitten", "persian", "siamese"]):
        return "Cat"
    elif any(k in label_lower for k in ["rabbit", "hare"]):
        return "Rabbit"
    elif any(k in label_lower for k in ["hamster", "guinea pig", "mouse", "rat", "gerbil"]):
        return "Hamster"
    elif any(k in label_lower for k in ["bird", "parrot", "sparrow", "macaw"]):
        return "Bird"
    elif any(k in label_lower for k in ["turtle", "tortoise"]):
        return "Reptile"
    else:
        return "Other"

def main():
    if len(sys.argv) < 2:
        print("|Other", flush=True)
        sys.exit(0)

    image_path = sys.argv[1]
    img = cv2.imread(image_path)
    if img is None:
        print("|Other", flush=True)
        sys.exit(0)

    try:
        img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
        img = cv2.resize(img, (224, 224))
        x = np.expand_dims(img, axis=0)
        x = preprocess_input(x)

        model = ResNet50(weights="imagenet")
        preds = model.predict(x, verbose=0)

        breed = decode_predictions(preds, top=1)[0][0][1]
        pet_type = map_to_pet_type(breed)

        # Only print this last line
        print(f"{breed.replace('_',' ').title()}|{pet_type}", flush=True)

    except Exception as e:
        # Always return a valid PHP-safe line
        print("|Other", flush=True)

if __name__ == "__main__":
    main()
