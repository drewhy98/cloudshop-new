<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In - ShopSphere</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Arial, sans-serif;
      background-color: #fafafa;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      flex-direction: column;
    }

    /* Header */
    .header {
      background-color: #ffffff;
      color: #2e5d34;
      padding: 25px 0;
      text-align: center;
      position: fixed;
      top: 0;
      width: 100%;
      border-bottom: 1px solid #e0e0e0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .header h1 {
      font-family: 'Georgia', serif;
      margin: 0;
      font-size: 2em;
    }

    .header p {
      color: #2e5d34;
      margin-top: 5px;
    }

    /* Form */
    .login-form {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 400px;
      margin-top: 100px;
    }

    h2 {
      color: #2e5d34;
      font-family: 'Georgia', serif;
      margin-bottom: 20px;
      text-align: center;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #2e5d34;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }

    .btn {
      background-color: #2e5d34;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #244928;
    }

    .error {
      color: red;
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      border-radius: 5px;
      padding: 10px;
      text-align: center;
      margin-bottom: 15px;
    }

    .links {
      text-align: center;
      margin-top: 20px;
    }

    .links a {
      color: #2e5d34;
      text-decoration: none;
      font-weight: bold;
    }

    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>ShopSphere</h1>
    <p>Welcome Back</p>
  </div>

  <div class="login-form">
    <h2>Log In</h2>

    <?php if (isset($_GET['error'])): ?>
      <div class="error">
        <?php echo htmlspecialchars($_GET['error']); ?>
      </div>
    <?php endif; ?>

    <form method="post" action="process_login.php">
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label>Password *</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>

      <input type="submit" class="btn" value="Log In">
    </form>

    <div class="links">
      <p>Don’t have an account? <a href="register.php">Register here</a></p>
      <p>Are you an Admin? <a href="admin_login.php">Log in here</a></p>
      <p><a href="index.php">Back to Home</a></p>
    </div>
  </div>

</body>
</html>
