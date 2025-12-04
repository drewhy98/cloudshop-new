import logging
import azure.functions as func
import json
import mysql.connector
import bcrypt

def main(req: func.HttpRequest) -> func.HttpResponse:
    logging.info("Processing login request.")

    # ---------------------------
    # Parse JSON body
    # ---------------------------
    try:
        body = req.get_json()
    except:
        return func.HttpResponse(
            json.dumps({"error": "Invalid JSON body."}),
            status_code=400,
            mimetype="application/json"
        )

    email = body.get('email')
    password = body.get('password')

    if not email or not password:
        return func.HttpResponse(
            json.dumps({"error": "Please fill in all fields."}),
            status_code=400,
            mimetype="application/json"
        )

    try:
        # ---------------------------
        # DIRECT DATABASE CONNECTION
        # ---------------------------
        db = mysql.connector.connect(
            host="drewhdb.mysql.database.azure.com",
            user="cmet01",
            password="Cardiff01",
            database="shopsphere",
            ssl_disabled=False   # Azure MySQL uses SSL by default
        )

        cursor = db.cursor(dictionary=True)

        # Look up user
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

        # Verify password
        if not bcrypt.checkpw(password.encode(), user["password"].encode()):
            return func.HttpResponse(
                json.dumps({"error": "Incorrect password."}),
                status_code=400,
                mimetype="application/json"
            )

        # SUCCESS
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
