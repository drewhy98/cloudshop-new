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
    Maintain a global MySQL connection for better performance.
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
#   MAIN LOGIN FUNCTION
# -------------------------------------------------------
def main(req: func.HttpRequest) -> func.HttpResponse:
    logging.info("Processing login request.")

    # Parse JSON input
    try:
        body = req.get_json()
    except:
        return func.HttpResponse(
            json.dumps({"error": "Invalid JSON body."}),
            status_code=400,
            mimetype="application/json"
        )

    email = body.get("email")
    password = body.get("password")

    if not email or not password:
        return func.HttpResponse(
            json.dumps({"error": "Please fill in all fields."}),
            status_code=400,
            mimetype="application/json"
        )

    try:
        # Connect to MySQL (reused global connection)
        db = get_db_connection()
        cursor = db.cursor(dictionary=True)

        # Look up user by email
        cursor.execute(
            "SELECT id, name, email, password FROM shopusers WHERE email = %s",
            (email,)
        )
        user = cursor.fetchone()

        if not user:
            return func.HttpResponse(
                json.dumps({"error": "No account found with that email."}),
                status_code=400,
                mimetype="application/json"
            )

        # Verify password with bcrypt
        if not bcrypt.checkpw(password.encode(), user["password"].encode()):
            return func.HttpResponse(
                json.dumps({"error": "Incorrect password."}),
                status_code=400,
                mimetype="application/json"
            )

        # LOGIN SUCCESS
        return func.HttpResponse(
            json.dumps({
                "message": "Success",
                "id": user["id"],
                "name": user["name"],
                "email": user["email"]
            }),
            status_code=200,
            mimetype="application/json"
        )

    except Exception as e:
        logging.error("Login error: " + str(e))
        return func.HttpResponse(
            json.dumps({"error": "Server error."}),
            status_code=500,
            mimetype="application/json"
        )
