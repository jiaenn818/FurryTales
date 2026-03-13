import sys
import json
import mysql.connector
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

def get_recommendations(customer_id, limit=5):
    # ================= DB CONNECTION =================
    db = mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="",
        database="furrytales1"
    )
    cursor = db.cursor(dictionary=True)

    # ================= FETCH DATA =================
    cursor.execute("""
            SELECT p.PetID, p.PetName, p.Type, p.Breed
            FROM pet p
            LEFT JOIN purchase_items pi ON p.PetID = pi.ItemID
            WHERE pi.ItemID IS NULL
    """)

    pets = pd.DataFrame(cursor.fetchall())

    if pets.empty:
        return []

    pets['features'] = pets['PetName'] + " " + pets['Type'] + " " + pets['Breed']

    # ---------- SEARCH HISTORY ----------
    cursor.execute(
        "SELECT keyword FROM search_histories WHERE CustomerID=%s",
        (customer_id,)
    )
    search_history = pd.DataFrame(cursor.fetchall())

    # ---------- BROWSING HISTORY ----------
    cursor.execute(
        "SELECT PetID FROM browsing_histories WHERE CustomerID=%s",
        (customer_id,)
    )
    browsing = pd.DataFrame(cursor.fetchall())
    browsing['interaction'] = 1 if not browsing.empty else None

    # ---------- CART HISTORY ----------
    cursor.execute("""
        SELECT carts.CustomerID, cart_items.PetID
        FROM cart_items
        JOIN carts ON cart_items.CartID = carts.CartID
        WHERE carts.CustomerID=%s
    """, (customer_id,))
    cart = pd.DataFrame(cursor.fetchall())
    cart['interaction'] = 3 if not cart.empty else None

    # ================= CONTENT-BASED =================
    user_keywords = []

    if not search_history.empty:
        user_keywords += search_history['keyword'].tolist()

    if not browsing.empty:
        browsed_pets = pets[pets['PetID'].isin(browsing['PetID'])]
        user_keywords += browsed_pets['features'].tolist()

    if not cart.empty:
        cart_pets = pets[pets['PetID'].isin(cart['PetID'])]
        user_keywords += cart_pets['features'].tolist()

    user_profile = " ".join(user_keywords)

    tfidf = TfidfVectorizer()
    pet_vectors = tfidf.fit_transform(pets['features'])

    if user_profile.strip():
        user_vector = tfidf.transform([user_profile])
        content_scores = cosine_similarity(user_vector, pet_vectors).flatten()
    else:
        content_scores = np.zeros(len(pets))

    pets['content_score'] = content_scores

    # ================= COLLABORATIVE =================
    interactions = pd.concat([
        browsing[['PetID', 'interaction']] if not browsing.empty else pd.DataFrame(),
        cart[['PetID', 'interaction']] if not cart.empty else pd.DataFrame()
    ])

    if not interactions.empty:
        interactions = interactions.groupby('PetID')['interaction'].sum()

        pets['collab_score'] = pets['PetID'].map(interactions).fillna(0)
    else:
        pets['collab_score'] = 0

    # ================= NORMALIZATION =================
    if pets['content_score'].max() > 0:
        pets['content_score'] /= pets['content_score'].max()

    if pets['collab_score'].max() > 0:
        pets['collab_score'] /= pets['collab_score'].max()

    # ================= HYBRID SCORE =================
    ALPHA = 0.6  # content
    BETA = 0.4   # collaborative

    pets['final_score'] = (
        ALPHA * pets['content_score'] +
        BETA * pets['collab_score']
    )

    # ================= RESULT =================
    recommendations = pets.sort_values(
        by='final_score',
        ascending=False
    ).head(limit)
    
    cursor.close()
    db.close()
    
    return recommendations[
        ['PetID', 'PetName', 'Type', 'Breed', 'final_score']
    ].to_dict(orient='records')


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({'success': False, 'message': 'No customer_id provided'}))
        sys.exit(1)
    
    customer_id = sys.argv[1]
    limit = int(sys.argv[2]) if len(sys.argv) > 2 else 5
    
    try:
        recommendations = get_recommendations(customer_id, limit)
        print(json.dumps({
            'success': True,
            'recommendations': recommendations
        }))
    except Exception as e:
        print(json.dumps({
            'success': False,
            'message': str(e)
        }))
        sys.exit(1)

