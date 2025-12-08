import logging
import azure.functions as func
import json
import mysql.connector
import bcrypt
import os

app = func.FunctionApp()

# -------------------------------------------------------
#   PRELOAD DATABASE SETTINGS
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
    Maintain a global MySQL connection for performance.
    """
    global db_connection

    if db_connection is None or not db_connection.is_connected():
        db_connection = mysql.connector.connect(
            **DB_CONFIG,
            ssl_disabled=False
        )

    return db_connection


# -------------------------------------------------------
#   LOGIN FUNCTION (Python v2 decorator model)
# -------------------------------------------------------
@app.function_name(name="Login")
@app.route(route="login", methods=["POST"], auth_level=func.AuthLevel.ANONYMOUS)
def login(req: func.HttpRequest) -> func.HttpResponse:
    logging.info("Processing login request.")

    # Parse JSON body
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
        db = get_db_connection()
        cursor = db.cursor(dictionary=True)

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

        if not bcrypt.checkpw(password.encode(), user["password"].encode()):
            return func.HttpResponse(
                json.dumps({"error": "Incorrect password."}),
                status_code=400,
                mimetype="application/json"
            )

        # Success response
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
