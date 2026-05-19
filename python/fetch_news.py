import requests
from datetime import datetime, timezone
import mysql.connector
import torch
from transformers import AutoTokenizer, AutoModelForSequenceClassification

# ============================================================
# DB CONFIG
# ============================================================
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "appaan_db"
}

def get_db_connection():
    return mysql.connector.connect(**db_config)

# ============================================================
# API CONFIG (PAKAI API KEY ASLI)
# ============================================================
NEWSDATA_API = "pub_4825cce8240c48aa96760715bfa82460"
ND_BASE = "https://newsdata.io/api/1/news"
MAX_PAGES = 3

BASE_QUERY = "crypto"

# ============================================================
# NLP (FinBERT)
# ============================================================
print("Loading FinBERT model...")

tokenizer = AutoTokenizer.from_pretrained("ProsusAI/finbert")
model = AutoModelForSequenceClassification.from_pretrained("ProsusAI/finbert")
labels = ["negative", "neutral", "positive"]

def analyze_sentiment(text):
    try:
        inputs = tokenizer(text, return_tensors="pt", truncation=True, padding=True)
        outputs = model(**inputs)
        probs = torch.nn.functional.softmax(outputs.logits, dim=-1)
        idx = torch.argmax(probs).item()
        return labels[idx], float(probs[0][idx].detach())
    except Exception:
        return "neutral", 0.0

# ============================================================
# HELPERS
# ============================================================
def parse_dt(s):
    if not s:
        return datetime.now(timezone.utc)

    try:
        return datetime.strptime(s, "%Y-%m-%dT%H:%M:%SZ").replace(tzinfo=timezone.utc)
    except:
        return datetime.now(timezone.utc)

def get_coin_keywords():
    conn = get_db_connection()
    cur = conn.cursor(dictionary=True)

    cur.execute("SELECT symbol, keywords FROM coins")
    rows = cur.fetchall()

    cur.close()
    conn.close()

    mapping = {}

    for row in rows:
        words = row["keywords"].lower().split(",")
        mapping[row["symbol"]] = words

    return mapping

COIN_KEYWORDS = get_coin_keywords()

def detect_coin(title, description=""):
    text = (title + " " + description).lower()

    for symbol, words in COIN_KEYWORDS.items():
        for w in words:
            w = w.strip()
            if not w:
                continue

            # 2 mode detection
            if w in text:
                return symbol

    return None

# ============================================================
# SAVE ARTICLE
# ============================================================
def save_article(article):
    try:
        conn = get_db_connection()
        cur = conn.cursor()

        cur.execute("SELECT id FROM articles WHERE url=%s", (article["url"],))
        exists = cur.fetchone()

        if not exists:
            cur.execute("""
                INSERT INTO articles
                (title, url, source, thumbnail, published_at,
                 sentiment, sentiment_score, coin_symbol, inserted_at)
                VALUES (%s,%s,%s,%s,%s,%s,%s,%s,NOW())
            """, (
                article["title"],
                article["url"],
                article["source"],
                article["thumbnail"],
                article["published_at"].strftime("%Y-%m-%d %H:%M:%S"),
                article["sentiment"],
                article["sentiment_score"],
                article["coin"]
            ))
            conn.commit()
            print("Saved:", article["title"][:70])

        cur.close()
        conn.close()

    except Exception as e:
        print("DB ERROR:", e)

# ============================================================

# MAIN PIPELINE

# ============================================================

def run_pipeline():
    print("Fetching news from API...")

next_page = None

for _ in range(MAX_PAGES):

    params = {
        "apikey": NEWSDATA_API,
        "q": BASE_QUERY,
        "language": "en"
    }

    if next_page:
        params["page"] = next_page

    try:
        r = requests.get(ND_BASE, params=params, timeout=10)
        data = r.json()
    except Exception as e:
        print("API ERROR:", e)
        break

    # VALIDASI RESPONSE
    if "results" not in data:
        print("API response invalid:", data)
        break

    results = data["results"]

    if not isinstance(results, list):
        print("Unexpected API format:", data)
        break

    next_page = data.get("nextPage")

    for a in results:

        if not isinstance(a, dict):
            continue

        title = a.get("title")
        if not title:
            continue

        desc = a.get("description") or ""
        link = a.get("link") or "#"
        source = a.get("source_id", "unknown")
        image = a.get("image_url") or ""
        pub = a.get("pubDate")

        COIN_KEYWORDS = get_coin_keywords()
        coin = detect_coin(title, desc)

        if not coin:
            print("SKIP (no coin):", title[:60])
            continue
        else:
            print("DETECTED:", coin, "|", title[:60])

        sentiment, score = analyze_sentiment(title + " " + desc)

        article = {
            "title": title,
            "url": link,
            "source": source,
            "thumbnail": image,
            "published_at": parse_dt(pub),
            "sentiment": sentiment,
            "sentiment_score": round(score, 3),
            "coin": coin
        }

        save_article(article)

    if not next_page:
        break

# ============================================
# UPDATE SENTIMENT SUMMARY (DI LUAR LOOP)
# ============================================
try:
    conn = get_db_connection()
    cur = conn.cursor()

    cur.execute("""
    INSERT INTO sentiment_summary (coin_symbol, date, positive, negative, neutral, dominant)
    SELECT 
        coin_symbol,
        DATE(published_at),
        SUM(sentiment='positive'),
        SUM(sentiment='negative'),
        SUM(sentiment='neutral'),
        CASE 
            WHEN SUM(sentiment='positive') >= GREATEST(SUM(sentiment='negative'), SUM(sentiment='neutral')) THEN 'positive'
            WHEN SUM(sentiment='negative') >= GREATEST(SUM(sentiment='positive'), SUM(sentiment='neutral')) THEN 'negative'
            ELSE 'neutral'
        END
    FROM articles
    GROUP BY coin_symbol, DATE(published_at)
    ON DUPLICATE KEY UPDATE
        positive = VALUES(positive),
        negative = VALUES(negative),
        neutral = VALUES(neutral),
        dominant = VALUES(dominant);
    """)

    conn.commit()
    cur.close()
    conn.close()

    print("Sentiment summary updated.")

except Exception as e:
    print("SUMMARY ERROR:", e)

print("Pipeline finished.")

# ============================================================
# ENTRY POINT
# ============================================================
if __name__ == "__main__":
    run_pipeline()