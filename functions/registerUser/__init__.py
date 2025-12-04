import logging
import azure.functions as func
import json
import mysql.connector
import bcrypt
import os

def main(req: func.HttpRequest) -> func.HttpResponse:
    logging.info("Processing registration request.")

    try:
        body = req.get_json()
    except:
        return func.HttpResponse(
            json.dumps({"error": "Invalid JSON body."}),
            status_code=400,
            mimetype="application/json"
        )

    name = body.get('name')
    email = body.get('email')
    password = body.get('password')

    # Validate inputs
    if not name or not email or not password:
        return func.HttpResponse(
            json.dumps({"error": "Please fill in all fields."}),
            status_code=400,
            mimetype="application/json"
        )

    try:
        # -------------------------------
        #   Connect to Azure MySQL
        # -------------------------------
        db = mysql.connector.connect(
            host=os.getenv("DB_HOST"),
            user=os.getenv("DB_USER"),
            password=os.getenv("DB_PASSWORD"),
            database=os.getenv("DB_NAME"),
            ssl_disabled=False     # Azure MySQL requires SSL
        )

        cursor = db.cursor()

        # Check if email exists
        cursor.execute("SELECT email FROM shopusers WHERE email = %s", (email,))
        result = cursor.fetchone()

        if result:
            return func.HttpResponse(
                json.dumps({"error": "This email is already registered."}),
                status_code=400,
                mimetype="application/json"
            )

        # Hash password
        hashed_password = bcrypt.hashpw(password.encode(), bcrypt.gensalt()).decode()

        # Insert new user
        cursor.execute(
            "INSERT INTO shopusers (name, email, password) VALUES (%s, %s, %s)",
            (name, email, hashed_password)
        )
        db.commit()

        return func.HttpResponse(
            json.dumps({"message": "Success"}),
            status_code=200,
            mimetype="application/json"
        )

    except Exception as e:
        logging.error("Database or server error: " + str(e))
        return func.HttpResponse(
            json.dumps({"error": "Server error."}),
            status_code=500,
            mimetype="application/json"
        )
