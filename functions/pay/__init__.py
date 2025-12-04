import logging
import azure.functions as func
import json
import mysql.connector

def main(req: func.HttpRequest) -> func.HttpResponse:
    logging.info("Processing checkout request.")

    # ---------------------------
    # Parse incoming JSON
    # ---------------------------
    try:
        body = req.get_json()
    except:
        return func.HttpResponse(
            json.dumps({"error": "Invalid JSON body."}),
            status_code=400,
            mimetype="application/json"
        )

    user_id = body.get("user_id")
    address = body.get("address")

    if not user_id or not address:
        return func.HttpResponse(
            json.dumps({"error": "Missing user_id or address."}),
            status_code=400,
            mimetype="application/json"
        )

    try:
        # ---------------------------
        # MySQL Connection (HARDCODED)
        # ---------------------------
        db = mysql.connector.connect(
            host="drewhdb.mysql.database.azure.com",
            user="cmet01",
            password="Cardiff01",
            database="shopsphere",
            ssl_disabled=False
        )
        cursor = db.cursor(dictionary=True)

        # ---------------------------------------------------------
        # GET BASKET ITEMS FOR THIS USER
        # ---------------------------------------------------------
        basket_sql = """
            SELECT c.product_id, c.quantity, p.price, p.name
            FROM user_cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = %s
        """
        cursor.execute(basket_sql, (user_id,))
        items = cursor.fetchall()

        if len(items) == 0:
            return func.HttpResponse(
                json.dumps({"error": "Your basket is empty."}),
                status_code=400,
                mimetype="application/json"
            )

        # CALCULATE TOTAL
        total = sum(item["price"] * item["quantity"] for item in items)

        # ---------------------------------------------------------
        # INSERT THE ORDER
        # ---------------------------------------------------------
        order_sql = """
            INSERT INTO orders (user_id, total_amount, address)
            VALUES (%s, %s, %s)
        """
        cursor.execute(order_sql, (user_id, total, address))
        db.commit()

        order_id = cursor.lastrowid

        # ---------------------------------------------------------
        # INSERT ORDER ITEMS
        # ---------------------------------------------------------
        item_sql = """
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (%s, %s, %s, %s)
        """
        for item in items:
            cursor.execute(
                item_sql,
                (order_id, item["product_id"], item["quantity"], item["price"])
            )
        db.commit()

        # ---------------------------------------------------------
        # CLEAR THE USER'S CART
        # ---------------------------------------------------------
        cursor.execute("DELETE FROM user_cart WHERE user_id = %s", (user_id,))
        db.commit()

        # SUCCESS RESPONSE
        return func.HttpResponse(
            json.dumps({
                "message": "Order placed successfully",
                "order_id": order_id,
                "total": total,
                "items": items
            }),
            status_code=200,
            mimetype="application/json"
        )

    except Exception as e:
        logging.error("Checkout error: " + str(e))
        return func.HttpResponse(
            json.dumps({"error": "Server error."}),
            status_code=500,
            mimetype="application/json"
        )
