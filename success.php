<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Successful - ShopSphere</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Arial, sans-serif;
      background-color: #fafafa;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      margin: 0;
      text-align: center;
    }

    /* Header */
    .header {
      background-color: #ffffff;
      color: #2e5d34;
      width: 100%;
      text-align: center;
      padding: 25px 0;
      border-bottom: 1px solid #e0e0e0;
      position: sticky;
      top: 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .header h1 {
      font-family: 'Georgia', serif;
      margin: 0;
      font-size: 2em;
    }

    .header p {
      font-size: 1em;
      margin-top: 5px;
      color: #2e5d34;
    }

    /* Success box */
    .success-message {
      background: #ffffff;
      padding: 45px;
      border-radius: 10px;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
      max-width: 500px;
      margin-top: 80px;
    }

    .success-icon {
      font-size: 60px;
      color: #2e5d34;
      margin-bottom: 15px;
    }

    h2 {
      color: #2e5d34;
      font-family: 'Georgia', serif;
      margin-bottom: 10px;
    }

    p {
      color: #555;
      font-size: 1.05em;
    }

    /* Buttons */
    .btn {
      display: inline-block;
      background-color: #2e5d34;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 5px;
      margin: 10px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #244928;
    }

    .btn-secondary {
      background-color: #4c8b56;
    }

    .btn-secondary:hover {
      background-color: #3a6c44;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>ShopSphere</h1>
    <p>Registration Successful</p>
  </div>

  <div class="success-message">
    <div class="success-icon">✅</div>
    <h2>Registration Successful!</h2>
    <p>Your account has been created successfully. Welcome to <strong>ShopSphere</strong> — where freshness meets convenience!</p>

    <div style="margin-top: 30px;">
      <a href="index.php" class="btn">Go Back to Home</a>
      <a href="register.php" class="btn btn-secondary">Register Another User</a>
    </div>
  </div>

</body>
</html>
