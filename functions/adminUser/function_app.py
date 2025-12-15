import azure.functions as func
import json
import hashlib

app = func.FunctionApp()

# -------------------------------------------------------
#   Hardcoded Admin User (SHA-256 hash of "test")
# -------------------------------------------------------
ADMIN_USER = {
    "email": "admin@gmail.com",
    "name": "Admin",
    "password": hashlib.sha256("test".encode()).hexdigest(),
    "id": 1
}

@app.function_name(name="AdminLogin")
@app.route(
    route="admin/login",
    methods=["POST"],
    auth_level=func.AuthLevel.ANONYMOUS
)
def admin_login(req: func.HttpRequest) -> func.HttpResponse:
    try:
        body = req.get_json()
    except ValueError:
        return func.HttpResponse(
            json.dumps({"error": "Invalid JSON body"}),
            status_code=400,
            mimetype="application/json"
        )

    email = body.get("email")
    password = body.get("password")

    if not email or not password:
        return func.HttpResponse(
            json.dumps({"error": "Fill in all fields"}),
            status_code=400,
            mimetype="application/json"
        )

    if email != ADMIN_USER["email"]:
        return func.HttpResponse(
            json.dumps({"error": "No account found"}),
            status_code=400,
            mimetype="application/json"
        )

    hashed_password = hashlib.sha256(password.encode()).hexdigest()

    if hashed_password != ADMIN_USER["password"]:
        return func.HttpResponse(
            json.dumps({"error": "Incorrect password"}),
            status_code=400,
            mimetype="application/json"
        )

    return func.HttpResponse(
        json.dumps({
            "message": "Success",
            "id": ADMIN_USER["id"],
            "name": ADMIN_USER["name"],
            "email": ADMIN_USER["email"]
        }),
        status_code=200,
        mimetype="application/json"
    )
