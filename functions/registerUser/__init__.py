import logging
import azure.functions as func
import json
import mysql.connector
import bcrypt
import os

# -------------------------------------------------------
#   PRELOAD DATABASE SETTINGS (reduces cold starts)
# -------------------------------------------------------
DB_CONFIG = {
    "host": os.getenv("DB_HOST"),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASSWORD"),
    "database": os.getenv("DB_NAME"),
}

db_connection = None


def get_db_connection():
    """
    Maintain a global MySQL connection to reduce cold starts
    and avoid reconnecting for every incoming HTTP request.
    """
    global db_connection

    try:
        if db_connection is None or not db_connection.is_connected():
            db_connection = mysql.connector.connect(
                **DB_CONFIG,
                ssl_disabled=False  # Azure MySQL requires SSL
            )
        return db_connection
    except Exception as e:
        logging.error(f"Failed to connect to DB: {e}")
        raise


# -------------------------------------------------------
#   MAIN REGISTRATION FUNCTION
# -------------------------------------------------------
def main(req: func.HttpRequest) -> func.HttpResponse:
    logging.info("Processing registration request.")

    # Parse incoming JSON
    try:
        body = req.get_json()
    except Exception:
        return func.HttpResponse(
            json.dumps({"error": "Invalid JSON body."}),
            status_code=400,
            mimetype="application/json"
        )

    name = body.get("name")
    email = body.get("email")
    password = body.get("password")

    # Validate input
    if not name or not email or not password:
        return func.HttpResponse(
            json.dumps({"error": "Please fill in all fields."}),
            status_code=400,
            mimetype="application/json"
        )

    try:
        # -------------------------------------------------------
        #   Connect to MySQL (reused connection)
        # -------------------------------------------------------
        db = get_db_connection()
        cursor = db.cursor()

        # Check if email already exists
        cursor.execute(
            "SELECT email FROM shopusers WHERE email = %s",
            (email,)
        )
        result = cursor.fetchone()

        if result:
            return func.HttpResponse(
                json.dumps({"error": "This email is already registered."}),
                status_code=400,
                mimetype="application/json"
            )

        # Hash password
        hashed_pw = bcrypt.hashpw(password.encode(), bcrypt.gensalt()).decode()

        # Insert user into MySQL
        cursor.execute(
            "INSERT INTO shopusers (name, email, password) VALUES (%s, %s, %s)",
            (name, email, hashed_pw)
        )
        db.commit()

        return func.HttpResponse(
            json.dumps({"message": "Success"}),
            status_code=200,
            mimetype="application/json"
        )

    except Exception as e:
        logging.error(f"Server error: {e}")
        return func.HttpResponse(
            json.dumps({"error": "Server error."}),
            status_code=500,
            mimetype="application/json"
        )
